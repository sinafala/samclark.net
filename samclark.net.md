# samclark.net — Site Guide & House Style

This is the single reference for how [samclark.net](https://samclark.net) is
built: its structure, conventions, the browser quirks it works around, and the
patterns to reuse (or avoid) when editing it. It is a working reference for
future-Sam and anyone else touching the site.

It supersedes the two earlier documents it was merged from — `samclark.md`
(the broader style guide) and `house-style.md` (the page-level house style).
Delete those once this is in place.

The de-facto source of truth remains the code itself: the two page templates
(`site/pages/TEMPLATE.shtml`, `site/projects/project-template.shtml`), the three
SSI includes, and the header comment at the top of `site/css/sam-styles.css`.
This document lives at the repository root and the site files sit alongside it:
`index.shtml`, `.htaccess`, `sitemap.xml` at the root, and everything else under
[`site/`](site/).

---

## 1. Overview

**samclark.net** is a hand-authored static site served by Apache with Server
Side Includes (SSI) enabled. There is no build step, no framework, and no
dependency manager. Every page is a `.shtml` file that includes a few shared SSI
fragments (head, navbar, footer) and links to a single shared CSS file.

**Stack at a glance**

- Apache (`mod_include` for SSI, `mod_headers` for cache control)
- Plain HTML5 / CSS3 / vanilla JavaScript
- PHP only for the feedback form handler (`site/forms/feedback.php`)
- Font Awesome 4.7 (CDN) for icons
- MathJax 3 (CDN) for math typesetting
- Google Analytics tag (gtag.js)

**Editing model**: edit files in place, push to the repo, and the production
server picks them up. There is no build, compile, or deploy pipeline beyond
`git push`. (For packaging a set of edits into a self-installing bundle, see the
`samclark-site-deploy` skill — section 23.)

---

## 2. Directory layout

```
samclark.net/
├── .htaccess                       Apache: redirects + cache headers
├── index.shtml                     Home page
├── robots.txt
├── sitemap.xml
├── samclark.net.md                 THIS document
└── site/
    ├── css/
    │   └── sam-styles.css          THE stylesheet (section 7)
    ├── includes/                   SSI fragments (section 5)
    │   ├── header.html             <head> contents
    │   ├── nav-links-top.shtml     Top navbar
    │   └── bottom.shtml            Footer
    ├── pages/                      Standard content pages
    │   ├── TEMPLATE.shtml          Skeleton for new pages
    │   ├── biography.shtml
    │   ├── cv.shtml
    │   ├── bibliography.shtml
    │   └── ...
    ├── projects/                   Project / research pages
    │   └── project-template.shtml  Skeleton for a project page
    ├── news-pages/                 Long-form research previews
    │   └── flow-field.shtml        Reference example
    ├── tutorials/                  Interactive / self-contained tutorials
    │   └── svd-demography.shtml    Reference example (MathJax + canvas)
    ├── forms/                      User-facing forms
    │   ├── feedback.shtml          Reference example
    │   ├── feedback.php            Server-side handler
    │   └── feedback-config.sample.php
    ├── search/                     Search page
    ├── media/
    │   ├── thumbs/                 Thumbnail-size images
    │   ├── fullsize/               Full-resolution images
    │   └── favicons/
    ├── cv/                         PDFs of CV + dissertation
    ├── talks/                      Talk PDFs/PPTXs
    └── ...
```

**When adding a new page**, pick the directory that matches its purpose:
general content in `pages/`, research projects in `projects/`, long-form
previews in `news-pages/`, interactive tutorials in `tutorials/`, forms in
`forms/`.

---

## 3. The page skeleton

Every page follows this exact structure (this is `site/pages/TEMPLATE.shtml`,
annotated):

```html
<!DOCTYPE html>

<html lang="en">

<head>
  <meta name="author" content="Samuel J. Clark">
  <meta name="description" content="…one-sentence page description…">
  <meta name="keywords" content="…comma, separated, keywords…">
  <meta property="og:title" content="…short title…">
  <meta property="og:description" content="…short description…">
  <title>Samuel Clark - Page Title</title>
  <!--#include virtual="/site/includes/header.html" -->
</head>

<body>

<div class="container">

  <header>
    <!--#include virtual="/site/includes/nav-links-top.shtml" -->
    <div class="sam-title">Page Title</div>
  </header>

  <main>

    <div class="sam-block">
      … page content …
    </div>

  </main>

  <footer><!--#include virtual="/site/includes/bottom.shtml" --></footer>

</div>
</body>
</html>
```

Notes:

- The blank lines after `<!DOCTYPE html>`, after `<html …>`, and around `<body>`
  are the site's habit — harmless, kept for consistency with existing files.
- `.container` is a full-height flex column: `header` and `footer` are fixed,
  `main` scrolls internally. Never put page content outside `main`.
- Put essentially all body content inside a single `<div class="sam-block">`
  (the 575&nbsp;px content column). Long-form pages may use other `sam-*`
  containers, but `sam-block` is the default.

---

## 4. `<head>` conventions

Per-page head contents, in this order:

1. `<meta name="author" content="Samuel J. Clark">`
2. `<meta name="description" …>` — one sentence (browser tabs, search snippets).
3. `<meta name="keywords" …>` — usually leads with `Samuel J. Clark, Samuel
   Clark, Sam Clark, …`, then page-specific terms.
4. `<meta property="og:title" …>` and `<meta property="og:description" …>`.
5. `<title>` — format **`Samuel Clark - <Page Title>`** (the homepage uses the
   longer `Samuel J. Clark - Demographer`).
6. `<!--#include virtual="/site/includes/header.html" -->` — **always last** in
   `<head>`.

`header.html` already provides, site-wide: Google Analytics, the viewport tag,
Twitter-card + Open Graph defaults (`og:site_name`, `og:type=website`,
`og:image`, `og:locale`), the stylesheet `<link>` (cache-busted `?ver=NN`),
MathJax, the favicon set, Font Awesome, and the Safari bfcache repaint fix
(section 17). **Do not repeat any of these in the page head**, and **do not add
a page-specific viewport `<meta>`** — two of them clash.

Page-specific SEO that `header.html` does *not* provide may be added **before**
the header include, and is welcome where it helps:

- `<link rel="canonical" href="https://samclark.net/…">` — point it at the
  page's real, final public URL (a short `.htaccess` alias is fine, e.g.
  `/svd-tutorial`).
