<?php
/* ============================================================================
 * feedback.php  —  samclark.net feedback handler (SMTP via PHPMailer)
 * ----------------------------------------------------------------------------
 * Receives a POST from /site/forms/feedback.shtml and emails it to Sam using
 * authenticated SMTP (smtp.dreamhost.com). Authenticated SMTP is used because
 * DreamHost silently drops unauthenticated mail() sends from the web server.
 *
 * The recipient address lives ONLY in this file, on the server. It is never
 * sent to the browser, so visitors cannot see it in page source. The SMTP
 * PASSWORD lives in a separate config file OUTSIDE the web root (see below);
 * it is never present in any web-served file.
 *
 * ----------------------------------------------------------------------------
 * ONE-TIME SERVER SETUP
 * ----------------------------------------------------------------------------
 *   A) Install PHPMailer (no Composer needed):
 *        - Download the latest release from:
 *            https://github.com/PHPMailer/PHPMailer/releases
 *        - Unzip it and place the src/ folder somewhere ABOVE the web root,
 *          e.g.  /home/YOURUSER/PHPMailer/src/
 *          (It must contain PHPMailer.php, SMTP.php, Exception.php.)
 *
 *   B) Create the credentials file (also ABOVE the web root):
 *        cp site/forms/feedback-config.sample.php ~/feedback-config.php
 *        # edit ~/feedback-config.php: set the real mailbox password
 *        chmod 600 ~/feedback-config.php
 *
 *   C) Check CONFIG_PATH and the PHPMailer path below match your server.
 *
 * Bot defenses (all enforced here, server-side — client checks are never
 * trusted): 1) POST only  2) honeypot "website" empty  3) min time on page
 * 4) message length bounds.
 *
 * Security: NO user-supplied data is ever placed in an email header. The
 * visitor's optional email is validated and newline-stripped before it is used
 * as a Reply-To, which prevents mail-header-injection.
 * ========================================================================== */

/* ------------------------------------------------------------------ config */

// Where feedback is delivered. SERVER-SIDE ONLY — never echoed to the client.
const RECIPIENT   = 'feedback@samclark.net';

// The From address. On DreamHost the SMTP login user and the From address must
// be the same mailbox on the sending domain.
const FROM_ADDR   = 'feedback@samclark.net';
const FROM_NAME   = 'samclark.net feedback';

const MIN_SECONDS = 3;      // reject submissions faster than this (bots)
const MAX_SECONDS = 86400;  // 24 h — stale token, make them reload
const MIN_CHARS   = 2;
const MAX_CHARS   = 8000;

// Path to the credentials file, OUTSIDE the web root. By default this looks
// one level above the site root. If feedback.php lives at
//   <siteroot>/site/forms/feedback.php
// then four levels up from __DIR__ is the parent of the site root. Adjust if
// your layout differs; the file just needs to be somewhere PHP can read but
// the web server will not serve.
define('CONFIG_PATH', dirname(__DIR__, 3) . '/feedback-config.php');

/* --------------------------------------------------------------- helpers */

function respond($ok, $error = null, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    $out = array('ok' => (bool) $ok);
    if ($error !== null) {
        $out['error'] = $error;
    }
    echo json_encode($out);
    exit;
}

// Remove anything that could break out of a header into a new one.
function strip_headers($s) {
    return trim(str_replace(array("\r", "\n", "%0a", "%0d", "%0A", "%0D"), '', $s));
}

/* ----------------------------------------------------------------- guards */

// 1. POST only.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Method not allowed.', 405);
}

// 2. Honeypot: real users never see or fill this field.
if (!empty($_POST['website'])) {
    // Pretend success so the bot learns nothing.
    respond(true);
}

