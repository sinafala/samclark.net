# samclark.net — House Style

This document describes the conventions every page on
[samclark.net](https://samclark.net) follows. It is the written-down version of
the structure already present in the site's templates
(`site/pages/TEMPLATE.shtml`, `site/projects/project-template.shtml`), its three
SSI includes, and its single stylesheet (`site/css/sam-styles.css`). When in
doubt, that CSS file's own header comment and the two templates are the source of
truth; this document summarizes and explains them.

---

## 1. Philosophy

- **One stylesheet, one shell.** Every page links the same
  `site/css/sam-styles.css` and is built from the same flex-column shell
  (`header` / `main` / `footer`). Pages differ only in the content inside
  `main`.
- **Design small, scale up.** Content is authored for a narrow ~575&nbsp;px
  column at 12–15&nbsp;px type. On viewports ≥ 780&nbsp;px the whole body is
  scaled with the CSS `zoom` property (see `sam-styles.css` §2). Do not add
  per-page font-size or width hacks for desktop — let the zoom do it.
- **Light only.** The site has a white background and dark text everywhere.
  There is no dark-mode theme. Pages (including interactive ones) should render
  the same in light and dark browsers.
- **Server-Side Includes for shared chrome.** The navbar, `<head>` boilerplate,
  and footer are pulled in with SSI `#include`. This is why content pages use the
  **`.shtml`** extension (see §2).
- **Minimal formatting in prose.** Section headings, paragraphs, and the
  occasional list. Reserve special components (tables, callouts, reference lists)
  for the long-form pages that need them.

---

## 2. File types and the `.shtml` requirement

Content pages are **`.shtml`**, not `.html`. The server only parses
Server-Side Includes in `.shtml` files (this is also why `.htaccess` sets
`Cache-Control: no-cache, must-revalidate` on `css|shtml`). A page saved as
`.html` will display the literal `<!--#include ... -->` comments instead of the
navbar/header/footer, so it will not be in house style.

- Shared fragments that are *included* (never served directly) keep `.html`:
  `site/includes/header.html`.
- Everything a visitor navigates to is `.shtml`.
- If a page must be renamed from `.html` to `.shtml`, update any links to it
  (e.g. in `index.shtml`) and add an `.htaccess` redirect from the old URL so
  external links and any existing `canonical` keep working.

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

- Blank lines after `<!DOCTYPE html>`, after `<html …>`, and around `<body>` are
  the site's habit — harmless, kept for consistency with existing files.
- `.container` is a full-height flex column. `header` and `footer` are fixed;
  `main` scrolls internally. Never put page content outside `main`.
- Put essentially all body content inside a single `<div class="sam-block">`
  (the 575&nbsp;px content column). Long-form pages may use other `sam-*`
  containers, but `sam-block` is the default.

---

## 4. `<head>` conventions

Order and contents, in this order:

1. `<meta name="author" content="Samuel J. Clark">`
2. `<meta name="description" …>` — one sentence.
3. `<meta name="keywords" …>` — usually leads with `Samuel J. Clark, Samuel
   Clark, Sam Clark, …` then page-specific terms.
4. `<meta property="og:title" …>` and `<meta property="og:description" …>`.
5. `<title>` — format **`Samuel Clark - <Page Title>`** (the homepage uses the
   longer `Samuel J. Clark - Demographer`).
6. `<!--#include virtual="/site/includes/header.html" -->` — **always last** in
   `<head>`.

`header.html` already provides, site-wide: Google Analytics, the viewport tag,
Twitter-card + Open Graph defaults (`og:site_name`, `og:type=website`,
`og:image`, `og:locale`), the stylesheet link (cache-busted `?ver=NN`), MathJax,
the favicon set, Font Awesome, and a Safari bfcache repaint fix. **Do not repeat
any of these in the page head.**

Page-specific SEO that header.html does *not* provide may be added **before** the
header include and is welcome where it helps (Sam cares about SEO):

- `<link rel="canonical" href="https://samclark.net/…actual page URL…">` — must
  point at the page's real, final URL.
- A `<script type="application/ld+json">` JSON-LD block
  (e.g. `ScholarlyArticle`).
- `citation_*` meta for Google Scholar.

Avoid re-declaring `og:type` / `og:site_name` / `twitter:card` in the page head —
header.html owns those, and duplicate `og:*` tags with different values are
ambiguous to scrapers.

---

## 5. The three includes

| Include | Role |
| --- | --- |
| `site/includes/header.html` | Everything inside `<head>` except the per-page meta/title above. |
| `site/includes/nav-links-top.shtml` | The navy top navbar: home avatar + Academic / VA / Personal / News+ dropdowns. First child of `<header>`. |
| `site/includes/bottom.shtml` | Copyright line + `Updated YYYY-MM-DD` + the "Send Feedback" button. Sole child of `<footer>`. |

Two maintenance points live in these files, not in pages:

- The stylesheet cache-buster `?ver=NN` is in `header.html`. Bump it when you
  change `sam-styles.css`.
- The footer's `Updated YYYY-MM-DD` line is in `bottom.shtml`.

The footer also hides the feedback button on the feedback page itself via an SSI
flag (`hide_feedback_btn`).

---

## 6. Titles, bylines, headings

- **Page title:** `<div class="sam-title">…</div>` — large, centered, in
  `<header>`, after the navbar include. Use `sam-title-short` when it sits under a
  tight navbar.
- **Byline (long-form pages):** `<div class="sam-byline">Samuel J. Clark</div>`
  directly under the title.
- **In-page section headings:** use `<h1>` for the section headings *inside*
  `sam-block` (this is the site convention — see `methods.shtml` and
  `flow-field.shtml`). The visual hierarchy is set by the stylesheet, not by
  jumping heading levels:
  - `h1` — section heading (15&nbsp;px).
  - `h2` — sub-points / dense sub-headings (12&nbsp;px). The homepage uses `h2`
    for dated update entries (`<h2>2026-04</h2>`).
  - `h3` — italic sub-sub-heading (13&nbsp;px italic).
- Special heading variants exist for specific pages: `h1.sam-date` (green date,
  news), `h2.bib-year` / `h3.bib` (bibliography).

---

## 7. Links and palette

Global link colors (from `sam-styles.css` §6):

- link / visited: navy `#0000A0`
- hover / focus: `green`
- active: `red`

Internal site links use `target="_parent"`; external links use
`target="_blank"`. Do not hard-code link colors inline in normal prose — let the
global anchor styles apply. (Inline color is acceptable only inside a
self-contained interactive page that carries its own palette; see §9.)

The site's structural blue is `#0000A0` (navbar, rules, buttons, key-finding
left border). Greens are used for "good"/date accents.