- A `<script type="application/ld+json">` JSON-LD block (e.g. `ScholarlyArticle`).
- `citation_*` meta for Google Scholar.

Avoid re-declaring `og:type` / `og:site_name` / `twitter:card` in the page head —
`header.html` owns those, and duplicate `og:*` tags with different values are
ambiguous to scrapers.

---

## 5. The three SSI includes

Every page is composed via three SSI directives that live in
`/site/includes/`. The canonical skeleton in section 3 shows where they go.

| Include | Role |
| --- | --- |
| `header.html` | Everything inside `<head>` except the per-page meta/title in section 4: OG/Twitter defaults, the CSS `<link>` with the `?ver=NN` cache buster, MathJax, favicons, Font Awesome, the GA tag, and the `pageshow` Safari-bfcache listener (section 17). |
| `nav-links-top.shtml` | The navy navbar: home-avatar link on the left + four dropdown menus (Academic / VA / Personal / News+). First child of `<header>`. See section 13. |
| `bottom.shtml` | Footer: copyright + `Updated YYYY-MM-DD` line, and the "Send Feedback" button. Sole child of `<footer>`. See section 14. |

Two maintenance points live in these files, not in pages:

- The stylesheet cache-buster `?ver=NN` is in `header.html`. **Bump it whenever
  `sam-styles.css` changes** (section 18).
- The footer `Updated YYYY-MM-DD` date is in `bottom.shtml`, between two
  `<!-- UPDATE THIS -->` markers.

The footer also hides the feedback button on the feedback page itself via an SSI
flag (`hide_feedback_btn`); see section 14.

**Anti-pattern**: do NOT create "self-contained" pages that inline the
nav/footer/styles. That was tried briefly with the feedback form and ripped out
immediately — it means every header/footer change has to be re-applied by hand,
and styles drift. (The narrow, sanctioned exception for a page-scoped
`<style>` block — interactive tutorials — is section 12; it still uses the SSI
shell.)

---

## 6. Routing and SEO (`.htaccess`, `robots.txt`, `sitemap.xml`)

The site uses Apache `Redirect` / `RedirectMatch` rules to expose short,
memorable URLs that point at canonical file paths. For example:

