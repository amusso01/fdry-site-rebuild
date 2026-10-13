# Frontend build (Vite + pnpm)

Reference for the Foundry theme frontend pipeline. Read this before adding CSS or JS.

## Override technique — do not touch old files

This theme is an old Understrap build. **Never edit compiled legacy assets**, including:

- `css/theme.css`, `css/theme.min.css`
- `css/london.css` (rules migrated to `src/styles/_overrides.scss`)
- `js/theme.js`, `js/theme.min.js`
- Page scripts under `js/` and `mainjs/` (e.g. `about.js`, `filterCategory.js`, `app.min.js`)

All **new** styling and JavaScript goes in `src/`, compiles to hashed files in `dist/` (e.g. `fdry.[hash].css`, `fdry.[hash].js`), and loads **after** the old files so cascade and load order win.

New dev PHP (menus, enqueue, helpers, theme supports) lives in [`library/function-dev.php`](library/function-dev.php), loaded from [`functions.php`](functions.php). Legacy [`inc/enqueue.php`](inc/enqueue.php) is otherwise unchanged. Only its Vanta/Three.js enqueues (`three.min.js`, `app.min.js`) were removed, together with those files, when the footer brief was rebuilt.

### Why

Safer than rewriting the 15k-line theme CSS. New work stays isolated and easy to revert.

## Folder map

