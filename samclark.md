# samclark.net — Style Guide & Best Practices

This document describes how the site is structured, the conventions it
follows, the browser quirks it works around, and the patterns to reuse
(or avoid) when making changes. It is meant as a working reference for
future-Sam and anyone else editing the site.

The actual site files live in [`samclark.net/`](samclark.net/).

---

## 1. Overview

**samclark.net** is a hand-authored static site served by Apache with
Server Side Includes (SSI) enabled. There is no build step, no
framework, and no dependency manager. Every page is a `.shtml` file
that includes a few shared SSI fragments for the head, navbar, and
footer, and links to a single shared CSS file.

**Stack at a glance**:
- Apache (with `mod_include` for SSI and `mod_headers` for cache control)
- Plain HTML5 / CSS3 / vanilla JavaScript
- PHP only for the feedback form handler (`site/forms/feedback.php`)
- Font Awesome 4.7 (CDN) for icons
- MathJax 3 (CDN) for math typesetting
- Google Analytics tag (gtag.js)

**Editing model**: edit the files in place, push to the repo, and the
production server picks them up. There is no build, no compile, no
deploy pipeline beyond `git push`.

---

## 2. Directory layout

```
samclark.net/
├── .htaccess                       Apache: redirects + cache headers
├── index.shtml                     Home page
├── robots.txt
├── sitemap.xml
└── site/
    ├── css/
    │   └── sam-styles.css          THE stylesheet (see section 5)
    ├── includes/                   SSI fragments (see section 4)
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
    ├── news-pages/                 Long-form research previews
    │   └── flow-field.shtml        Reference example
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

**When adding a new page**: pick the directory that matches its
purpose. New research previews go in `news-pages/`, new forms in
`forms/`, general content in `pages/` or `projects/`.

---

## 3. URL aliases (`.htaccess`)

The site uses Apache `Redirect` and `RedirectMatch` rules to expose
short, memorable URLs that point at the canonical file paths. For
example:

| Short URL  | Canonical                                     |
|------------|-----------------------------------------------|
| `/cv`      | `/site/cv/Samuel-Clark_CV.pdf`                |
| `/cvs`     | `/site/pages/cv.shtml`                        |
| `/pubs`    | `/site/pages/bibliography.shtml`              |
| `/mtb`     | `/site/pages/mtb.shtml`                       |
| `/va`      | `/site/projects/verbal-autopsy.shtml`         |
| `/openva`  | `/site/projects/verbal-autopsy.shtml`         |

When you rename or move a page, **leave a redirect in `.htaccess`** so
external links and bookmarks keep working. The "LEGACY URL FIXES" and
"LEGACY PREFIX REDIRECTS" sections of `.htaccess` are full of examples.

---

## 4. SSI includes

Every page is composed via three SSI directives that live in
`/site/includes/`:

```html
<head>
  ...metadata...
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
        ...content...
      </div>
    </main>
    <footer>
      <!--#include virtual="/site/includes/bottom.shtml" -->
    </footer>
  </div>
</body>
```

This is the canonical page skeleton — see
[`site/pages/TEMPLATE.shtml`](samclark.net/site/pages/TEMPLATE.shtml).

**What lives in each include**:

- **`header.html`** — `<meta>` defaults, OG/Twitter tags, the CSS
  `<link>` with the `?ver=NN` cache buster, MathJax, favicons, Font
  Awesome, the Google Analytics tag, and a small `pageshow` listener
  for the Safari bfcache repaint workaround (section 11).
- **`nav-links-top.shtml`** — the navy navbar: home-avatar link on the
  left + four dropdown menus in the middle. See section 8.
- **`bottom.shtml`** — copyright line + "Send Feedback" button. The
  button is conditionally hidden via an SSI `<!--#set var ... -->` flag
  on the feedback page itself (section 9).

**Anti-pattern**: do NOT create "self-contained" pages that inline the
nav/footer/styles. That was tried briefly with the feedback form and
ripped out immediately — it means every header/footer change has to be
re-applied by hand, and styles drift.

---

## 5. CSS (`site/css/sam-styles.css`)

There is exactly **one** stylesheet for the whole site. Read the
top-of-file comment block — it lists all 19 numbered sections and the
conventions in detail.

### Design tokens (section 1 of the CSS)

All re-tunable values live in a single `:root` block:

```css
:root {
  --desktop-zoom:    1.7;    /* desktop zoom factor */
  --mobile-edge-pad: 12px;   /* L/R padding on phones */
  --navbar-height:   30px;   /* navbar + home avatar size */

  --bg:       #ffffff;
  --tile-bg:  #f2f2f2;
  --fg:       #111;
  --muted:    #555;
  --gap:      10px;
  --radius:   8px;
}
```