| Short URL       | Canonical                                       |
|-----------------|-------------------------------------------------|
| `/cv`           | `/site/cv/Samuel-Clark_CV.pdf`                  |
| `/cvs`          | `/site/pages/cv.shtml`                          |
| `/pubs`         | `/site/pages/bibliography.shtml`                |
| `/mtb`          | `/site/pages/mtb.shtml`                         |
| `/va`, `/openva`| `/site/projects/verbal-autopsy.shtml`           |
| `/svdcomp`      | `/site/projects/methods.shtml#svd-comp`         |
| `/svd-tutorial` | `/site/tutorials/svd-demography.shtml`          |

- **When you rename or move a page, leave a `Redirect` in `.htaccess`** so
  external links, bookmarks, and any existing `canonical` keep working. The
  "LEGACY URL FIXES" and "LEGACY PREFIX REDIRECTS" sections of `.htaccess` are
  full of examples. Anchored shortcuts (`…/news.shtml#2024-07-04`) live here too.
- A 301 (permanent) is appropriate for a URL meant to be canonical; plain
  `Redirect` (302) is fine for a convenience shortcut.
- **`robots.txt`** allows everything except non-public areas (`/site/clark/`,
  `/site/includes/`, `/site/search/`), the two template files, and the feedback
  form/handler. It points at the sitemap.
- **`sitemap.xml`** is **curated by hand** (not auto-generated). Add new public
  pages — including long-form previews and tutorials — with `lastmod` /
  `changefreq` / `priority`.

---

## 7. CSS — `site/css/sam-styles.css`

There is exactly **one** stylesheet for the whole site. Read its top-of-file
comment block — it lists all 19 numbered sections and the conventions in detail.

### Design tokens (CSS section 1)

All re-tunable values live in a single `:root` block:

```css
:root {
  --desktop-zoom:    1.7;    /* desktop zoom factor (section 9) */
  --mobile-edge-pad: 12px;   /* L/R padding on phones */
  --navbar-height:   30px;   /* navbar + home-avatar size */

  --bg:       #ffffff;
  --tile-bg:  #f2f2f2;
  --fg:       #111;
  --muted:    #555;
  --gap:      10px;
  --radius:   8px;
}
```

**Rule**: when a value might be retuned (sizes, palette, breakpoints), add it
here as a token rather than hard-coding it in a rule. The navbar height already
does this — `.navbar { height: var(--navbar-height) }` and the `.navbar-home img`
dimensions both flow from one knob.

### Naming conventions

| Pattern              | Used for                                                  |
|----------------------|-----------------------------------------------------------|
| `.sam-*`             | site-wide content blocks (`sam-block`, `sam-title`, …)    |
| `<feature>-*`        | feature-scoped classes (`feedback-*`, `navbar-*`)         |
| `.row-* / .column-*` | two-column flex layouts                                   |
| `.column-left-*` etc | left/right halves of a two-column row                     |

Give each new component a class prefix and keep all its rules together in one
section (see NEWS-PAGES and FORMS-PAGES in the CSS for examples).

### Color palette

Use literals from this palette, not arbitrary hex values:

| Hex / name | Used for                                       |
|------------|------------------------------------------------|
| `#0000A0`  | Navy — links, navbar, primary buttons, rules   |
| `green`    | Hover / success state, date accents            |
| `red`      | Active link flash, error state                 |
| `#f2f2f2`  | Light grey — gallery tile / callout background |
| `#555`     | Muted secondary text (notes, captions)         |
| `#fff5d6`  | Faint cream — print-style highlight wash       |
| `#eaeaff`  | Pale blue — `:target` highlight, table row     |

### Where to add new CSS

| What you're adding                       | Goes in CSS section      |
|------------------------------------------|--------------------------|
| New token / tunable value                | 1. DESIGN TOKENS         |
| New global element default               | 3. BASE / RESET          |
| New heading / text style                 | 5. TYPOGRAPHY            |
| New content container (`.sam-something`) | 7. CONTENT CONTAINERS    |
| New small fine-print variant             | 8. NOTES & DISCLAIMERS   |
| New nav button / footer tweak            | 12. NAVBAR / 13. FOOTER  |
| New image class                          | 16. IMAGES & MEDIA       |
| Style for a research-preview / tutorial  | 18. NEWS-PAGES           |
| Style for a new form                     | 19. FORMS-PAGES          |

There is **one CSS file by design** — do not add a second stylesheet. When a
page needs styles that don't fit an existing class, prefer adding a new class to
the right section over inlining a `<style>` block (but see section 12 for the
narrow exception). Bump `?ver=NN` after any CSS edit (section 18).

