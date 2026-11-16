# CLAUDE.md

Classic (non-block) WordPress theme for Ismael Rehab's photo/video portfolio, built on the FHGR IM5 "Portfolio Starter Theme" boilerplate (HS26). Plain PHP/CSS/JS, no build step, no package manager, no tests, and no local PHP/Node install — PHP can't be linted locally.

Design source: Figma exports in `../../mokups/` (DESKTOP/*.pdf, Mobile/*.png). Colors (decided 15.11.2026, 60-30-10): cream `#fffeec` background, dark `#494a45` text/lines, blue `#1b4a6f` only for actions (Kontakt, Senden, active states), green `#1c7f60` only for category labels/filters; only these four colors — no white, black, red, beige or transparency tints (form errors use a dashed border + «!» icon). Lines only: 3px under the header, above the footer, the homepage intro divider, and form field borders; no arrows in links/buttons. Fonts (self-hosted woff2): Montserrat 300/400/700/900, DM Serif Display regular + italic.

## Deployment — read first

[.vscode/settings.json](.vscode/settings.json) configures the VS Code FTP Deploy extension with `"uploadOnSave": true` to the live Hostpoint server (`remotePath: "/"` maps to `wp-content/themes/<theme>/`). **Every saved file is uploaded to production immediately.** Broken PHP on save means a broken live site, so keep edits syntactically complete.

## Dependencies

- **Carbon Fields** must be active as a plugin — it is not bundled. All content fields (`Container`, `Field`, `carbon_get_*`) depend on it.
- Requires PHP 8+ (union types, `mixed`, `str_contains`).

## Architecture

`functions.php` only `require_once`s each `backend/*/index.php`, in this order: `init`, `helpers`, `nav`, `cpt`, `tax`, `carbonfields`, `getter`, `contact`, `seo`.

### backend/ — functionality
| Folder | Purpose |
|---|---|
| `init/` | Admin cleanup: custom image sizes (`preload`, `xxs`…`xl`), Gutenberg disabled, page editor removed, posts/categories/tags/comments hidden, Customizer/theme editor blocked, SVG uploads allowed. Rarely edited. |
| `helpers/` | Global helpers: `js_log(...)`, `get_media_by_id($id, $size)` → `['title','alt','src','width','height','srcset','type']` or `false`, `get_responsive_media($id, $mobile_id, $size)` → `['desktop','mobile']` (for `partials/picture.php`), `get_menu_by_slug($slug)`, `get_youtube_id($url)`. |
| `nav/` | `register_nav_menus`: `main` (Hauptnavigation), `legals` (Rechtliches). |
| `cpt/` | Custom post types — one `_<slug>.php` per CPT, required inside the `init` hook. `project` (URL `/projekt/…`, ordered via page-attributes `menu_order`). |
| `tax/` | Taxonomies — `project_category` (no public archives; used for tags and the Sammlung filter). |
| `carbonfields/` | Field config, loaded on `carbon_fields_register_fields`. `_globals.php` = theme options (contact, socials, form texts/recipient/sender address); `_project.php` = project teaser images/size/homepage text + Stammdaten; `_modules.php` = module builder (pages **and** projects) + `render_modules()`. |
| `getter/` | `_globals.php`: `get_globals_contact/socials/form()`. `_project.php`: `get_project_teaser_by_id()`, `get_projects()`, `get_project_categories()`, `get_used_project_categories()`, `get_project_facts()`. |
| `seo/` | `<title>` (`get_document_title_text()`: home = name – tagline, else `Page | name`), meta description + Open Graph (per-page `seo_description`/`seo_image` from `carbonfields/_seo.php`, fallbacks: project highlight text/image, then Globales), JSON-LD `ProfessionalService` on the front page, theme favicon (`frontend/assets/favicon/`) unless a WP site icon is set. |
| `contact/` | AJAX handler `contact_form` (nonce + honeypot) → `wp_mail()` to the configured recipient, `From:` = theme option `form_sender` if set. On failure the `wp_mail_failed` reason is appended to the error message for logged-in admins only. |

### Module system (page builder)
Pages have no editor; content is a Carbon Fields `complex` field `modules` defined in [backend/carbonfields/_modules.php](backend/carbonfields/_modules.php). Adding a module = three touches:
1. `->add_fields('<slug>', '<Label>', [...])` in the complex field.
2. Optional `case '<slug>':` in `render_modules()` to transform data (e.g. image IDs → `get_media_by_id()`).
3. View at `frontend/views/modules/<slug>.php`, reading `$args['data']`, wrapped in `<section data-module="<slug>">`.

`render_modules()` loads `frontend/views/modules/{$module['_type']}`, so the view filename must equal the module slug (the `data-module` attribute uses hyphens, e.g. `hero-image`).

Modules: `intro` (homepage split: sticky greeting + portrait that opens contact, hand-picked projects via association), `hero_image`, `story` (alternating text/image), `title` (title + lead, shows categories on projects), `facts`, `video` (YouTube, optional facts sidebar), `reels` (≤3 vertical, YouTube Shorts or MP4), `text`, `gallery` (per-image width 1/4–full + double height, CSS grid dense), `carousel` (centered item enlarged), `collection` (all projects + category filter), `contact` (form block, mobile-only by default, optional image beside it), `cta` (question + Kontakt + link to Sammlung), and for the Angebot page `offer_intro`, `offers` (cards; `occasion` preselects the form field via `data-occasion`), `process` (full-width blue band, `id="ablauf"`; phases → nested complex `steps` with title/addition/text, numbered 01… across phases), `testimonials`, `faq` (`<details>`). `get_collection_url()` (Globales → Seiten, else first page containing the `collection` module) feeds the intro CTA, the project back link and the active menu item on project pages.

Project teasers: the Sammlung shows the designer-made `teaser_image` (title baked in); its grid span (1 or 2 of 3 columns) is chosen in `main.js` with a seeded pseudo-random per project ID unless `teaser_size` forces narrow/wide. The Sammlung grid has 12 columns; `layoutCollection()` in `main.js` builds rows of 2–4 projects from `rowPatterns`, reshuffled per visit (seeded once per page load), and gives high-priority projects (`teaser_size`: wide = high, narrow = low) the widest slot. Homepage desktop shows `highlight_image` + title + text; mobile (<768px) shows only images (4:5) with the category label, title/text visually hidden, plus an «Alle Projekte ansehen» button.

Mobile-only extras: `.page-cta` (rendered before the footer, hidden when `main` already contains a `.cta` or contact module), the project title image in the `title` module, and the lightbox (`views/layout/lightbox.php`, gallery/carousel images on project pages). Homepage typing animation: an inline script in `header.php` sets `html.is-typing` on the first visit per session (`sessionStorage`, off with reduced motion); `main.js` splits the intro heading into `.char` spans and reveals them one by one (switching to `is-typing-js`; per-part speed in `speeds`, the name slower), including the subtitle; CSS fallback shows the heading after 3s if the script fails. New image sub-sizes are saved as WebP (`image_editor_output_format` in `init/`), and the homepage portrait is preloaded (`seo/`).

### Templates
Root WP templates (`index.php` = 404 fallback, `page.php`, `single-project.php`) are thin: `get_header()` → `frontend/views/layout/header` → `frontend/views/pages/<x>` → `frontend/views/layout/footer` → `get_footer()`. Root `header.php`/`footer.php` hold the HTML shell and load `frontend/styles/main.css` and `frontend/scripts/main.js` (ES module) directly — no `wp_enqueue_*`; both are cache-busted with the theme version from `style.css`. `footer.php` also renders the contact `<dialog>` (`views/layout/contact.php`); any element with `data-contact-open` opens it. The form has a hidden input named `action`, which shadows `form.action` in JS — `main.js` reads the URL with `getAttribute('action')`. After adding a `single-<cpt>.php`, permalinks must be re-saved in WP admin.

### frontend/
- `views/layout|pages|modules|teaser|partials/` — markup. `partials/` holds reusable bits: `picture`, `image`, `facts`, `contact-form`, `icon`.
- `styles/main.css` — imports `_fonts.css` and `_resets.css`; design tokens as custom properties in `:root` — use only these: spacing `--space-1`…`--space-8` (8/16/24/32/48/64/96/128), radii `--radius-s` 6px (buttons, labels, fields) / `--radius-l` 20px (images, cards), font sizes `--text-xs`…`--text-3xl` (14/16/20/24/32/48/88); components are selected via data attributes (`[data-layout]`, `[data-page]`, `[data-module="..."]`) with native CSS nesting; desktop-first `max-width` breakpoints (3xl 1514 → xxs 400) at the bottom.
- `assets/fonts/` — self-hosted woff2, declared in `_fonts.css`.

### Motion
The «Bewegung» block in `main.css` + end of `main.js`: scroll reveals (`main.js` adds `.reveal` only to elements below the fold, IntersectionObserver adds `.is-visible`, classes are removed again after 2s; easing `--ease-glide`, 48px travel), cross-document view transitions (`@view-transition`; project images share `view-transition-name: project-image-<id>` via the `transition` arg of the `picture`/`image` partials), sliding nav highlight and mobile menu, soft FAQ (`::details-content`), hero parallax (`animation-timeline: view()`), mobile header hides on scroll down (`.is-hidden`). All off with `prefers-reduced-motion`. Headless Chrome with `--virtual-time-budget` does not run IntersectionObserver/scroll events — test motion in real time.

## Conventions
- Code comments and admin labels are in **German**; comments use the `// -> description` style, `// todo:` for placeholders.
- Field, module, CPT and taxonomy slugs are `snake_case`; partial files in backend subfolders are `_`-prefixed and required from that folder's `index.php`.
- Keep data fetching in getters/`render_modules()`; views should only output.

## Manuals
[manuals/](manuals/) contains step-by-step (German) recipes intended to be followed by humans or AI — use them for standard extensions: `module.md`, `cpt.md`, `taxonomy.md`, `menu.md`, `globals.md`, `installation.md`, `go-live-checkliste.md`. Note: the getter snippet in `cpt.md` has a stray `;` inside the array literal — don't copy it verbatim.

## WordPress setup the theme expects
- Pages: Startseite (set as static front page, module `intro`), Über mich, Sammlung (module `collection`), Impressum, Datenschutz, AGB.
- Menus: `main` (über mich, Sammlung), `legals` (Impressum, Datenschutz, AGB).
- After changing CPT/taxonomy registration: re-save Settings → Permalinks.
- Mail delivery depends on the host's `wp_mail()`; an SMTP plugin may be needed.
