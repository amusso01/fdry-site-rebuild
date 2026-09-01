# Frontend build (Vite + pnpm)

Reference for the Foundry theme frontend pipeline. Read this before adding CSS or JS.

## Override technique — do not touch old files

This theme is an old Understrap build. **Never edit compiled legacy assets**, including:

- `css/theme.css`, `css/theme.min.css`
- `css/london.css` (rules migrated to `src/styles/_overrides.scss`)
- `js/theme.js`, `js/theme.min.js`
- Page scripts under `js/` and `mainjs/` (e.g. `about.js`, `filterCategory.js`, `app.min.js`)

All **new** styling and JavaScript goes in `src/`, compiles to fixed files in `dist/` (`fdry.css`, `fdry.js`), and loads **after** the old files so cascade and load order win.

New dev PHP (menus, enqueue, helpers, theme supports) lives in [`library/function-dev.php`](library/function-dev.php), loaded from [`functions.php`](functions.php). Legacy [`inc/enqueue.php`](inc/enqueue.php) is unchanged.

### Why

Safer than rewriting the 15k-line theme CSS. New work stays isolated and easy to revert.

## Folder map

```
library/
  function-dev.php   # New dev site PHP (see below)
src/
  scripts/
    main.js          # JS entry — imports SCSS + part modules
    part/            # Feature modules (hamburger, gsap, marquee, …)
  styles/
    main.scss        # SCSS entry; @use partials from here
    _overrides.scss  # Override rules (replaces css/london.css)
dist/
  fdry.css           # Compiled CSS (same filename every build — overwrite on deploy)
  fdry.js            # Compiled JS
  manifest.json      # Content hash used as ?ver= for cache busting (read by PHP)
components/          # PHP partials for header-new templates
header-new.php       # New header (legacy <head>, new body markup)
```

Add SCSS partials under `src/styles/` and import them from `main.scss`.

Add JS modules under `src/scripts/part/` and import them from `main.js` (see **JavaScript modules** below). Do not add separate `<script>` tags in PHP for bundled features.

## Library PHP — `function-dev.php`

All **new dev site (2026/27)** logic goes in [`library/function-dev.php`](library/function-dev.php). It is required once from [`functions.php`](functions.php) under `// NEW DEV SITE 2026/27 logic`.

**Do not** add new dev features to legacy `inc/` files unless they must hook into old Understrap behaviour. Keep new work isolated here so it is easy to find and revert.

### What belongs here

| Concern | Function / hook | Notes |
|---------|-----------------|--------|
| Block editor palette | `ea_setup()` | `after_setup_theme` |
| Nav menu locations | `fdry_register_theme_menus()` | `mainmenu`, `footermenu_1`, `footermenu_2` |
| Vite assets | `fdry_get_vite_assets()`, `fdry_enqueue_assets()` | Reads `dist/manifest.json` |
| ACF SVG helper | `acfFile_toSvg()` | Used by components (e.g. marquee logos) |

Add new helpers, CPTs, ACF hooks, and enqueue rules to this file as the new site grows.

### Enqueue (new dev assets)

`fdry_enqueue_assets()` reads [`dist/manifest.json`](dist/manifest.json) via `fdry_get_vite_assets()`. WordPress enqueues fixed paths with a version query string from the manifest.

| Handle           | Source            | Depends on          |
|------------------|-------------------|---------------------|
| `fdry-overrides` | `dist/fdry.css`   | `understrap-styles` |
| `fdry-scripts`   | `dist/fdry.js`    | `jquery`            |

**Cache busting:** each build writes a short content hash to `manifest.json` (e.g. `"version": "a1b2c3d4"`). WordPress enqueues `fdry.js?ver=a1b2c3d4` — browsers fetch fresh files when content changes, but filenames stay the same so FTP uploads simply overwrite the old files.

**Deploy:** upload/overwrite these three files in `dist/`:

- `fdry.css`
- `fdry.js`
- `manifest.json`

If `manifest.json` is missing (no build yet), assets are not enqueued and the site still loads.

Legacy assets remain in [`inc/enqueue.php`](inc/enqueue.php) (`understrap-styles`, `understrap-scripts`, page-specific old JS).

### Templates and headers

- New pages: `get_header( 'new' )` loads [`header-new.php`](header-new.php) (same legacy `<head>`, new header/components).
- `header-new.php` sets `FDRY_USING_NEW_HEADER` and opens `#page` + `#content`.
- [`footer.php`](footer.php) closes `#content` and `#page` when that constant is set.
- PHP partials live under [`components/`](components/) (`get_template_part()`).

## JavaScript modules

Vite has a **single entry**: [`src/scripts/main.js`](src/scripts/main.js).

1. Import SCSS first (so the build emits `fdry.css`).
2. Import top-level modules from `./part/` — not `../part/`.
3. Initialise everything inside one `DOMContentLoaded` handler.

```js
import '../styles/main.scss'

import gsapMotion from './part/gsap'
import hamburger from './part/hamburger'
import marquee from './part/marquee'

document.addEventListener('DOMContentLoaded', () => {
	gsapMotion.init()
	hamburger()
	marquee()
})
```

**Part file rules:**

- One feature per file under `src/scripts/part/`.
- Export a default function or `{ init }` object.
- Return early if the DOM elements for that feature are not on the page.
- Sub-modules (e.g. `gsapReveal.js`, `gsapParallax.js`) are imported **only** by their parent (`gsap.js`), not by `main.js`.

If `pnpm dev` leaves `dist/` empty, check the terminal — a failed build (often a bad import path) clears `dist/` when `emptyOutDir` is true.

## Commands

From the theme root:

```bash
pnpm install   # first time, or after package.json changes
pnpm dev       # watch mode — rebuilds dist/ on save
pnpm build     # one-off production build
```

`pnpm dev` runs `vite build --watch`. There is no Vite dev server; WordPress serves the built files from `dist/`.

## Git

**Committed:**

- `library/` — new dev PHP (`function-dev.php`)
- `src/` — source of truth
- `dist/` — `fdry.css`, `fdry.js`, `manifest.json` (required on server without Node)
- `package.json`, `pnpm-lock.yaml`, `vite.config.js`

**Ignored** (see [`.gitignore`](.gitignore)):

- `node_modules/` — from `pnpm install`; never commit
- `.vite/` — Vite cache
- `*.log`, `.pnpm-debug.log*`
- `.DS_Store`, `.idea/`, `*.swp`

Commit `pnpm-lock.yaml` so installs stay reproducible.

## Bundler

[Vite](https://vite.dev/) with the `sass` package. Config: [`vite.config.js`](vite.config.js).

Build output is always `fdry.js`, `fdry.css`, and `manifest.json`. A Vite plugin writes `manifest.json` with a content hash used as the WordPress `?ver=` parameter — overwrite the same three files on each deploy.