---

## 8. Mobile / desktop scaling strategy

Pages are designed for a **~575&nbsp;px content column at 12–15&nbsp;px type** —
that is the mobile design. On viewports `>= 780px` the entire body is scaled up
via the non-standard `zoom` property:

```css
@media (min-width: 780px) {
  body { zoom: var(--desktop-zoom); }
}
```

`zoom` is non-standard but supported in WebKit/Blink and Firefox 126+, which
covers everything we care about. The advantage: design once at the small size,
then scale, instead of writing separate stylesheets per breakpoint. **Do not add
per-page desktop font-size or width hacks — let the zoom do it.**

**Mobile polish** lives in one block in CSS section 2:

- Force `font-size: 16px` on all text-entry controls so iOS Safari does NOT
  auto-zoom on field focus (section 17).
- Add `--mobile-edge-pad` (12&nbsp;px) to every primary content container so
  text doesn't hug the screen edges. **When you add a new content-block class**
  that should honor this, add it to the selector list in the
  `@media (max-width: 779px)` block.

---

## 9. Titles, headings, and links

- **Page title:** `<div class="sam-title">…</div>` — large, centered, in
  `<header>` after the navbar include. Use `sam-title-short` when it sits under a
  tight navbar.
- **Byline (long-form pages):** `<div class="sam-byline">Samuel J. Clark</div>`
  directly under the title.
- **In-page section headings:** use `<h1>` for section headings *inside*
  `sam-block` (site convention — see `methods.shtml` and `flow-field.shtml`).
  Visual hierarchy comes from the stylesheet, not from jumping heading levels:
  - `h1` — section heading (15&nbsp;px).
  - `h2` — dense sub-headings / dated items (the homepage uses `<h2>2026-04</h2>`
    for update entries; 12&nbsp;px).
  - `h3` — italic sub-sub-heading (13&nbsp;px).
  - Special variants: `h1.sam-date` (green date, news), `h2.bib-year` /
    `h3.bib` (bibliography).
- **Links:** internal site links use `target="_parent"`; external links use
  `target="_blank"`. **Do not hard-code link colors inline in normal prose** —
  let the global anchor styles apply (navy link, green hover, red active). Inline
  color is acceptable only inside a self-contained interactive page that carries
  its own palette (section 12).

---

## 10. Content containers and components

All defined in `sam-styles.css`; the common ones:

- `div.sam-block` — default 575&nbsp;px content column. Variants:
  `sam-bold-block`, `sam-block-padded`, `sam-centered-block`, `sam-block-index`.
- `div.sam-image` — captioned-image wrapper used inside `sam-block`; pair with an
  `img.mtb` (block, centered, full-width responsive) and bold `Figure N.` caption
  text.
- `div.sam-links-list` / `-short`, `div.sam-link-top` / `-bottom` — link strips.
- `div.sam-note*` — small grey fine-print (`sam-note`, `-left`, `-top`,
  `-bottom`).
- Two-column rows: `.row-title`, `.row-index`, `.row-people` with their matching
  `.column-*-*` children.
- Images: `img.mtb` (wide figures), `img.sam-index` (right-float home thumb),
  `img.sam-plain`, `img.sam-logo`, `img.sam-people`, `img.badge`.
- Media paths: thumbnails in `/site/media/thumbs/`, full-size in
  `/site/media/fullsize/`.

---

## 11. Long-form pages: research previews and tutorials

These live under `site/news-pages/` (e.g. `flow-field.shtml`) and
`site/tutorials/` (e.g. `svd-demography.shtml`). They use the standard skeleton
(section 3) and add the CSS section-18 components:

- `p.lead` — opening lead paragraph (slightly larger than body); optional
  `<em class="hl">…</em>` for a faint highlight.
- `hr.sam-sep` — thin grey rule between the lead and the first section heading.
- `div.sam-keyfinding` (with `h3.kf`) — pulled-out finding callout (grey
  background, navy left rule).
- `table.headline` — comparison table (with `<colgroup>`, `tr.dir` direction
  hints, `tr.ff` highlighted row, `.grp` banner rows).
- `ol.refs` — numbered reference list; entries get `id="ref-…"` and are
  cross-referenced from prose with `<sup><a href="#ref-…">N</a></sup>`.
- `p.sam-disclaimer` — closing "preliminary results" note.
- A trailing `div.sam-note-left` for contact / "Updated YYYY-MM-DD".

