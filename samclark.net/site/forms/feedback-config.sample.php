<?php
/* ============================================================================
 * feedback-config.sample.php  —  SMTP credentials for the feedback form
 * ----------------------------------------------------------------------------
 * SETUP (do this once, on the server, via SSH/SFTP):
 *
 *   1. Copy this file to your HOME directory (ABOVE the web root) and rename
 *      it to remove ".sample":
 *          cp feedback-config.sample.php ~/feedback-config.php
 *      So it ends up at, e.g.,  /home/YOURUSER/feedback-config.php
 *      Keeping it above the web root means it can never be served over HTTP.
 *
 *   2. Edit ~/feedback-config.php and set FEEDBACK_SMTP_PASS to the real
 *      password for the feedback@samclark.net mailbox.
 *
 *   3. Lock down its permissions so only you can read it:
 *          chmod 600 ~/feedback-config.php
 *
 *   4. In feedback.php, confirm CONFIG_PATH points at this file (it defaults
 *      to one directory above the site root — adjust if your layout differs).
 *
 * SECURITY: This file holds a password. Never put it inside the web root,
 * never commit it to git, and never share it. The form's feedback.php reads
 * from it at runtime; the password is never present in any web-served file.
 * ========================================================================== */

// The SMTP login password for the FROM mailbox (feedback@samclark.net).
// Replace the placeholder below with the real mailbox password.
define('FEEDBACK_SMTP_PASS', 'CHANGE-ME-TO-THE-REAL-PASSWORD');

// --- The settings below normally need no changes for DreamHost ---

// SMTP login username — must be the full email address of the FROM mailbox.
define('FEEDBACK_SMTP_USER', 'feedback@samclark.net');

// DreamHost outgoing mail server.
define('FEEDBACK_SMTP_HOST', 'smtp.dreamhost.com');

// Port + encryption. These two MUST match each other:
//   465 with 'smtps' (implicit TLS)   <-- default, as requested
//   587 with 'tls'   (STARTTLS)       <-- DreamHost's documented alternative
// If 465 ever fails to connect, switch BOTH lines to 587 / 'tls'.
define('FEEDBACK_SMTP_PORT', 465);
define('FEEDBACK_SMTP_SECURE', 'smtps');

// Absolute path to the PHPMailer src/ directory on the server.
// After installing PHPMailer (see feedback.php header), set this to the folder
// that contains PHPMailer.php, SMTP.php, and Exception.php.
define('FEEDBACK_PHPMAILER_SRC', '/home/YOURUSER/PHPMailer/src');