**Rule**: when a value might be retuned (sizes, palette, breakpoints),
add it here as a token rather than hard-coding it in a rule. The
navbar height already does this — `.navbar { height: var(--navbar-height) }`
and `.navbar-home img { width: var(--navbar-height); height: var(--navbar-height) }`
both flow from one knob.

### Naming conventions

| Pattern              | Used for                                                     |
|----------------------|--------------------------------------------------------------|
| `.sam-*`             | site-wide content blocks (`sam-block`, `sam-title`, ...)     |
| `<feature>-*`        | feature-scoped classes (`feedback-*`, `navbar-*`)            |
| `.row-* / .column-*` | two-column flex layouts                                      |
| `.column-left-*` etc | left/right halves of a two-column row                        |

When adding rules for a new component, give it a class prefix and keep
all its rules together in one section (see NEWS-PAGES and FORMS-PAGES
in the CSS file for examples).

### Color palette

| Hex       | Used for                                  |
|-----------|-------------------------------------------|
| `#0000A0` | Navy — links, navbar, primary buttons     |
| `green`   | Hover state, success state                |
| `red`     | Active link flash, error state            |
| `#f2f2f2` | Light grey — gallery tile / callout bg    |
| `#555`    | Muted secondary text (notes, captions)    |
| `#fff5d6` | Faint cream — print-style highlight wash  |
| `#eaeaff` | Pale blue — `:target` highlight, table row|

Use color literals from this palette, not arbitrary hex values.

### Where to add new CSS

| What you're adding                                | Goes in section          |
|---------------------------------------------------|--------------------------|
| New token / tunable value                         | 1. DESIGN TOKENS         |
| New global element default                        | 3. BASE / RESET          |
| New heading / text style                          | 5. TYPOGRAPHY            |
| New content container (`.sam-something`)          | 7. CONTENT CONTAINERS    |
| New small fine-print variant                      | 8. NOTES & DISCLAIMERS   |
| New nav button / footer tweak                     | 12. NAVBAR / 13. FOOTER  |
| New image class                                   | 16. IMAGES & MEDIA       |
| Style for a research-preview page                 | 18. NEWS-PAGES           |
| Style for a new form                              | 19. FORMS-PAGES          |

**If your page needs styles that don't fit any existing class**, prefer
adding a new class to the right section of `sam-styles.css` over
inlining a `<style>` block on the page. Inline `<style>` blocks have
caused real bugs (e.g., the feedback form's mobile padding rule
silently overriding the global `.sam-block` on every page).

---

## 6. Mobile / desktop scaling strategy

Pages are designed for a **~575 px content column at 12-15 px type**.
That is the mobile design. On viewports `>= 780 px` the entire body is
scaled up via the non-standard `zoom` property:

```css
@media (min-width: 780px) {
  body { zoom: var(--desktop-zoom); }
}
```

`zoom` is non-standard but supported in WebKit/Blink and Firefox 126+,
which covers everything we care about. The advantage: design once at
the small size, then scale, instead of writing separate stylesheets
per breakpoint.

**Mobile polish** lives in one block in section 2 of the CSS:
- Force 16 px font-size on all text-entry controls so iOS Safari does
  NOT auto-zoom on field focus.
- Add `--mobile-edge-pad` (12 px) to every primary content container
  so text doesn't hug the screen edges.

**When you add a new content-block class** that should honor the
mobile edge padding, add it to the selector list in the
`@media (max-width: 779px)` block.

---

## 7. Page-specific styles

Some pages do legitimately need styles that don't make sense as
site-wide rules (e.g., a comparison table specific to one research
preview). Two patterns:

1. **Inline `<style>` in the page head** — fine for genuinely one-off
   styles that are scoped to classes only that page uses. Reference:
   none currently — both former examples were folded into the CSS.

2. **Folded into the CSS** under a dedicated section — preferred when
   the styles might be reused on similar pages. References:
   `NEWS-PAGES` section for research previews,
   `FORMS-PAGES` section for forms.

**Strong rule**: page-inline styles must NEVER touch site-wide classes
(`.sam-block`, etc.). If you need to tweak a global class on one page,
the right answer is almost always to introduce a new variant class.

---

## 8. The top navbar

Markup lives in [`site/includes/nav-links-top.shtml`](samclark.net/site/includes/nav-links-top.shtml).
Styles are in section 12 of the CSS.

Structure:

```html
<div class="navbar">

  <div class="dropdown navbar-home">                    <-- left: home avatar
    <a href="/index.shtml" target="_parent" class="dropbtn" aria-label="Home">
      <img src="/site/media/thumbs/sam.jpg" alt="Home">
    </a>
  </div>

  <div class="navbar-mid">                              <-- middle: dropdowns
    <div class="dropdown">
      <button class="dropbtn">Academic</button>
      <div class="dropdown-content">...links...</div>
    </div>
    ...
  </div>

</div>
```

