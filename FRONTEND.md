# Frontend build (Vite + pnpm)

Reference for the Foundry theme frontend pipeline. Read this before adding CSS or JS.

## Override technique — do not touch old files

This theme is an old Understrap build. **Never edit compiled legacy assets**, including:

- `css/theme.css`, `css/theme.min.css`
- `css/london.css` (rules migrated to `src/styles/_overrides.scss`)
- `js/theme.js`, `js/theme.min.js`
- Page scripts under `js/` and `mainjs/` (e.g. `about.js`, `filterCategory.js`, `app.min.js`)

All **new** styling and JavaScript goes in `src/`, compiles to hashed files in `dist/` (e.g. `fdry.[hash].css`, `fdry.[hash].js`), and loads **after** the old files so cascade and load order win.

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
  fdry.[hash].css    # Hashed CSS (filename changes when content changes)
  fdry.[hash].js     # Hashed JS
  .vite/
    manifest.json    # Maps entry to current hashed filenames (read by PHP)
components/          # PHP partials for header-new templates
header-new.php       # New header (legacy <head>, new body markup)
media/
  showreel-*.mp4     # Full showreel for the modal (gitignored, deployed by hand)
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
| Vite assets | `fdry_get_vite_assets()`, `fdry_enqueue_assets()` | Reads `dist/.vite/manifest.json` |
| ACF SVG helper | `acfFile_toSvg()` | Used by components (e.g. marquee logos) |

Add new helpers, CPTs, ACF hooks, and enqueue rules to this file as the new site grows.

### Enqueue (new dev assets)

`fdry_enqueue_assets()` reads [`dist/.vite/manifest.json`](dist/.vite/manifest.json) via `fdry_get_vite_assets()`. WordPress enqueues the current hashed filenames with no `?ver=` query string — the hash in the filename busts caches (including WP Rocket).

| Handle           | Source                 | Depends on          |
|------------------|------------------------|---------------------|
| `fdry-overrides` | `dist/fdry.[hash].css` | `understrap-styles` |
| `fdry-scripts`   | `dist/fdry.[hash].js`  | `jquery`            |

**Cache busting:** each build emits new hashed filenames. PHP reads the manifest to resolve the current paths. Upload the new hashed files plus `dist/.vite/manifest.json`; remove old `fdry.*` files from the server when convenient.

**Deploy:** after `pnpm build`, upload/commit from `dist/`:

- `fdry.[hash].css`
- `fdry.[hash].js`
- `.vite/manifest.json`

The full showreel files in `media/` are gitignored, so they are uploaded separately and only when the reel changes (see **Showreel modal** below).

If the manifest is missing (no build yet), assets are not enqueued and the site still loads.

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
- `dist/` — hashed assets + `.vite/manifest.json` (required on server without Node)
- `package.json`, `pnpm-lock.yaml`, `vite.config.js`

**Ignored** (see [`.gitignore`](.gitignore)):

- `node_modules/` — from `pnpm install`; never commit
- `.vite/` — Vite cache
- `*.log`, `.pnpm-debug.log*`
- `.DS_Store`, `.idea/`, `*.swp`
- `build/` — video masters and encoder output
- `media/*.mp4` — the full showreel, deployed to the server by hand

Commit `pnpm-lock.yaml` so installs stay reproducible.

## Bundler