```
library/
  function-dev.php   # New dev site PHP (see below)
  function-redirect.php  # Every theme 301 redirect, in fdry_redirect_moved_urls()
  function-images.php    # WebP copies of uploads and the front-end URL swap (see Images (WebP))
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
components/          # PHP partials for header-new templates (footer/ is on every page)
img/
  footer/            # Footer images, committed (e.g. brief-bg.jpg)
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
| Nav menu locations | `fdry_register_theme_menus()` | `mainmenu`, `secondarymenu`, `footermenu_1` to `footermenu_4`. Understrap's old `primary` location is no longer registered (removed from `inc/setup.php`). |
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

### Resource hints

`fdry_resource_hints()` (`wp_resource_hints`, new header only) preconnects to the third-party hosts the first screen waits on (PERFORMANCE-PLAN.md #3):

| Host | Why | `crossorigin` |
|------|-----|---------------|
| `use.typekit.net` | Font files | Yes: fonts are CORS requests |
| `p.typekit.net` | The kit CSS `@import`s `p.css`, which holds the stylesheet and every script after it | No |
| `cdn-cookieyes.com` | `banner.js` waits for DOMContentLoaded, then fetches 4 JSON files in a row | Yes: `fetch()` without credentials uses a separate connection from the script's |

- **Why these hosts start cold.** WP Rocket self-hosts the kit CSS, the CookieYes loader and jQuery in its minify cache (`/wp-content/cache/min/1/…`), so the browser only reaches these hosts late, for the files those reference.
- **The stale hint.** The same function drops core's dns-prefetch for `ajax.googleapis.com`, since nothing loads from it any more.
- **Keep the list short.** Every preconnect opens a connection early and competes for bandwidth. Add a host only if it's on the first screen's critical path.

**`gtag()` stub.** `header-new.php` defines `gtag()` (a `dataLayer` push) but no longer loads `gtag.js`: Google Ads comes from GTM. The stub is there for `mainjs/footer.js`'s old conversion calls. Remove it together with `footer.js` (PERFORMANCE-PLAN.md #4 step 5).

### Templates and headers

- New pages: `get_header( 'new' )` loads [`header-new.php`](header-new.php) (same legacy `<head>`, new header/components).
- `header-new.php` sets `FDRY_USING_NEW_HEADER` and opens `#page` + `#content`.
- [`footer.php`](footer.php) closes `#content` and `#page` when that constant is set.
- PHP partials live under [`components/`](components/) (`get_template_part()`).
- **Homepage.** WordPress ranks [`front-page.php`](front-page.php) above the template picked in the page editor, so the homepage always renders from `front-page.php`. Its sections mirror [`template-home.php`](template-home.php), and the new "Homepage" ACF group is located on both that template and `page_type == front_page`. Keep the homepage page on "NEW template homepage" anyway: that template is what hides the content editor and turns on the hero poster preload. Each Work Parallax card has an optional **Card image** (`work_image`, an image ID) that replaces the work's featured image, and `fdry_get_work_parallax_cards()` falls back to the featured image when it's empty. The work-parallax and work-row images are not preloaded and load lazily: they are below the fold, and preloading them held back the hero, fonts and JS (SEO-PLAN.md #9). The legacy front-page ACF groups are disabled (`"active": false`). To roll back, swap the `<main>` block in `front-page.php` for [`legacy-home.php`](components/page/legacy-home.php) and set Settings → Reading → Homepage back to the old page.
- **Work row.** [`work-row.php`](components/page/work-row.php) has two layouts: full bleed, or contained.
  - **Contained rows.** Pass `'contained' => true` to wrap the cards in `.content-block > .content-max`, as the homepage does. The section gets `work-row--contained`, which adds a 5px gap between the cards and 8px corners. The `inner` variant (the work row in [`centered-content.php`](components/page/centered-content.php), e.g. on Service inner) already sits in a content block. It gets the class automatically, without a second wrapper. A contained row directly after `.navigation-content` gets 35px top padding, so homepage work row 1 doesn't touch the dark band.
  - **Corners.** The corners are on `.work-row__link`, not the `<img>`. The link clips both the image and the overlay, and a radius on the image would be lost when it scales on hover.
  - **Gap on mobile.** Below tablet the slider's grid is the Swiper wrapper, so the CSS gap only applies from tablet up. [`workRowSlider.js`](src/scripts/part/workRowSlider.js) sets `spaceBetween: 5` for contained rows instead.
  - **Full-bleed rows.** Service and Service Child still use full-bleed rows, with square corners and no gap.
- **Image banners.** [`image-banner.php`](components/page/image-banner.php) reads `{prefix}_desktop_image` and `{prefix}_mobile_image` (default prefix `banner`). Service inner has two banners: Banner 1 after the 4-column grid, and Banner 2 (`'prefix' => 'banner_2'`, ACF tab "Image Banner 2") between Image Text Alternate and Two Column Content Gray. Banner 2 also passes `'appearance' => 'spaced'`, which adds `inner-image-banner--spaced`: a white band padded 40px / 72px from `tablet`, like the sections around it, so the image doesn't touch the grey row above. The default appearance has no top gap and a 32px bottom margin. A banner with no desktop image renders nothing.
- **Work URLs.** Everything lives under `/works/`: the Work page (the page on [`template-work.php`](template-work.php), slug `works`) is `/works/`, and case studies (`works_post`) are `/works/{slug}/`. Link to the page with `fdry_template_page_url('template-work.php', '/works/')` from `function-dev.php`, never a page ID or a hard-coded path: the old Work page (ID 50) is unpublished, and the page used to be `/work/`.
  - **No `works_post` archive.** The CPT has `has_archive => false`. With an archive, WordPress adds a `works/?$` rule, and CPT rules are checked before page rules, so the archive would load instead of the page. The same goes for anything else under `/works/`: the CPT single rule `works/([^/]+)/?$` comes first, so a child page of Works, or a `/works/page/N/` URL, is read as a case study and 404s. A new `/works/...` route needs its own `add_rewrite_rule(..., 'top')` on `init`, like the category rule.
  - **Old URLs.** In [`function-redirect.php`](library/function-redirect.php), `fdry_redirect_moved_urls()` 301s `/work/`, `/work/category/{slug}/` (to `/works/category/{slug}/`), `/works/feed/` and `?post_type=works_post` on `template_redirect` priority 1, before `redirect_canonical`.
  - **Breadcrumb.** With no archive, Yoast adds no post-type crumb to case studies. `fdry_works_breadcrumb()` (`wpseo_breadcrumb_links`) inserts "Work" → `/works/` after Home, and that feeds the BreadcrumbList schema too.
  - **Category pages.** `/works/category/{slug}/` also queries `works_post` (the rewrite rule in `functions.php`, added on `init` so it lands before the CPT's rules), so the redirect skips requests with a `category_name`. Those pages still use the legacy [`archive-works-category.php`](archive-works-category.php), whose intro still prints page 50's content.
- **Insights.** [`template-insight.php`](template-insight.php) ("NEW template Insight") renders [`insight-archive.php`](components/page/insight-archive.php): a 50/50 intro from the "New Insight" ACF group (`insight_tagline`, `insight_title`, `insight_content`), then every published post as an `.insight-card`, two per row, styled in [`_insight-archive.scss`](src/styles/components/_insight-archive.scss). Each card has three links to the post: the image, Read more and the title (an `h2`). The article is not wrapped in `<a>`. The image link repeats the title link, so it has `tabindex="-1"` and `aria-hidden="true"`. Images are cropped to 3:2 with 4px corners, lazy-loaded natively with `wp_get_attachment_image()`, and zoom in on hover like the work cards. The component does not use the legacy `.grid-insight` / `.insight-grid` CSS. The old page, `page-templates/parent.php` with [`content-insight.php`](loop-templates/content-insight.php), is unchanged. To roll back, set the Insights page template back to "Parent-pages".
- **Single insight.** [`single.php`](single.php) renders [`components/single/insight.php`](components/single/insight.php) for posts. Works still use the legacy `content-single-work.php`. The page is laid out as follows, styled in [`_single-insight.scss`](src/styles/components/_single-insight.scss):
  - The header is centred. It has an "Insights" eyebrow (a `<p>` link to the page using `template-insight.php`), the post title as the `<h1>`, and share icons from `svg-template/svg-{linkedin,instagram,x}.php`.
  - The hero image comes from ACF `hero_image`, falling back to the featured image. It is 600px tall with 4px corners, eager and `fetchpriority="high"`, and never faded, because it is the LCP element.
  - The post body is a 750px column: the category, then the Gutenberg content, then a back link to Insights.
  - The "Our Work" banner at the bottom also has 4px corners.
  - LinkedIn and X share the post. Instagram has no share URL, so that icon opens FDRY's profile.
  - **Legacy typography.** The Gutenberg output still sits inside `.insight-content .entry-content`, because its heading sizes and bullets come from `theme.css` and `mainstyle.css`. Those two classes are kept only as hooks. Move that typography into `src/` before dropping `theme.css`. The category is outside `.insight-content`, so the old purple `.insight-info .category` style no longer applies.
  - **Gutenberg links and images.** These are overridden in `_single-insight.scss`. Links use the `wysiwyg-link` mixin (underlined, fading to grey), which beats the purple `theme.css` link. Images get 4px corners and a 20px top margin. The margin needs `!important` to beat `mainstyle.css`'s `.aligncenter img` margin.
  - **Links.** The Insights and Work links come from `fdry_template_page_url()` in `function-dev.php`, not from hard-coded page IDs.
  - **Rollback.** The old partial is still at [`content-single-insight.php`](loop-templates/content-single-insight.php). To roll back, point the include in `single.php` back at it.
- **404.** [`404.php`](404.php) renders a real "not found" page with a 404 status. WordPress sets the status before the template loads, and Yoast adds `noindex`.
  - **No redirect to the homepage.** Every missing URL used to 301 to the homepage. Google treats that as a soft 404, it passes no link value, and it hid broken links from Search Console. When a page moves, add a 301 rule from the old URL to the new one (in the 301 Redirects plugin; enter the target as a URL, not a page, so it can't fall back to `?page_id=`). Don't redirect from the template. To roll back, re-add `header("HTTP/1.1 301 Moved Permanently"); header("Location: " . get_bloginfo('url')); exit();` above `get_header('new')`.
  - **Markup.** It's `main.error-page > .content-block > .content-max`, holding the "Oops!" `h1`, the `h2` and the homepage link. Each one fades up, 0.1 s apart.
  - **Styles.** They're in [`_error-page.scss`](src/styles/templates/_error-page.scss). The class names avoid the old `#error-404-wrapper` / `.overlay-404`, which `theme.css` still styles with a background photo and white text.
- **Single job.** [`single-job.php`](single-job.php) (the `job` CPT) is unchanged and still renders `content-page.php`. Its Gutenberg content gets the WYSIWYG styling from CSS alone, in [`_single-job.scss`](src/styles/templates/_single-job.scss). The selector is `.single-job .entry-content`, which uses the body class WordPress adds for the CPT.
  - It gets list bullets back (the reset strips them from `ul.wp-block-list`), block spacing, heading sizes and underlined links.
  - `p + p` is 15px.
  - **Editor output mixin.** The rules are the `wysiwyg-content` mixin in [`_wysiwyg.scss`](src/styles/common/_wysiwyg.scss), which `.wysiwyg` uses too. Include it to style any other editor output.
  - **Rule order.** In the mixin, the heading `margin: 0` comes before the spacing rules. Otherwise it cancels `* + h2`, and a heading after a list or another heading gets no gap.

## JavaScript modules

Vite has a **single entry**: [`src/scripts/main.js`](src/scripts/main.js).

1. Import SCSS first (so the build emits `fdry.css`).
2. Import top-level modules from `./part/` — not `../part/`.
3. Initialise everything inside one `DOMContentLoaded` handler, each through `run()`, so one module throwing can't stop the ones after it.

```js
import '../styles/main.scss'

import smoothScroll from './part/smoothScroll'
import gsapMotion from './part/gsap'
import hamburger from './part/hamburger'
import marquee from './part/marquee'

document.addEventListener('DOMContentLoaded', () => {
	run(smoothScroll)
	run(gsapMotion.init)
	run(hamburger)
	run(marquee)
})
```

**Part file rules:**

- One feature per file under `src/scripts/part/`.
- Export a default function or `{ init }` object.
- Return early if the DOM elements for that feature are not on the page.
- Sub-modules (e.g. `gsapFade.js`, `gsapParallax.js`) are imported **only** by their parent (`gsap.js`), not by `main.js`.

**`isolateGsap` must stay the first JS import in `main.js`.** GSAP plugins (ScrollTrigger, MotionPath) register themselves with `window.gsap` the moment they are imported. The theme no longer loads a GSAP of its own onto `window` (the old tech banner's CDN copy is gone), but GTM, a WordPress plugin or an old inline script can. Without the isolation our plugins would attach to that copy, our `registerPlugin` calls would do nothing, and every `scrollTrigger` in the bundle would be ignored: parallax plays on load instead of following the scroll. [`isolateGsap.js`](src/scripts/part/isolateGsap.js) hides the global while our modules load, and `main.js` restores it with `restoreGlobalGsap()` before `DOMContentLoaded`, so any code that relies on it keeps working.

If `pnpm dev` leaves `dist/` empty, check the terminal — a failed build (often a bad import path) clears `dist/` when `emptyOutDir` is true.

## Smooth scroll

[`smoothScroll.js`](src/scripts/part/smoothScroll.js) runs [Lenis](https://github.com/darkroomengineering/lenis) for wheel and trackpad scrolling. Lenis keeps the native window scroll and only eases the input, so `position: sticky`, the fixed header, `window.scrollY` and ScrollTrigger all keep working. GSAP ScrollSmoother was ruled out: it moves the page into a transformed wrapper, which breaks `position: sticky` (the `work-parallax` cards) and needs markup changes in the header and footer.

**Where it runs.** `fdry-scripts` loads on every page, legacy `header.php` included, so Lenis only starts when `<body>` has the `fdry-new-header` class. `fdry_new_header_body_class()` in [`function-dev.php`](library/function-dev.php) adds it wherever `FDRY_USING_NEW_HEADER` is set. It never starts with `prefers-reduced-motion`. Touch devices keep native scrolling (`syncTouch` is off).

**GSAP.** Lenis steps on `gsap.ticker` and calls `ScrollTrigger.update` on every scroll, so ScrollTrigger reads the smoothed position in the same frame. `gsap.ticker.lagSmoothing(0)` is set globally, as Lenis recommends.

**Feel.** Each wheel step is a 1.2 s easeOutExpo tween (`SCROLL_DURATION`, `easeOutExpo`), the same config as [lionandmason.com](https://lionandmason.com/). Raise `SCROLL_DURATION` for a longer glide. While a duration is set Lenis ignores `lerp`, and the same curve applies to `lenis.scrollTo()` (anchor links).

**Locking the page.** `overflow: hidden` does not stop Lenis, because it scrolls with `window.scrollTo()`. Overlays send an event with `{ isOpen }` in `detail` instead, and `smoothScroll.js` stops or restarts Lenis:

| Event | Sent by |
|-------|---------|
| `fdry:nav-toggle` | `hamburger.js` |
| `fdry:showreel-toggle` | `showreelModal.js` |

A new overlay that locks the page should send the same kind of event and be added to the listeners in `smoothScroll.js`. Keep its `overflow: hidden` rule as well, since that is what holds the page when Lenis is off.

**Nested scroll.** `allowNestedScroll: true` lets inner scroll areas (the nav overlay list, the CookieYes and Klaviyo pop-ups) scroll natively, including while Lenis is stopped. It checks the DOM on every wheel event. If that ever shows up in profiling, switch it off and add `data-lenis-prevent` to our own scroll containers, plus a `prevent` callback for third-party markup.

**Anchors.** Same-page `#hash` links glide to the target below the header, update the URL and move focus to the target. The skip link, links in a stopped state (e.g. inside the open menu) and links to other pages are left to the browser.

**CSS.** `lenis/dist/lenis.css` is pulled in from [`main.scss`](src/styles/main.scss). It resets the legacy `html, body { height: 100% }` from `typeformstyle.css`. `html.lenis { scroll-behavior: auto !important }` in `_reset.scss` cancels `theme.css`'s `html { scroll-behavior: smooth }`. It needs `!important` because the Table of Content block plugin prints `html{scroll-behavior:smooth!important}` inline. Left smooth, `ScrollTrigger.refresh()`, which jumps to the top and back to measure, reads the wrong scroll position and misplaces the parallax ranges.

**Scroll-driven effects.** Build them on ScrollTrigger (`scrub` for effects tied to scroll position), not on `lenis.on('scroll')`, so they behave the same where Lenis is off. `getLenis()` returns the instance, or `null` where it is off, for the rare case that needs Lenis itself (e.g. scroll velocity).

## Fade up / fade down

[`gsapFade.js`](src/scripts/part/gsapFade.js) fades elements in as they reach `top 90%` of the viewport, the same technique as [lionandmason.com](https://lionandmason.com/). It is a paused `gsap.fromTo` on `y` and `opacity`, `power3.out` over 2 s, which an IntersectionObserver plays once. Add the attributes in PHP; no JS changes are needed per component.

| Attribute | Effect | Default |
|-----------|--------|---------|
| `data-fade-up` | Rises into place from below | 50px, 2 s |
| `data-fade-down` | Drops into place from above (e.g. the header on load) | 50px, 2 s |
| `data-fade-up-delay` / `data-fade-down-delay` | Seconds before it starts | `0` |
| `data-fade-up-duration` / `data-fade-down-duration` | Seconds **added to** the 2 s base, so `".2"` is 2.2 s | `0` |
| `data-fade-up-distance` / `data-fade-down-distance` | Travel in px | `50` |
| `data-fade-up-group` | Put on a wrapper: its direct `p`, `h1`–`h6`, `ul`, `ol`, `img`, `figure`, `blockquote` and `hr` children each fade up. A value (e.g. `"0.1"`) staggers them. | no stagger |
| `data-fade-first="up\|down"` | First screen only: the same fade in CSS, from first paint. Print it with `fdry_fade_attrs()`, never by hand (see **First screen**) | — |

The duration attribute works like lionandmason's. Consecutive items with `.2`, `.4`, `.6`… start together and land one after another, which gives a cascade (their logo strip). Use `-delay` to make them start one after another instead.

Elements already past `top 90%` on load play straight away, and so do elements above the viewport (e.g. after a reload lower down).

- **First screen.** Anything that can be in the first viewport, meaning the header and the page's first section, must not wait for `gsapFade.js`. Behind the gate it stays at `opacity: 0` until the bundle has downloaded and run, and nothing above the fold paints before that. On slow 4G that held FCP on service pages at 5.8 s (PERFORMANCE-PLAN.md #2).
  - **How.** Print the attributes with `fdry_fade_attrs($first, $delay, $direction)` from [`function-dev.php`](library/function-dev.php). With `$first` true it prints `data-fade-first` and the delay as an inline `--fade-delay`. With `$first` false it prints the usual `data-fade-up` / `-delay`. A component that can be first or not passes its flag: `centered-content.php` passes `is_first`, and hands it on to its inner work row (the LCP slide on service pages).
  - **Motion.** The `fdry-fade-first-up` / `-down` keyframes in `_helper.scss` copy the GSAP fade: 50px, 2 s, `cubic-bezier(0.25, 1, 0.5, 1)` (`power3.out`). They run from first paint, with no JS. They animate the `translate` property, so a component's own `transform` is left alone, and `backwards` fill leaves no inline opacity behind.
  - **Opacity 0.01, never 0.** Chrome runs CSS opacity animations on the compositor, and it doesn't count content under opacity 0 as painted until the animation ends. Measured in Chrome 153 (12 Oct 2026):

    | Start | FCP and LCP |
    |---|---|
    | No fade | first paint |
    | Opacity 0 | after the full 2 s (plus the delay) |
    | Opacity 0.01 | first paint |

    The same applies to any CSS fade on an LCP element.
  - **What the metric means.** At 0.01 the metrics fire on the first frame, a little before the text is readable. With the power3.out curve, an element is about half visible 0.3 s in. A delayed element, such as the centred-content title at 0.5 s, is read about 0.5 s later than the metric says.
  - **Fallbacks.** The hide rule doesn't match `data-fade-first`. With reduced motion, no JS or a failed bundle, the first screen simply shows.
  - **Groups.** There is no CSS group. A first-screen `data-fade-up-group` becomes one wrapper fade, as on `template-one-column.php` and the insight archive intro.
  - **Where it's used.**
    - `header-new.php` (down);
    - the first `centered-content` (About, Services, Service child, Service inner);
    - the intros of `work-archive.php` and `insight-archive.php`, including the work results;
    - `template-contact.php`, `template-one-column.php`, `404.php` and the single insight header.
    - Not the homepage `intro-content`: the hero is `100vh`, so it is always below the fold.
  - **Rollback.** Make `fdry_fade_attrs()` return its `$first = false` branch for every call.

- **Why not ScrollTrigger.** Fades used to be ScrollTriggers. On cold, logged-in loads from wp-admin they sometimes stopped firing, and everything below the fold stayed at `opacity: 0`: a white page. ScrollTrigger works from positions it stores, which can go stale or stop updating. IntersectionObserver checks each element's real box on every scroll and reflow, whatever does the scrolling, so an element on screen can't stay at its start state.
- **Hiding before JS.** `_helper.scss` sets `opacity: 0` on these elements and on group children, but only under `html.fdry-fade`. `fdry_fade_gate()` in [`function-dev.php`](library/function-dev.php) adds that class from an inline script in `<head>`, so nothing flashes. `gsapFade.js` adds `fdry-fade-ready` once it is set up. If that has not happened by the window `load` event (bundle failed, blocked or delayed by WP Rocket), the gate comes off and the content shows without the fade. With `prefers-reduced-motion` the class is never set, and `gsap.js` skips the tweens.
- **Layout shifts.** Only parallax still uses ScrollTrigger, which measures on load and on window resize. On a cold cache the layout keeps changing after `load` (late fonts, images without dimensions). `gsap.js` refreshes ScrollTrigger once fonts are ready and, debounced, whenever the body height changes, on pages that have ScrollTriggers.
- **Transforms.** GSAP animates the inline `transform` and clears it when the tween ends, leaving `opacity: 1`. An element with its own CSS transform only conflicts during the tween itself. Never fade a `.swiper-wrapper`: Swiper moves it with an inline `transform`, which the tween clears. Fade the `.swiper` container instead, as `work-row.php` does in slider mode.
- **Content swapped by JS.** `gsapFade.js` only scans the page once. Put the fade on a container that stays, not on items that are replaced or appended later, or they stay hidden. Don't fade an element whose own `opacity` a state class changes either: the tween leaves inline `opacity: 1`, which beats the class. The Work archive fades `.work-archive__results` for both reasons: the AJAX cards, and the `is-loading` dim on `.work-archive__grid`.
- **Header.** The fade is on `.site-header__inner` (`fdry_fade_attrs(true, 0, 'down')`), **not** `.site-header`. The header's hide-on-scroll animates `.site-header`'s `transform` with a CSS transition, so a fade on the same element would fight it.

## Tech banner grid

The platform logo grid above the footer is [`components/footer/tech-banner.php`](components/footer/tech-banner.php), included from `footer.php` on every page. Its layout is in [`_tech-banner.scss`](src/styles/components/_tech-banner.scss), and [`techGrid.js`](src/scripts/part/techGrid.js) animates it.

- **Lines.** The grid is 8×3 on desktop (logos in the middle row, empty cells around them) and 2×3 below 768px. There are no borders: the grid has `gap: 1px` and a line-coloured background, so the lines are the gaps.
- **Overlay.** `techGrid.js` reads the track sizes and gaps from `getComputedStyle(grid)`. It adds a static SVG with a plus mark at every inner crossing, written once, and a `<canvas>` on top for the "comets". A comet is a short dash with a small soft glow that travels along the lines and leaves a faint trail. It copies terminal-industries.com's `sections-grid`.
- **Canvas, not animated DOM.** GSAP tweens each comet's plain state object (`{ alpha, progress }`), and one `gsap.ticker` callback draws all of them. Nothing on the page changes while they run. The first version animated SVG elements, which meant about 1,000 DOM attribute writes a second. Hotjar (on every page) records every DOM change and the Tailwind browser runtime watches them too, which is a lot of work for a tab left on the footer. **Rule for future animations:** anything that runs continuously should draw to a canvas or animate `transform`/`opacity` through CSS or WAAPI, never write DOM attributes every frame.
- **Glow.** The same glow the SVG version had: an ellipse (`GLOW_RX`×`GLOW_RY` at `GLOW_ALPHA`) blurred once into an off-screen sprite with a canvas shadow. `shadowBlur` = 2 × `GLOW_BLUR` gives the same Gaussian as the old `feGaussianBlur` σ4. Each frame stamps it turned along the path, centred on the middle of the dash, where the trail also ends. Positions and angles come from points sampled every `SAMPLE_STEP` px from a throwaway `<path>` (added, measured, removed), since a `Path2D` can't be measured. That's the only DOM change per comet. The canvas reaches `GLOW_PAD` past the grid so glows at the outer edges aren't cut off, and `.tech-grid-section` hides the overflow.
- **Random paths.** Each comet takes a random walk in grid-line indices (`randomRoute()`). It starts at an outer edge on a random visible line, steps from crossing to crossing, may turn 90° at a crossing (`TURN_CHANCE`, at most `MAX_TURNS`), and ends at an outer edge. The walk only uses the lines that are drawn, so the same code covers the 8- and 2-column layouts. It picks again (up to `ROUTE_TRIES`) if the new route would share a line with a comet already on screen, so comets rarely meet head-on.
- **At most 3 on screen.** There is one independent loop per `FIRST_DELAY` range (three). Each waits a random delay, sends a comet and, once it has faded out, waits again. The loops start at different times and overlap freely, and launches are at least `MIN_GAP` apart, so there are never more than three comets and they never pop in together. A comet held back by `MIN_GAP` always waits at least `MIN_RETRY` (50ms). Floating-point rounding can leave the remaining gap at ~1e-15, GSAP rounds that delay to 0 and can run it again in the same tick, and the first version looped there forever, which froze the tab after a while.
- **Speeds.** Each comet gets a random speed in `SPEED_MIN`–`SPEED_MAX` px/s that is at least `SPEED_GAP` px/s away from every comet already on screen, so no two move at the same pace. Delays, speeds and sizes (`DASH`, glow) are constants at the top of the file.
- **Colours.** The custom properties on `.tech-grid-section` (`--tech-grid-line`, `--tech-grid-cross`, `--tech-grid-accent`) control the colours. The canvas reads `--tech-grid-accent` when it builds, so change colours in the SCSS, not the JS.
- **Cost.** Every live comet and pending delay is kept in a set. It is paused while the grid is off screen (IntersectionObserver), so nothing runs in the background and there's no burst of backlog on return. The canvas only redraws while comets are on screen. A debounced ResizeObserver kills everything and rebuilds the SVG and canvas on the new lines, including the 8 ↔ 2 column switch. With `prefers-reduced-motion` only the plus marks are drawn.

## Footer brief

The "Let's talk / Send your brief / START" band is [`components/footer/brief.php`](components/footer/brief.php), included from `footer.php` on every page, and styled in [`_footer-brief.scss`](src/styles/components/_footer-brief.scss). It replaced the old `#wrapper-footer` markup and its Vanta HALO WebGL background.

- **Layout.** Text on the left and the START circle on the right, `space-between`, stacking below `phone`. Vertical padding is 80 / 64 / 48px (desktop / below `tablet` / below `phone`).
- **Side padding.** `content-block content-block--footer` in [`_helper.scss`](src/styles/common/_helper.scss) gives 90px on desktop, 32px below `tablet` and 25px below `phone`. Use it for the other footer sections too.
- **Background.** A static image, `img/footer/brief-bg.jpg` (2880×720, committed). Keep it under about 300 KB: it's a grainy gradient, so export it as a progressive JPG at around quality 75, which keeps the grain (the current file is mozjpeg q75, 266 KB). It's a lazy `<img>` with `object-fit: cover`. It only renders if the file exists, so without it the band shows `$color__footerBg`.
- **START.** A `$color__btn-yellow` circle linking to `/brief-1/`, with a box-shadow pulse (`footer-brief-pulse`) that stops on hover and focus and under reduced motion. It has no `id`. Target `.footer-brief__button`, not the old `#box`. Link text is “Start your brief” (uppercase in the UI) so Lighthouse’s link-text audit sees descriptive `innerText`.
- **Motion.** Each element has `data-fade-up` with a 0.1s stagger (see **Fade up / fade down**).

## Site footer

The contact block, menus, logo, social and legal links and the copyright bar are [`components/footer/site-footer.php`](components/footer/site-footer.php). `footer.php` includes it on every page, after the brief. The styles are in [`_site-footer.scss`](src/styles/components/_site-footer.scss).

- **Layout.**
  - **Top row.** The top row has "Come & say hello", the address and two buttons on the left, and the contact details column on the right.
  - **Menu grid.** Below the top row is the menu grid: **Footer menu 1** to **Footer menu 4**, one column each.
  - **Shared columns.** The top row and the menu grid share the same four columns (`repeat(4, minmax(0, 1fr))`, 32px gap). From `tablet` (1140px), the contact block spans columns 1–3 and the contact details sit in column 4, lined up with the fourth menu.
  - **Below 1140px.** The contact details stack under the contact block. The menu grid stays four columns down to 768px.
  - **Below 768px.** The menu grid is hidden and the mobile accordion takes its place. The contact details stay visible above it.
- **Contact details width.** The contact details column is at most 190px, so the address breaks after "Palace" as in the design.
- **Type.**
  - Title: 34/36px, weight 500.
  - Address: 20px, weight 400.
  - Contact details: 16px, weight 500, `line-height: normal`, 12px apart.
  - Column headings: 18px, weight 500, line-height 36px, white.
  - Menu links: 16px, weight 500, `line-height: normal`, 12px apart, muted grey turning white on hover.
  - The menu type is on the `ul`, not the links, so each `li` gets a 16px line box.
- **Menus.** All four are WordPress menus, assigned in Appearance → Menus to the `footermenu_1` … `footermenu_4` locations.
  - **Rendering.** They are printed with `wp_nav_menu()` (one level, no fallback), each in its own `<nav>`.
  - **Unassigned locations.** A location with no menu assigned is skipped, and the menus that are assigned fill the grid from the left.
- **Column headings.**
  - **Source.** Each heading is the menu's **Footer title** field (`footer_title`), from ACF group [`group_fd_footer_menu.json`](acf-json/group_fd_footer_menu.json). The field only shows on menus assigned to a footer location, and the component reads it with `fdry_get_nav_menu_acf_field()`.
  - **Empty title.** A menu with no title shows no heading, so give all four a title or the columns won't line up.
  - **Screen readers.** The title labels the `<nav>`. Without a title, the menu's name labels it.
- **Mobile accordion.** Below 768px, Footer menu 1–4 render as an accordion, one panel per menu.
  - **Files.** The markup is [`menu-accordion.php`](components/footer/menu-accordion.php), styled in [`_footer-accordion.scss`](src/styles/components/_footer-accordion.scss). [`footerAccordion.js`](src/scripts/part/footerAccordion.js) starts accordion-js on `.footer-accordion` with one panel open at a time. accordion-js's base CSS comes from `_nav-mobile.scss`.
  - **Triggers.** Each trigger is the menu's Footer title, or the menu's name when it has none.
  - **Shared data.** `site-footer.php` builds the menu list once (`$footer_menus`: location, title, label) and passes it to the partial, so the grid and the accordion always show the same menus in the same order.
  - **Menus print twice.** Each menu is printed in both places, and the copy for the other breakpoint is `display: none`, so screen readers skip it. `wp_nav_menu()` adds a suffix to the repeated `menu-{slug}` ids, so they stay unique.
  - **Fade.** The fade is on the `.site-footer__accordion` wrapper, not the panels, which accordion-js changes.
- **Contact details.** The email, phone and address are the `$contact` array at the top of the component. The address prints both under the title and in the contact details column.
- **Buttons.** Both use the BTN partial, [`components/partials/button.php`](components/partials/button.php): "Book an appointment" is `white` and "Join our newsletter" is `transparent`.
  - **Partial args.** The partial takes a `tag` arg (`'a'` or `'button'`) and a `class` arg for an extra class on the inner element.
  - **Footer overrides.** The footer drops BTN's uppercase and turns the transparent variant white.
  - **Focus ring.** A focused button shows a ring on the `.btn`, because `theme.css` strips focus outlines.
- **Newsletter.** "Join our newsletter" renders as `<button class="site-footer__newsletter">`. [`newsletterForm.js`](src/scripts/part/newsletterForm.js) finds it by that **class** and opens Klaviyo form `RdsGcP`. Keep the class if the button changes. The old `#fdryklaviyo-open-btn` id and `.fdryklaviyo-open-button` class are gone.
- **Breakpoint.** 768px (`media('<768px')`), the old Tailwind `md:`. Side padding is `content-block content-block--footer`.
- **Wrapper.** A `div` with `role="contentinfo"`, not a `<footer>`, because `mainstyle/linktree.css` hides every `<footer>` on the linktree template.
- **Spacing from legacy CSS.** The contact row's desktop padding (55px / 58px) used to come from an old `.mainfooter` rule in `mainstyle.css`. `.site-footer__top` now sets it itself.

## Tailwind (removed)

`footer.php` used to load the Tailwind browser runtime (`@tailwindcss/browser`, 282 KB of JS from jsdelivr) on every page. It is gone, so Tailwind utility classes no longer do anything. Use BEM classes and SCSS.

Its reset (Preflight) applied to every page, legacy templates included: no list bullets, no default borders on buttons, inputs and iframes, and `img`, `svg` and `video` displayed as blocks. It is kept as static CSS in [`_preflight.scss`](src/styles/common/_preflight.scss), inside `@layer base` as before, so any unlayered rule wins over it. The only other class Tailwind alone styled was `.relative` on the brief form (`page-templates/typeform.php`), which is kept in `_overrides.scss`.

## Commands

From the theme root:

```bash
pnpm install   # first time, or after package.json changes
pnpm dev       # watch mode — rebuilds dist/ on save
pnpm build     # one-off production build
pnpm perf      # measure the live site (PERFORMANCE-PLAN.md #0); see below
```

`pnpm perf [mobile|desktop|both] [paths…]` loads pages in headless Chrome over the DevTools protocol, on the throttling the plan's baseline used, and writes `build/perf/<date>-<label>-<device>.json`. Without paths it measures the plan's 8 verification pages. Add `--runs 3` for before/after numbers, `--profile` for JS time by script, `--legacy-css` for the legacy rules each page uses, and `--screenshots` for first-screen and full-page PNGs. The options are listed in the header of [`scripts/perf-measure.mjs`](scripts/perf-measure.mjs). WP Rocket keeps a separate page cache for phones, so the script requests each page once before measuring it, to measure the cached page.

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

## Images (WebP)

[`function-images.php`](library/function-images.php) gives every JPG/PNG upload a WebP copy and serves that copy on the front end. It fixes oversized uploads across the whole library and never touches the originals (PERFORMANCE-PLAN.md #1).

**Why the originals were the problem.** WordPress re-compresses the sizes it makes at upload, but it serves the uploaded file byte for byte. A page gets that file in two cases:
- **The file is smaller than the size asked for.** The first service-page slide is a 640px PNG requested as `large`.
- **It's the largest srcset candidate.** That happens on wide screens.

Templates can only choose between files that exist, so the fix has to be in the files. Tested on the live files:

| File | Original | WebP copy |
|---|---|---|
| `calling-by-fdry.png` (service-page slide, the LCP) | 564 KB | 45 KB |
| `hero-banner.jpg` (service inner banner) | 920 KB | 65 KB |

**Naming.** The copy sits next to its file as `{file}.{ext}.webp`, so `calling-by-fdry.png` becomes `calling-by-fdry.png.webp`. Keeping the extension stops `photo.jpg` and `photo.png` from clashing, and taking `.webp` off gives back the original URL.

**Quality.** `FDRY_WEBP_QUALITY` is 80.
- **No-gain files.** A copy that isn't smaller than its source is deleted, so that file keeps being served as it is.
- **Pinning the quality.** The editor resets the quality to the WebP default (86) when it changes format at save time, so `set_quality()` alone doesn't hold. `fdry_webp_editor_quality()` pins it through `wp_editor_set_quality` during each conversion.

**Conversion.**
- **New uploads.** On `wp_update_attachment_metadata`, `fdry_webp_on_metadata_update()` converts every file in the metadata that has no copy yet. Core saves the metadata after each size it makes, after Regenerate Thumbnails and after a media-library edit, so that one hook covers them all. A failure never breaks the upload.
- **Deletes.** `fdry_webp_delete_copy()` (`wp_delete_file`) deletes the copy along with its file. That covers deleted attachments, regenerated sizes and restored edits.
- **Existing library.** Run `wp fdry webp` (WP-CLI).
  - It goes through JPG/PNG attachments in ID order, 200 per query.
  - It marks each finished attachment with `_fdry_webp`, so you can stop it and run it again.
  - An attachment with a failed file isn't marked, so the next run tries it again.
  - Flags:

    | Flag | Effect |
    |---|---|
    | `--ids=1,2` | Only these attachments |
    | `--dry-run` | Count the work without writing anything |
    | `--limit=100` | Stop after 100 attachments and print the command for the next chunk (with `--after`). Use it in Plesk's WP-CLI panel, which runs over a web request and can time out |
    | `--force` | Make existing copies again (e.g. after a quality change) |
    | `--delete` | Remove every copy and marker |
  - **Plesk WP-CLI panel.** WP Toolkit → WP-CLI runs as the site's user, which is what you want: a run as root would leave copies WordPress can't delete. Type commands without the `wp` prefix (`fdry webp --limit=100`).
  - **Cut-off runs are safe.** Each copy is written as `{file}.{ext}.tmp.webp` and renamed once complete, so a timeout never leaves a half-written copy that would be served. A leftover temp file is never served, and the next conversion of that file overwrites it.
  - When it finishes, clear the WP Rocket cache.

**URL swap.** `fdry_webp_url()` returns the copy's URL when the copy exists on disk. Otherwise it returns the URL unchanged. It runs on:
- `wp_get_attachment_image_src`, for the work row, work cards and parallax (one `large` URL each);
- `wp_calculate_image_srcset`, for every srcset;
- ACF `image` and `gallery` values, for `fdry_image_parts()` and every component that reads `url`;
- Gutenberg `<img>` tags (`wp_content_img_tag`).

Hero posters are already WebP, so they and their preload stay the same.

- **Where it runs.** The swap runs on the front end and in its two AJAX actions, `fdry_load_more_works` and `load_more_posts` (`fdry_webp_ajax_actions()`).
  - **Where it never runs:** wp-admin, any other admin-ajax call, REST (the block editor), feeds and WP-CLI.
  - **Why the AJAX allowlist.** Other admin-ajax calls include the media modal, which could save a swapped URL into post content. A new front-end AJAX action that prints images must be added to the list.
- **Looking up an attachment from a URL.** A URL-format ACF image field returns the `.webp` URL, which matches no attachment. Pass it through `fdry_webp_original_url()` before `attachment_url_to_postid()`. The single insight hero missed this at first: with no ID it fell back to a bare `<img>` with no srcset, and phones downloaded the 611 KB full-size copy, which took LCP from 3.8 s to 9.6 s.
- **Site icons and social previews** stay on the JPG/PNG. `fdry_webp_original_url()` strips the `.webp` on `get_site_icon_url`, `wpseo_opengraph_image` and `wpseo_twitter_image`. The apple-touch-icon (iPhone home screen), the Windows tile and Google's search favicon expect PNG, and the icon files are only a few KB.
- **WebP only, no `<picture>` fallback.** Every browser since Safari 14 (2020) reads WebP. The markup and CSS stay the same, and Cloudflare caches one file per URL without needing `Vary: Accept`.
- **Not covered.** URLs hard-coded in legacy templates or CSS.

**Off switch.** Put `define('FDRY_WEBP', false);` in `wp-config.php` to stop both the conversion and the swap, then clear the WP Rocket cache. The copies stay on disk until `wp fdry webp --delete` removes them.

**Requirements.** The server's Imagick or GD must be able to write WebP. Check under Tools → Site Health → Info → Media handling. Without that support the CLI stops with an error, and uploads carry on as before without copies. Copies add roughly 25–35% to the size of the JPG/PNG files on disk.

**Why not a service.** Cloudflare is on the Free plan. Polish is Pro-only, and Image Transformations' 5,000 a month is too few for a library of more than 5,000 images. WP Rocket can't convert images itself, and Imagify would add a plugin and a monthly fee.

## Hero / showreel video

The hero and service showreel play a **self-hosted, muted, looping MP4** behind a poster image. There is no Vimeo and no third-party player.

### How it loads

1. The still renders as a separate `<picture class="hero-video__poster">` and is preloaded in `wp_head` (`fdry_preload_hero_poster()`, homepage only), so it paints as the LCP element. Its `<img>` is never lazy, in either variant. The Services showreel (the inline variant) sits right under the first section and is that page's mobile LCP element: lazy, it painted at 4.3 s on slow 4G, and eager it paints with the CSS (about 1.5 s). It isn't preloaded, because eager is already enough.
2. The `<video>` ships with `preload="none"`, **no `src`** and **no `autoplay`** — only `data-src-mp4` / `data-src-webm`.
3. [`heroVideo.js`](src/scripts/part/heroVideo.js) decides whether to fetch at all, then attaches `<source>` elements and calls `play()`. On `loadeddata` the video fades in **over** the still.

**The still must never be the video's `poster` attribute.** It was, once, and because `.hero-video__media` is `opacity: 0` until `.is-playing`, the poster was hidden along with the element containing it — black hero on mobile, and no early paint anywhere. A single element cannot cross-fade against its own poster.

### Mobile still

`{prefix}_poster_mobile` is optional and falls back to `{prefix}_poster`. When set it is served below the breakpoint via `<source media>`, following the `banner_mobile_image` pattern in [`image-banner.php`](components/page/image-banner.php). Worth supplying: a 16:9 still loses most of its composition cropped into a `100vh` phone viewport.

`FDRY_HERO_MOBILE_MAX_PX` (768) in [`function-dev.php`](library/function-dev.php) is the single source of truth. It feeds the `<source media>`, both preload `media` queries, and `data-mobile-max` on the section, which `heroVideo.js` reads instead of hardcoding a value — so the art-direction switch and the download gate cannot drift apart. The theme has other breakpoints (639px in image-banner, 700px in legacy `front-page.php`); those are unrelated and deliberately untouched.

It **never** downloads the video when the viewport is ≤768px, `prefers-reduced-motion` is set, or `saveData` is on — the poster stands in. The `--inline` variant (service pages) also waits until it approaches the viewport.

Omitting `autoplay` is deliberate: it overrides `preload="none"` and the browser fetches anyway, defeating all of the above.

**Keep the mobile poster small:** a WebP of 200 KB or less. In October 2026 it was a 1.46 MB PNG, which took 31 s to arrive on a simulated slow 4G phone, the first thing on screen. A portrait 1080×1920 still suits the `100vh` phone crop better than a square one.

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

1. The modal's `<video>` ships with `preload="none"`, **no `src` and no `poster`**: the poster URL is in `data-poster`. Page load downloads nothing. Browsers fetch a `poster` attribute as soon as the element exists, whatever `preload` says and even inside a closed `<dialog>`, so it can't be in the markup. It used to be, and every phone downloaded the 54 KB desktop poster for a modal it never opened (PERFORMANCE-PLAN.md #8).
2. On `pointerenter` or `focus` of the button, it sets the poster from `data-poster` and the `src` with `preload="metadata"`. The modal keeps the 16:9 desktop poster on phones too, because it matches the reel's frame. On desktop it is already cached from the hero. Chrome fetches about 0.3 MB (the index and a fraction of a second of video), then stops.
3. On click it opens the dialog and the overlay grows out of the button: a GSAP `clip-path` tween from the button's pill to the full viewport, about 0.6 s. The reel then fades in and starts 0.1 s after it is fully visible (`PLAY_DELAY`), and the hero loop pauses once the overlay covers it. iOS only allows sound from within the user gesture, so the click handler calls `play()` and pauses straight away, which unlocks the element for the later `play()`. The download starts at the click, so the reel buffers while the overlay grows. Measured in headless Chrome, playback could start about 30 ms after the click when primed, and about 290 ms when not (20 Mbps link, 40 ms latency), so the reel is normally ready before the animation ends. With reduced motion there is no animation and it plays at once.
4. On close (button, Esc or backdrop) the reel fades out and the overlay collapses back into the button. Then it removes `src` and calls `load()`, which aborts the download. A paused video would keep buffering otherwise. Reopening starts the reel from the beginning, and the hero loop resumes. Esc skips the collapse when Chrome won't let the dialog's `cancel` event be cancelled (it needs a user activation since the last one).

The 720p file is served at or below `data-mobile-max` (the same `FDRY_HERO_MOBILE_MAX_PX` cutoff as the hero), with save-data on, or when `navigator.connection.effectiveType` is 3g or slower. That API only exists in Chromium, so other browsers decide on screen size alone. Unlike the background loop, phones **do** play the full reel, since the visitor asked for it.

To replace the reel, see "Full showreel" in [`video-encode.md`](video-encode.md).

## Nav overlay videos

Each desktop menu panel can show a looping video (the `menu_video` ACF field on the menu item, rendered in [`secondary.php`](components/navigation/secondary.php)).

- The `<video>` ships with `data-src`, `preload="none"`, and **no `src` and no `autoplay`**. Either one makes the browser download the file on every page, even with the menu closed: `autoplay` overrides `preload`, as with the hero.
- [`navMenu.js`](src/scripts/part/navMenu.js) `playPanelVideos()` copies `data-src` to `src` the first time a panel is shown, when the menu opens or another parent is hovered or focused.
- It skips videos with no layout box (`getClientRects()` is empty). Below the tablet breakpoint the desktop panels are `display: none`, so phones never download them.

Measured on 1 Oct 2026 (/about/, headless Chrome), before and after:

| | Before | After |
|---|---|---|
| Page load, menu closed | 8.07 MB (6.17 MB of it video) | 2.06 MB, no video |
| Menu opened on a phone | 6.17 MB already loaded | No video |
| Menu opened on desktop | 6.17 MB already loaded | Only the active panel's file |

The files are 1080×1080 but show at 280px. Exports at 560×560 would make them much smaller.