- The home avatar is a square `sam.jpg` (477x477 source) scaled to
  `var(--navbar-height)` square. Clicking it links to `/index.shtml`.
- `.navbar-mid` grows to fill the space between the avatar and the
  right edge (`flex: 1`), with its children left-aligned.
- Each `.dropdown` has a `:hover` rule that shows its `.dropdown-content`.

**Adding a new top-level dropdown**: copy one of the existing
`<div class="dropdown">` blocks inside `.navbar-mid` and edit the
button label + the link list.

**Adding a new menu item to an existing dropdown**: edit the
`.dropdown-content` block of the appropriate dropdown.

---

## 9. The footer

Markup lives in [`site/includes/bottom.shtml`](samclark.net/site/includes/bottom.shtml).
It is a flex row: copyright + "updated" date on the left, "Send
Feedback" button on the right.

**Updating the "Updated YYYY-MM-DD" date**: edit `bottom.shtml`. The
date appears between two `<!-- UPDATE THIS -->` markers.

**Hiding the Send Feedback button on a specific page**: set an SSI
flag *before* the include:

```html
<footer>
  <!--#set var="hide_feedback_btn" value="1" -->
  <!--#include virtual="/site/includes/bottom.shtml" -->
</footer>
```

`bottom.shtml` wraps the button in `<!--#if expr="v('hide_feedback_btn') != '1'" -->`
and skips rendering it when the flag is set. Used on the feedback page
itself so the button doesn't link back to the page you're already on.

---

## 10. The feedback form

Reference: [`site/forms/feedback.shtml`](samclark.net/site/forms/feedback.shtml)
and [`site/forms/feedback.php`](samclark.net/site/forms/feedback.php).

**Anatomy**:
- Static markup: a textarea + optional email field + a hidden
  honeypot field + a hidden `loaded_at` timestamp.