[Vite](https://vite.dev/) with the `sass` package. Config: [`vite.config.js`](vite.config.js).

Build output is hashed `fdry.[hash].js`, `fdry.[hash].css`, and `dist/.vite/manifest.json`. WordPress reads the manifest to enqueue the current filenames — no query-string cache busting, so CDN and WP Rocket pick up new assets automatically.

## Hero / showreel video

The hero and service showreel play a **self-hosted, muted, looping MP4** behind a poster image. There is no Vimeo and no third-party player.

### How it loads

1. The still renders as a separate `<picture class="hero-video__poster">` and is preloaded in `wp_head` (`fdry_preload_hero_poster()`, homepage only), so it paints as the LCP element.
2. The `<video>` ships with `preload="none"`, **no `src`** and **no `autoplay`** — only `data-src-mp4` / `data-src-webm`.
3. [`heroVideo.js`](src/scripts/part/heroVideo.js) decides whether to fetch at all, then attaches `<source>` elements and calls `play()`. On `loadeddata` the video fades in **over** the still.

**The still must never be the video's `poster` attribute.** It was, once, and because `.hero-video__media` is `opacity: 0` until `.is-playing`, the poster was hidden along with the element containing it — black hero on mobile, and no early paint anywhere. A single element cannot cross-fade against its own poster.

### Mobile still

`{prefix}_poster_mobile` is optional and falls back to `{prefix}_poster`. When set it is served below the breakpoint via `<source media>`, following the `banner_mobile_image` pattern in [`image-banner.php`](components/page/image-banner.php). Worth supplying: a 16:9 still loses most of its composition cropped into a `100vh` phone viewport.

`FDRY_HERO_MOBILE_MAX_PX` (768) in [`function-dev.php`](library/function-dev.php) is the single source of truth. It feeds the `<source media>`, both preload `media` queries, and `data-mobile-max` on the section, which `heroVideo.js` reads instead of hardcoding a value — so the art-direction switch and the download gate cannot drift apart. The theme has other breakpoints (639px in image-banner, 700px in legacy `front-page.php`); those are unrelated and deliberately untouched.

It **never** downloads the video when the viewport is ≤768px, `prefers-reduced-motion` is set, or `saveData` is on — the poster stands in. The `--inline` variant (service pages) also waits until it approaches the viewport.

Omitting `autoplay` is deliberate: it overrides `preload="none"` and the browser fetches anyway, defeating all of the above.

### Encoding a new video

See [`video-encode.md`](video-encode.md). It covers running `pnpm encode`, where masters and output go, which file goes in which ACF field, and why the site serves MP4 only.

### Upload guard

Content editors have no access to the encoder, so `fdry_validate_background_video()` blocks uploads that would undo the above and returns a copy-pasteable ffmpeg brief (`fdry_video_encode_instructions()`) to forward to a developer. It rejects files over 30 MB, MP4s without faststart, and anything above 10 Mbps.

`fdry_inspect_mp4()` parses the MP4 container in plain PHP — the host has no ffmpeg — and **fails open**: anything it cannot parse is allowed through rather than blocking a valid upload.

`fdry_validate_poster_present()` requires a poster wherever a video is set, since the poster is what makes the whole approach work.

### Showreel modal

The showreel button opens the full reel (with sound) in a native `<dialog>`. It is the same on the homepage hero and the service page showreel, because both render [`hero-video.php`](components/page/hero-video.php).

There is **one site-wide reel and no ACF field**. `FDRY_SHOWREEL_BASENAME` in [`function-dev.php`](library/function-dev.php) points at `media/showreel-2026` in the theme root. `fdry_showreel_sources()` resolves `{name}.mp4` (1080p) and `{name}-720.mp4`, returning only files that exist on disk. **If the 1080p file is missing, the button does not render**, so a missed upload never shows a button that opens a broken player.

It is a single faststart MP4, not HLS. For a 1–3 minute reel, HLS's only real gain is dropping quality mid-play on a weak connection. It would cost hls.js, a switch of the whole bundle to `type="module"`, two playback paths (native in Safari, hls.js elsewhere), CDN and MIME config, and hundreds of files per reel. It would also risk the soft start that got Vimeo removed. Revisit HLS if the reel goes past about 5 minutes or 4K, or if analytics show many viewers on weak connections.

How [`showreelModal.js`](src/scripts/part/showreelModal.js) loads it:

1. The modal's `<video>` ships with `preload="none"` and **no `src`**. Page load downloads nothing.
2. On `pointerenter` or `focus` of the button, it sets `src` with `preload="metadata"`. Chrome fetches about 0.3 MB (the index and a fraction of a second of video), then stops.
3. On click it opens the dialog, pauses the hero loop, and calls `play()` **inside the click handler**, because iOS only allows sound from within the user gesture. Measured in headless Chrome, playback starts about 30 ms after the click when primed, and about 290 ms when not (20 Mbps link, 40 ms latency).
4. On close (button, Esc or backdrop) it saves the position, removes `src` and calls `load()`, which aborts the download. A paused video would keep buffering otherwise. Reopening resumes from the saved position, and the hero loop resumes.

The 720p file is served at or below `data-mobile-max` (the same `FDRY_HERO_MOBILE_MAX_PX` cutoff as the hero), with save-data on, or when `navigator.connection.effectiveType` is 3g or slower. That API only exists in Chromium, so other browsers decide on screen size alone. Unlike the background loop, phones **do** play the full reel, since the visitor asked for it.

To replace the reel, see "Full showreel" in [`video-encode.md`](video-encode.md).