### Interactive / self-contained pages

Some tutorials (e.g. `svd-demography.shtml`) are interactive documents with
MathJax and canvas visualizations. They still use the standard SSI shell and
`sam-block`, but may carry a **page-scoped `<style>` block** in `<head>` for
one-off widgets the shared stylesheet does not define (equation blocks, colored
explanatory callouts, toggle buttons, legends). Rules:

- **Scope it to page-only widget classes.** It must NOT style site-wide classes
  (`.sam-block`, etc.) or the shell (`body`, `html`, `.container`, `header`,
  `main`, `footer`) — those belong to the shell and the page CSS must not fight
  them. (See the inline-style rule, section 12.)
- **Stay light.** Strip any `@media (prefers-color-scheme: dark)` overrides and
  force any color-aware JavaScript to its light palette, so the page matches the
  rest of the site in every browser.
- **MathJax `$…$` delimiters:** `header.html` loads MathJax with default
  delimiters, which do **not** process single-`$` inline math. If the page uses
  `$…$` / `$$…$$`, declare the config **before** the header include:

  ```html
  <script>
    window.MathJax = {
      tex: {
        inlineMath:  [['$','$'], ['\\(','\\)']],
        displayMath: [['$$','$$'], ['\\[','\\]']]
      }
    };
  </script>
  <!--#include virtual="/site/includes/header.html" -->
  ```

  Do **not** load a second MathJax script — `header.html` already loads it.

---

## 12. Page-specific styles and the inline-`<style>` rule

The default is: **page-specific styles that could ever recur belong in a
dedicated section of `sam-styles.css`**, not inline (see NEWS-PAGES and
FORMS-PAGES). Folding into the CSS is preferred whenever similar pages might
reuse the styles.

A page-scoped `<style>` block is a **last resort**, allowed only for a genuinely
self-contained, one-off page — currently the interactive tutorials (section 11,
e.g. `svd-demography.shtml`). Even then it must obey two hard rules:

1. It must be scoped to classes **only that page uses**.
2. It must **NEVER** touch site-wide classes (`.sam-block`, …) or the shell
   elements (`body`, `html`, `.container`, `header`, `main`, `footer`).

Why the strictness: an inline rule that touches a global class silently
overrides it on that one page. This actually happened — the feedback form's
mobile-padding rule once overrode the global `.sam-block` on every page that
shared the selector. If you need to tweak a global class on one page, the right
answer is almost always a new variant class in the CSS, not an inline override.

---

## 13. The top navbar

Markup: [`site/includes/nav-links-top.shtml`](site/includes/nav-links-top.shtml).
Styles: CSS section 12.

```html
<div class="navbar">

  <div class="dropdown navbar-home">                    <!-- left: home avatar -->
    <a href="/index.shtml" target="_parent" class="dropbtn" aria-label="Home">
      <img src="/site/media/thumbs/sam.jpg" alt="Home">
    </a>
  </div>

  <div class="navbar-mid">                              <!-- middle: dropdowns -->
    <div class="dropdown">
      <button class="dropbtn">Academic</button>
      <div class="dropdown-content">...links...</div>
    </div>
    ...
  </div>

</div>
```

- The home avatar is a square `sam.jpg` (477×477 source) scaled to
  `var(--navbar-height)` square; clicking it links to `/index.shtml`.
- `.navbar-mid` grows to fill the space between the avatar and the right edge
  (`flex: 1`), children left-aligned.
- Each `.dropdown` has a `:hover` rule that reveals its `.dropdown-content`.
- **Add a top-level dropdown**: copy a `<div class="dropdown">` block inside
  `.navbar-mid` and edit the button label + link list. **Add a menu item**: edit
  the relevant `.dropdown-content` block.

---

## 14. The footer

Markup: [`site/includes/bottom.shtml`](site/includes/bottom.shtml).
A flex row: copyright + "updated" date on the left, "Send Feedback" button on
the right.

- **Update the date**: edit the `Updated YYYY-MM-DD` line in `bottom.shtml`
  (between the two `<!-- UPDATE THIS -->` markers).
- **Hide the Send Feedback button on a page**: set an SSI flag *before* the
  include:

  ```html
  <footer>
    <!--#set var="hide_feedback_btn" value="1" -->
    <!--#include virtual="/site/includes/bottom.shtml" -->
  </footer>
  ```

  `bottom.shtml` wraps the button in
  `<!--#if expr="v('hide_feedback_btn') != '1'" -->` and skips it when the flag
  is set — used on the feedback page itself.