// 3. Timing check.
$loaded = isset($_POST['loaded_at']) ? (int) $_POST['loaded_at'] : 0;
if ($loaded > 0) {
    // loaded_at is JS Date.now() in milliseconds.
    $elapsed = (time() * 1000 - $loaded) / 1000.0;
    if ($elapsed < MIN_SECONDS) {
        respond(true); // too fast → almost certainly a bot; fake success
    }
    if ($elapsed > MAX_SECONDS) {
        respond(false, 'This form expired. Please reload the page and try again.');
    }
} else {
    // No timestamp at all → a scripted POST that skipped the page. Reject.
    respond(true);
}

/* ----------------------------------------------------------------- input */

$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$email   = isset($_POST['email'])   ? trim($_POST['email'])   : '';

$len = function_exists('mb_strlen') ? mb_strlen($message) : strlen($message);
if ($len < MIN_CHARS) {
    respond(false, 'Please enter a message.');
}
if ($len > MAX_CHARS) {
    respond(false, 'That message is too long. Please shorten it.');
}

// Validate the optional reply address. If it is missing or invalid we simply
// do not set Reply-To — we never reject a real message over a bad email.
$reply_to = '';
if ($email !== '') {
    $clean = strip_headers($email);
    if (filter_var($clean, FILTER_VALIDATE_EMAIL)) {
        $reply_to = $clean;
    }
}

/* ------------------------------------------------------------------ build */

$subject = 'samclark.net feedback';

// Body. User content goes ONLY in the body, never in a header.
$body  = "New feedback from samclark.net\n";
$body .= "----------------------------------------\n\n";
$body .= $message . "\n\n";
$body .= "----------------------------------------\n";
$body .= 'Reply-to address given: ' . ($reply_to !== '' ? $reply_to : '(none)') . "\n";
$body .= 'Received: ' . date('Y-m-d H:i:s T') . "\n";
if (!empty($_SERVER['REMOTE_ADDR'])) {
    $body .= 'Sender IP: ' . $_SERVER['REMOTE_ADDR'] . "\n";
}

/* ------------------------------------------------------------- send (SMTP) */

// Load credentials from outside the web root.
if (!is_readable(CONFIG_PATH)) {
    error_log('feedback.php: config file not readable at ' . CONFIG_PATH);
    respond(false, 'The message could not be sent right now. Please try again later.');
}
require CONFIG_PATH;

// Load PHPMailer (path comes from the config file).
$src = defined('FEEDBACK_PHPMAILER_SRC') ? rtrim(FEEDBACK_PHPMAILER_SRC, '/') : '';
if ($src === '' || !is_readable($src . '/PHPMailer.php')) {
    error_log('feedback.php: PHPMailer not found at ' . $src);
    respond(false, 'The message could not be sent right now. Please try again later.');
}
require $src . '/Exception.php';
require $src . '/PHPMailer.php';
require $src . '/SMTP.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true); // true = throw exceptions

try {
    $mail->isSMTP();
    $mail->Host       = FEEDBACK_SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = FEEDBACK_SMTP_USER;       // feedback@samclark.net
    $mail->Password   = FEEDBACK_SMTP_PASS;       // from the external config
    $mail->SMTPSecure = FEEDBACK_SMTP_SECURE;     // 'smtps' for 465, 'tls' for 587
    $mail->Port       = (int) FEEDBACK_SMTP_PORT; // 465 (default) or 587
    $mail->CharSet    = 'UTF-8';
    $mail->Timeout    = 15;

    // From + recipient. From MUST equal the SMTP login mailbox.
    $mail->setFrom(FROM_ADDR, FROM_NAME);
    $mail->addAddress(RECIPIENT);

    // Visitor's optional, validated address as Reply-To (never in a raw header).
    if ($reply_to !== '') {
        $mail->addReplyTo($reply_to);
    }

    $mail->Subject = $subject;
    $mail->Body    = $body;          // plain text
    $mail->isHTML(false);
    $mail->XMailer = 'samclark.net-feedback';

    $mail->send();
    respond(true);

} catch (\Throwable $e) {
    // Log the real reason server-side; never leak details to the browser.
    error_log('feedback.php SMTP error: ' . $e->getMessage());
    respond(false, 'The message could not be sent right now. Please try again later.');
}