---

## 8. Content containers and components

All live in `sam-styles.css`; the common ones:

- `div.sam-block` — default 575&nbsp;px content column. Variants:
  `sam-bold-block`, `sam-block-padded`, `sam-centered-block`, `sam-block-index`.
- `div.sam-image` — captioned-image wrapper used inside `sam-block`. Pair with an
  `img.mtb` (block, centered, full-width responsive) and bold `Figure N.`
  caption text.
- `div.sam-links-list` / `-short`, `div.sam-link-top` / `-bottom` — link strips.
- `div.sam-note*` — small grey fine-print (`sam-note`, `-left`, `-top`,
  `-bottom`).
- Two-column rows: `.row-title`, `.row-index`, `.row-people` with their
  matching `.column-*-*` children.
- Images: `img.mtb` (wide figures), `img.sam-index` (right-float home thumb),
  `img.sam-plain`, `img.sam-logo`, `img.sam-people`, `img.badge`.
- Media paths: thumbnails in `/site/media/thumbs/`, full-size in
  `/site/media/fullsize/`.

---

## 9. Long-form pages: research previews and tutorials

These live under `site/news-pages/` (e.g. `flow-field.shtml`) and
`site/tutorials/` (e.g. `svd-demography.shtml`). They use the standard skeleton
(§3) and add the §18 stylesheet components:

- `p.lead` — opening lead paragraph (slightly larger than body); optional
  `<em class="hl">…</em>` for a faint highlight.
- `hr.sam-sep` — thin grey rule between the lead and the first section heading.
- `div.sam-keyfinding` (with `h3.kf`) — pulled-out finding callout (grey
  background, navy left rule).