---

## 15. The feedback form

Reference: [`site/forms/feedback.shtml`](site/forms/feedback.shtml)
and [`site/forms/feedback.php`](site/forms/feedback.php). The
feedback form has its **own dedicated skill** (`samclark-feedback`) that owns its
files, SMTP setup, and its `install-feedback.sh` installer — use that skill for
any work on the form itself.

**Anatomy**

- Static markup: a textarea + optional email field + a hidden honeypot field +
  a hidden `loaded_at` timestamp.
- JS attached at runtime that POSTs to the PHP endpoint (the endpoint URL is
  assembled from a string array so scrapers can't grep for it).
- PHP handler that rejects fast submissions (bots), honeypot fills, and missing
  required fields; on success, emails the message to Sam.

**Anti-bot measures already in place (do not remove)**

- Honeypot field hidden off-screen (`position: absolute; left: -5000px`).
- `loaded_at` timestamp planted at page-load; submissions faster than ~2&nbsp;s
  after load are rejected as bot-like.
- Endpoint URL assembled at runtime, not visible in static HTML.

**Add a new form page**: model it on `feedback.shtml`, reusing the `.feedback-*`
class names (container, label, button, status line, honeypot). If genuinely
different, use a fresh `.<form-name>-*` prefix and a parallel block in the
FORMS-PAGES CSS section. `feedback-config.sample.php` documents the config keys;
the real `feedback-config.php` is `.gitignore`d so credentials don't ship.

---

## 16. Browser quirks and workarounds

Documented inline where they're worked around, collected here for reference.

### Safari bfcache + CSS `zoom`

Safari's back/forward cache restores pages with a stale paint that ignores the
current `body { zoom }` value (navigate away and back, and the page renders at
the wrong scale). **Workaround** (in `header.html`): a `pageshow` listener that
tickles `body.style.zoom` when the page is restored from bfcache
(`event.persisted === true`), forcing a repaint at the correct scale.

### Safari flex `flex: 1` quirks

In nested flex containers Safari sometimes fails to grow a `flex: 1` child
reliably, bunching or overflowing siblings. **Workaround**: `margin-left: auto`
on the element that should be flush-right — the canonical "push right" pattern,
identical in Chrome and Safari.

### Safari `img { width: auto; height: 100% }` quirks

Safari can use an image's intrinsic width during flex layout even when
`width: auto` should compute from the height constraint — this blows the navbar
apart when the avatar's 477&nbsp;px intrinsic width steals space from the
buttons. **Workaround**: give the image **explicit pixel dimensions**; the
`.navbar-home img` rule uses `width/height: var(--navbar-height)`.

### iOS Safari ignores `width: 100%` on iframes

iOS treats iframes as having their embedded page's intrinsic width (often
1000+&nbsp;px), overflowing the parent. **Workaround**: the
`width: 1px; min-width: 100%` pair (iOS honors `min-width`). See
`iframe.biography`.

### iOS Safari focus-zoom on small inputs

iOS auto-zooms when focusing a field with `font-size` under 16&nbsp;px.
**Workaround**: the global mobile rule (CSS section 2) forces `font-size: 16px`
on all text-entry inputs below 780&nbsp;px.

---

## 17. Cache control

Three layers cooperate to make CSS / HTML changes actually show up:

1. **Cache-buster query string**: `header.html` references the CSS as `?ver=NN`.
   **Bump `NN` every time `sam-styles.css` changes** so browsers fetch the new
   URL instead of a cached copy.
2. **`.htaccess` cache headers**: `.css` and `.shtml` are served
   `Cache-Control: no-cache, must-revalidate` (in `<IfModule mod_headers.c>`).
   Browsers may cache but must revalidate; unchanged files come back 304, so the
   overhead is small. Images / fonts / favicons are untouched and stay heavily
   cacheable.
3. **Safari bfcache repaint** (section 16) — handles stale paint from the
   back/forward cache.

If a change still isn't showing in Safari after bumping the version: **Develop →
Empty Caches** (`Cmd+Option+E`). Enable the Develop menu via Settings → Advanced
→ "Show features for web developers".

---

## 18. Adding a new page — checklist