- JS attached at runtime that POSTs to the PHP endpoint (the endpoint
  URL is assembled from a string array so scrapers can't grep for it).
- PHP handler that rejects fast submissions (bots), honeypot fills,
  and missing required fields; on success, emails the message to Sam.

**Anti-bot measures already in place** (do not remove):
- Honeypot field hidden off-screen (`position: absolute; left: -5000px`).
  Bots fill it; humans don't see it.
- `loaded_at` timestamp planted at page-load. Submissions arriving
  faster than ~2 seconds after load are rejected as bot-like.
- Endpoint URL assembled at runtime, not visible in static HTML.

**To add a new form page**: model it on `feedback.shtml`. Use the
`.feedback-*` class names for the same patterns (form container,
label, button, status line, honeypot). If the form is genuinely
different in look or behavior, use a fresh `.<form-name>-*` prefix
and add a parallel set of rules to the FORMS-PAGES CSS section.

**`feedback-config.sample.php`** documents the config keys; the real
`feedback-config.php` is `.gitignore`d so credentials don't ship.

---

## 11. Browser quirks and workarounds

These are documented in inline comments where they're worked around,
but worth collecting here.

### Safari bfcache + CSS `zoom`

Safari's back/forward cache (bfcache) restores pages with a stale paint
that does not respect the current `body { zoom }` value. The symptom:
navigate away from a page and come back, and the page renders at the
wrong zoom even though the computed style is correct.

**Workaround** (in `site/includes/header.html`): a small `pageshow`
listener that tickles `body.style.zoom` when the page is restored
from bfcache (`event.persisted === true`), forcing a repaint at the
correct scale.

### Safari flex `flex: 1` quirks

In nested flex containers, Safari sometimes fails to grow a `flex: 1`
child reliably, causing siblings to bunch together or overflow.

**Workaround**: `margin-left: auto` on the element that should be
flush-right. It's the canonical "push to the right" pattern and works
identically in Chrome and Safari regardless of how `flex: 1` resolves.

### Safari `img { width: auto; height: 100% }` quirks

Safari can use an image's intrinsic width during flex layout even
when `width: auto` should give a computed width matching the height
constraint. Blows the navbar apart when the avatar's intrinsic 477 px
width briefly steals space from the buttons.

**Workaround**: give the image **explicit pixel dimensions** rather
than `width: auto`. The `.navbar-home img` rule uses
`width: var(--navbar-height); height: var(--navbar-height)`.

### iOS Safari ignores `width: 100%` on iframes

iOS treats iframes as having their embedded page's intrinsic width,
which is often 1000+ px and overflows the parent.

**Workaround**: the `width: 1px; min-width: 100%` pair. iOS honors
`min-width`, so the iframe ends up at the parent container's width.
See `iframe.biography` in the CSS.

### iOS Safari focus-zoom on small inputs

iOS auto-zooms the page when the user focuses a form field whose
font-size is under 16 px.

**Workaround**: the global mobile rule in section 2 of the CSS forces
`font-size: 16 px` on all text-entry inputs on viewports < 780 px.

---

## 12. Cache control

Three layers cooperate to make CSS / HTML changes actually show up:

1. **Cache-buster query string**: `header.html` references the CSS as
   `?ver=NN`. **Bump `NN` every time `sam-styles.css` is changed** so
   browsers fetch the new URL rather than reusing their cached copy.

2. **`.htaccess` cache headers**: `.css` and `.shtml` files are served
   with `Cache-Control: no-cache, must-revalidate` (wrapped in
   `<IfModule mod_headers.c>`). Browsers may still cache them, but must
   conditionally revalidate with the server on every use. Unchanged
   files come back as 304 Not Modified, so the overhead is small.
   Images / fonts / favicons are untouched and remain heavily cacheable.

3. **Safari bfcache repaint** (section 11) — handles the case where
   the browser restores a stale paint from its back/forward cache.

If a change isn't showing up in Safari after bumping the cache version:
**Develop menu → Empty Caches** (or `Cmd+Option+E`). Enable the Develop
menu via Settings → Advanced → "Show features for web developers" if
it's not visible.

---

## 13. Adding a new page — checklist

1. Copy [`site/pages/TEMPLATE.shtml`](samclark.net/site/pages/TEMPLATE.shtml)
   to its new location (`site/pages/` for general, `site/projects/` for
   research projects, `site/news-pages/` for long-form previews).
2. Fill in the `<title>` and `<meta name="description">` / keywords /
   `og:title` / `og:description`. They appear in browser tabs, search
   results, and link previews.
3. Replace the title string in `<div class="sam-title">`.
4. Write the page content inside `<div class="sam-block">` (or one of
   the variants — see CSS section 7).
5. If the page needs an entry in the navbar, add it to
   `site/includes/nav-links-top.shtml`.
6. If the page should be reachable from a short URL, add a `Redirect`
   line to `.htaccess`.
7. Test on desktop Chrome AND desktop Safari AND mobile (or at least
   browser DevTools mobile emulation). The Safari / iOS quirks listed
   in section 11 are real; budget a moment to check.

---

## 14. Anti-patterns to avoid

- **Don't** inline `<style>` blocks on individual pages that touch
  site-wide classes (`.sam-block`, etc.). It will silently override
  the global style on that one page.
- **Don't** duplicate the head / nav / footer markup inside a page.
  Use the SSI includes. Every "self-contained" page becomes a
  maintenance burden the next time the navbar changes.
- **Don't** add page-specific viewport `<meta>` tags. The shared
  `header.html` already provides the right one. Two of them clash.
- **Don't** add a second stylesheet. There is one CSS file by design.
  Use the existing section structure to keep new rules organized.
- **Don't** rename or move a page without leaving a `Redirect` in
  `.htaccess`. External links and bookmarks will break.
- **Don't** forget to bump `?ver=NN` after editing the CSS.
- **Don't** rely on browser hard-refresh to validate a change "works".
  Bump the version and check that a normal-refresh in a normal session
  picks it up.

---

## 15. Where to look when something breaks

| Symptom                                          | Look at                                          |
|--------------------------------------------------|--------------------------------------------------|
| CSS change not showing up                        | `?ver=NN` bumped? `.htaccess` cache rules active?|
| Layout off only in Safari                        | Section 11 of this doc (bfcache, flex, img, iframe) |
| Mobile inputs cause page zoom                    | Section 2 of CSS (16 px rule applied to that input type?) |
| Navbar items overflow / bunch                    | `.navbar-mid` flex rules; explicit dimensions on `.navbar-home img` |
| Footer button shows on feedback page             | SSI `hide_feedback_btn` flag set in the page?    |
| Old URL 404s                                     | Add a `Redirect` to `.htaccess`                  |
| Feedback form silently fails                     | Honeypot filled? `loaded_at` too recent? Mail config? |
| MathJax not rendering                            | CDN load order in `header.html`; check console   |

---

## 16. Useful Apache + git commands

```bash
# See the current state of the working tree
git status

# Bump the CSS version, commit, and push
# (manually edit ?ver=NN in site/includes/header.html first)
git add site/css/sam-styles.css site/includes/header.html
git commit -m "Describe the CSS change"
git push

# Roll back the last commit if needed
git reset --hard HEAD~1

# Find every page that uses a particular class
grep -rn "sam-block-padded" samclark.net/

# Check that a redirect in .htaccess resolves
curl -I https://samclark.net/cv
```

---

*Last updated: 2026-05-24.*