- `table.headline` — comparison table (with `<colgroup>`, `tr.dir` direction
  hints, `tr.ff` highlighted row, `.grp` banner rows).
- `ol.refs` — numbered reference list; entries get `id="ref-…"` and are
  cross-referenced from the prose with `<sup><a href="#ref-…">N</a></sup>`.
- `p.sam-disclaimer` — closing "preliminary results" note.
- A trailing `div.sam-note-left` for contact / "Updated YYYY-MM-DD".

### Interactive / self-contained pages

Some pages (e.g. the SVD tutorial) are interactive documents with MathJax and
canvas visualizations. They still use the standard skeleton and `sam-block`, but
may carry a **page-scoped `<style>` block** in `<head>` for widgets the shared
stylesheet does not define (equation blocks, colored explanatory callouts,
toggle buttons, legends). This follows the CSS file's own
"feature-scoped styles use `<feature>-*`" convention, kept local to the page
because it is one-off. Rules for these pages:

- **Do not restyle `body`, `html`, `.container`, `header`, `main`, or `footer`**
  in the page-scoped block — those belong to the shell and the page-scoped CSS
  must not fight them. Scope page CSS to its own widget classes only.
- **Stay light.** Strip any `@media (prefers-color-scheme: dark)` overrides and
  force any color-aware JavaScript to its light palette, so the page matches the
  rest of the site in every browser.
- **MathJax `$…$` delimiters:** `header.html` loads MathJax with default
  delimiters, which do **not** process single-`$` inline math. If a page uses
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

  Do **not** load a second MathJax script — header.html already loads it.

---

## 10. `sam-styles.css` organization

The stylesheet is a single file with a numbered table of contents in its header
comment (sections 1–19). Conventions, from that header:

- **Design tokens** live in one `:root` (§1); retune the site from there rather
  than hard-coding values deeper in the file.
- Shorthand padding is `top right bottom left`; color literals follow
  htmlcolorcodes.com naming.
- Site-wide content blocks use `sam-*` classes; feature-scoped styles use
  `<feature>-*` (e.g. `feedback-*`, `navbar-*`).
- Section-specific `@media` queries live *inside* their section; globally-applied
  ones live in §2.

When you genuinely need new shared styles, add a new numbered section rather than
scattering rules, and bump the `?ver=NN` cache-buster in `header.html`.

---

## 11. Routing and SEO

- **`.htaccess`** holds short, memorable aliases (`/va`, `/cv`, `/methods`,
  `/svdcomp`, …), legacy URL fixes, and the `css|shtml` cache-control rule. Add a
  redirect here when a page is renamed or when a short alias is useful, and put
  anchored shortcuts (`…/news.shtml#2024-07-04`) here too.
- **`robots.txt`** allows everything except non-public areas
  (`/site/clark/`, `/site/includes/`, `/site/search/`), the two template files,
  and the feedback form/handler. It points at the sitemap.
- **`sitemap.xml`** lists public pages with `lastmod` / `changefreq` /
  `priority`. Add new public pages here. (Note: the sitemap is curated, not
  auto-generated — new long-form pages such as the news/tutorial pages should be
  added by hand.)

---

## 12. Checklist for a new page

1. Copy `site/pages/TEMPLATE.shtml` (or `project-template.shtml`).
2. Save as **`.shtml`** in the right folder
   (`site/pages/`, `site/projects/`, `site/news-pages/`, `site/tutorials/`).
3. Fill the five head meta tags + `<title>` (`Samuel Clark - …`); add
   `canonical` / JSON-LD if useful, before the header include.
4. Set the `sam-title` (and `sam-byline` for long-form).
5. Write content inside `sam-block`, using `<h1>` for section headings and the
   §8/§9 components as needed.
6. Use `target="_parent"` for internal links, `target="_blank"` for external.
7. Link to the page from wherever it belongs (homepage updates, a project page,
   the navbar) and add it to `sitemap.xml`; add an `.htaccess` alias/redirect if
   warranted.
8. If you touched `sam-styles.css`, bump `?ver=NN` in `header.html`.
9. Update the footer `Updated YYYY-MM-DD` in `bottom.shtml` if appropriate.