1. Copy `site/pages/TEMPLATE.shtml` (or `site/projects/project-template.shtml`)
   to its new location: `pages/` (general), `projects/` (research),
   `news-pages/` (long-form previews), `tutorials/` (interactive).
2. Save as **`.shtml`** — content pages must be `.shtml` so SSI is parsed. A
   `.html` page shows the literal `<!--#include … -->` comments instead of the
   navbar/header/footer. If you ever rename `.html → .shtml`, update links to it
   and add an `.htaccess` redirect from the old URL.
3. Fill the five head meta tags + `<title>` (`Samuel Clark - …`); add
   `canonical` / JSON-LD / `citation_*` before the header include if useful
   (section 4).
4. Set the `sam-title` (and `sam-byline` for long-form).
5. Write content inside `sam-block`, using `<h1>` for section headings and the
   section-10/11 components as needed.
6. Internal links `target="_parent"`, external `target="_blank"`.
7. Link the page from where it belongs (homepage updates, a project page, the
   navbar) and add it to `sitemap.xml`; add an `.htaccess` alias/redirect if
   warranted.
8. If you touched `sam-styles.css`, bump `?ver=NN` in `header.html`.
9. Update the footer `Updated YYYY-MM-DD` in `bottom.shtml` if appropriate.
10. Test on desktop Chrome **and** desktop Safari **and** mobile (or DevTools
    emulation) — the Safari/iOS quirks in section 16 are real.

---

## 19. Anti-patterns to avoid

- **Don't** inline a `<style>` block that touches site-wide classes
  (`.sam-block`, …) or shell elements — it silently overrides the global style
  on that page. (Page-scoped widget styles for a self-contained tutorial are the
  only exception; section 12.)
- **Don't** duplicate the head / nav / footer markup inside a page. Use the SSI
  includes. Every "self-contained" page becomes a maintenance burden the next
  time the navbar changes.
- **Don't** add a page-specific viewport `<meta>` — `header.html` already
  provides the right one; two clash.
- **Don't** add a second stylesheet. One CSS file by design; use its section
  structure.
- **Don't** rename or move a page without leaving a `Redirect` in `.htaccess`.
- **Don't** forget to bump `?ver=NN` after editing the CSS.
- **Don't** rely on a browser hard-refresh to validate a change "works"; bump
  the version and confirm a normal refresh in a normal session picks it up.

---

## 20. Where to look when something breaks

| Symptom                                | Look at                                                  |
|----------------------------------------|----------------------------------------------------------|
| CSS change not showing up              | `?ver=NN` bumped? `.htaccess` cache rules active?        |
| Layout off only in Safari              | Section 16 (bfcache, flex, img, iframe)                  |
| Mobile inputs cause page zoom          | CSS section 2 — 16&nbsp;px rule applied to that input?   |
| Navbar items overflow / bunch          | `.navbar-mid` flex rules; explicit dims on `.navbar-home img` |
| Footer button shows on feedback page   | SSI `hide_feedback_btn` flag set in the page?            |
| Old URL 404s                           | Add a `Redirect` to `.htaccess`                          |
| Feedback form silently fails           | Honeypot filled? `loaded_at` too recent? Mail config?    |
| MathJax not rendering / `$…$` literal  | `$…$` config before the header include (section 11); CDN load order; console |
| Page shows literal `<!--#include …-->` | Page saved as `.html` instead of `.shtml`                |

---

## 21. Useful Apache + git commands

```bash
# State of the working tree
git status

# Bump the CSS version, commit, push
# (edit ?ver=NN in site/includes/header.html first)
git add site/css/sam-styles.css site/includes/header.html
git commit -m "Describe the CSS change"
git push

# Roll back the last commit if needed
git reset --hard HEAD~1

# Find every page that uses a class
grep -rn "sam-block-padded" samclark.net/

# Check that an .htaccess redirect resolves
curl -I https://samclark.net/cv
```

---

## 22. Project skills

Two durable skills encode recurring workflows for this site:

- **`samclark-feedback`** — everything about the feedback form: its files, SMTP
  setup, packaging, and `install-feedback.sh`.
- **`samclark-site-deploy`** — package any other set of changed site files into a
  datetimestamped, self-installing zip plus a matching install script (same
  convention as `install-feedback.sh`: backup → unpack → retire → verify →
  archive). Use it when you want edits delivered ready to drop onto the server.

---

*Last updated: 2026-06-01. Supersedes `samclark.md` (2026-05-24) and
`house-style.md`.*
