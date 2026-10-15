# Performance plan: new (2026/27) templates

> **Status:** first recorded 12 October 2026. #0 done: `pnpm perf`. In progress: #1 done (12 Oct): service-page mobile LCP 11.3 s → 1.7 s. #2 done (12 Oct): mobile FCP 1.55–1.91 s on all 8 verification pages (`build/perf/2026-10-12-2305-after-2-mobile.json`). Measured against the live site (www.fdry.com) and the code on `main` (74c3662).
> Overlaps with `SEO-PLAN.md` are marked **(SEO #n)**. Where this plan re-ranks an SEO item, this plan wins, because it has the measurements.

## Context

The site now runs on `header-new.php`, `components/`, `library/function-dev.php` and the Vite bundle in `src/`. **All 37 pages in `page-sitemap.xml` use a new template** (checked from live body classes). Only `/brief/` (`page-templates/typeform.php`) still renders legacy content inside the new header. Off the sitemap, the brief steps (`brief-1/2/final`), work case studies (`content-single-work.php`) and work category pages (`archive-works-category.php`) are still legacy.

Even so, every page still loads the full legacy front end (Understrap CSS, jQuery, Bootstrap JS) and an uncoordinated set of marketing tags. The new code itself is in good shape. Most of the cost comes from what loads around it.

### How it was measured

- Headless Chrome 153 over the DevTools protocol, cold cache, first visit (so the cookie banner shows).
- **Mobile:** 412×823, Lighthouse "slow 4G" (150 ms, 1.6 Mbps) and 4× CPU throttling. **Desktop:** 1440×900, 10 Mbps, 40 ms.
- Includes CSS rule coverage and a main-thread CPU profile.
- PageSpeed Insights' API had no quota, so there is **no field (CrUX) data** here. Check Search Console → Core Web Vitals before starting (see Verification).
- TBT is approximated from long tasks after FCP, so Lighthouse will report different values. The ranking between pages holds.

### Baseline (12 Oct 2026)

Thresholds: LCP good ≤ 2.5 s, poor > 4 s.

| Page | Mobile FCP | Mobile LCP | Mobile LCP element | Mobile TBT | Desktop LCP | Page weight (mobile) | Third-party share |
|---|---|---|---|---|---|---|---|
| Home | 2.1 s | **7.3 s** | CookieYes banner text | 1.5 s | 1.5 s | 1.85 MB | 1.0 MB |
| Service inner (`/services/shopify-agency/`, same template as 21 others) | **5.8 s** | **11.3 s** | work-row slide, a 551 KB PNG | 1.0 s | 1.0 s | 2.0 MB (desktop 2.9 MB) | 1.07 MB |
| Works | 2.7 s | 3.9 s | first work card | 0.85 s | 1.2 s | 2.6 MB | 1.0 MB |
| Single insight | 2.1 s | 3.8 s | hero image | 0.85 s | 1.2 s | 1.4 MB | 1.0 MB |
| About | 2.8 s | 3.3 s | centred-content title | 0.8 s | 1.1 s | 1.7 MB | 1.0 MB |
| Insights | 2.7 s | 2.7 s | intro text | 1.0 s | 1.6 s | 1.6 MB | 1.0 MB |
| Contact | 2.0 s | 2.7 s | title | 0.87 s | 0.45 s | 1.6 MB | 1.0 MB |

CLS is 0–0.03 everywhere. TTFB is 60–250 ms (WP Rocket page cache).

### What every new-template page loads before it can paint

| Resource | Size (brotli / raw) | Used on new templates |
|---|---|---|
| `css/theme.min.css` (Understrap) | 57 KB / 464 KB | **0.8 %** |
| `mainstyle/mainstyle.css` | 10 KB / 51 KB | **0 %** |
| `mainstyle/typeformstyle.css` | 2 KB / 9 KB | 0.7 % |
| `dist/fdry.[hash].css` | 15 KB / 93 KB | 55–62 % |
| Typekit CSS → `p.typekit.net/p.css` (chained `@import`) | 1 KB | fonts |
| jQuery 3.1.1, in `<head>`, synchronous | 29 KB | only by `mainjs/footer.js` and a dead inline script |
| CookieYes `script.js`, synchronous | 9 KB | needed first, for consent blocking |

Then, at the end of `<body>`, and all synchronous: `lozad.min.js`, `theme.min.js` (Bootstrap and Understrap, 20 KB br), our bundle (81 KB br, 248 KB raw) and `mainjs/footer.js`.

### Where the main-thread time goes

Home and Shopify, mobile, first 25 s, JS self time:

| Script | Home | Shopify |
|---|---|---|
| Google tags (`gtag/js` ×3 + `gtm.js`) | 515 ms | 472 ms |
| Our bundle (`fdry.[hash].js`) | 162 ms | 241 ms |
| CookieYes | 87 ms | 77 ms |
| jQuery, `theme.min.js`, Meta CAPI | ~60 ms | ~55 ms |

Parse and compile time is on top of this (about 2.5 s of unattributed "program" time per page).

## Already good — keep

- **No layout shift:** CLS is about 0 on every page measured.
- **Video loads on demand.**
  - The hero video never downloads on phones.
  - The nav videos load when the menu opens.
  - The full showreel loads on intent.
- **Homepage hero poster.** The preload is media-scoped, and the mobile poster is now a 157 KB WebP (SEO #9 CMS step done).
- **Static assets.** Hashed filenames, 1-year `cache-control`, Cloudflare HIT and brotli.
- **Page cache.** WP Rocket's cache keeps TTFB low.
- **Tech grid.** It is a canvas, paused off screen.
- **Single insight hero.** It is eager with `fetchpriority="high"`, and the insight archive uses srcset.
- **DOM size.** At most about 1,500 nodes. Emojis and Gutenberg styles are already disabled.

## Priority legend

| Priority | Meaning |
|---|---|
| **P0 · Critical** | Makes a key page's mobile LCP or FCP "poor" today. Fix first. |
| **P1 · High** | Clear Core Web Vitals or weight win on every page. Next sprint. |
| **P2 · Nice to have** | Real but smaller gain, or needs the P0/P1 work first. |
| **P3 · Optional** | Polish, tooling or future-facing. |

## Priority list

| # | Priority | Issue | Type | Effort | Status |
|---|---|---|---|---|---|
| 0 | — | Write and commit a measurement script, so each item can be measured before and after. The baseline was taken with throwaway tools. | Code | S | Done (12 Oct): `pnpm perf` |
| 1 | **P0** | **Oversized uploads are the LCP image.** The service-page slide is a 551 KB PNG (11.3 s LCP). The banner is a 1376×600 JPG of 899 KB. Nothing converts uploads to WebP/AVIF. | Code + CLI | M | Done 12 Oct: library converted (3.2 GB → 628 MB in the main run); service mobile LCP 11.3 s → 1.7 s |
| 2 | **P0** | **The first screen is hidden until our JS runs.** The header (`data-fade-down`) and the first section fade in from `opacity: 0`, so FCP waits for the bundle: 5.8 s on service pages. (SEO #17, was P2) | Code | S | Done 12 Oct. Mobile FCP 1.55–1.91 s on all 8 pages. Service inner FCP 5.8 → 1.76 s, LCP 11.3 → 1.76 s (with #1) |
| 3 | **P0** | **Homepage LCP is the cookie banner (7.3 s).** CookieYes waits for DOMContentLoaded, which waits for our bundle, which is starved of bandwidth by the tags, fonts and legacy CSS. | Code + CMS | S (result depends on #4, #5) | Live 12 Oct (preconnects, inline Ads tag out). Home mobile LCP 6.05 → 5.72 s, DOMContentLoaded 5.17 → 4.78 s. Still the banner: needs #4 step 3 and the `btn-circle.svg` fix |
| 4 | **P1** | **Legacy CSS/JS on new templates**: `theme.min.css`, `mainstyle.css`, `typeformstyle.css`, jQuery in `<head>`, `theme.min.js`, `lozad`, `footer.js`, plus TweenMax 2 and `js/about.js` on /about/. (SEO #18, #23) | Code | L | Steps 1–3 and 5 (CSS and JS) live 14 Oct. Mobile FCP 1.6–2.0 → 0.73–0.88 s on all 7 new pages, render-blocking 8 → 4, CSS 85 → 16 KB, Home LCP 4.86 → 3.88 s. The tags now start earlier and compete (Services LCP 1.72 → 2.36 s). `/works/` first card eager (live 14 Oct): mobile LCP 3.26 → 2.01 s. Next: posts (step 4), dead code |
| 5 | **P1** | **Marketing tags: ~1 MB and ~0.5 s of main thread per page.** The Ads tag loads twice, there are two GA4 IDs, the dead Universal Analytics `analytics.js` still loads, and Hotjar, Apollo and Meta are hard-coded. (SEO #25, was P3) | GTM + Code | M | Step 1 live (12 Oct, with #3): one Ads `gtag/js`, −197 KB third-party; Ads hits and conversions checked |
| 6 | **P1** | **Fonts.** `theme.min.css` adds its own old Proxima Nova faces, so 400 downloads twice and 400 italic once more. There was no preconnect. (`font-display` is already `swap` on live via WP Rocket; the kit's unused weights cost no bytes.) | Adobe + Code | S | Step 3 (preconnect) live 12 Oct. Old faces gone on new pages with #4 (14 Oct): 6–7 files (184–216 KB) → 5 (150 KB). Step 1 (Adobe project) open |
| 7 | **P1** | **No srcset on work images.** The work row, work cards and parallax request `large` with no srcset. Parallax card 1 (`NUYU.jpg`, 315 KB) loads at 2.4 s on phones. (SEO #16, was P2) | Code | M | Open |
| 8 | **P2** | **Showreel modal poster.** The modal's `<video poster>` downloads the desktop poster (55 KB) on every page with a showreel, phones included, with the modal closed. | Code | S | Done 12 Oct: Home mobile LCP 5.75 → 4.5–4.9 s, DOMContentLoaded 4.81 → 3.5–3.9 s. The lazy Services showreel poster it exposed is fixed (Services mobile LCP 1.72 s) |
| 9 | **P2** | **Marquee SVGs are 116 KB of the 240 KB homepage HTML.** The 9 logos are printed twice and not optimised. | Code | S | Open |
| 10 | **P2** | **One 248 KB bundle on every page.** Swiper, the showreel, the work archive and the tech grid load everywhere. `smoothscroll-polyfill` is obsolete. | Code | M | Open |
| 11 | **P2** | **WP Rocket.** Turn on "Load JS deferred" and "Delay JS" for non-essential tags, after #4 and #5. Clear priority elements. (SEO #18) | CMS | S | Open |
| 12 | **P2** | **Upload guard for images** (size and dimensions), like the existing video guard, so #1 doesn't come back. | Code | S | Open |
| 13 | **P3** | **HTML is not cached at the edge** (`cf-cache-status: DYNAMIC`): use Cloudflare APO or a cache rule. Measure TTFB from the UK and US first. | CMS | M | Open |
| 14 | **P3** | **Nav overlay videos** are 1080×1080 but show at 280px. Export at 560×560. | Content | S | Open |
| 15 | **P3** | **Small leftovers.** `global-styles` inline CSS on ACF-only templates, a speculation-rules overlap with WP Rocket preload, and uncached queries in `fdry_template_page_url()` and `fdry_work_categories()`. | Code | S | Open |

**Expected outcome once P0 and P1 are done:**
- Mobile FCP drops from about 2–2.8 s to about 1–1.3 s on every page. Legacy CSS alone ends at about 1.9 s on slow 4G.
- Service-page LCP drops from 11.3 s to under 3 s.
- Homepage LCP drops from 7.3 s to under 3 s, to be confirmed with the measurement script after each item.
- Page weight drops by about 0.5–0.8 MB.

---

## #0 Measurement script

**Status: done (12 Oct 2026).** [`scripts/perf-measure.mjs`](scripts/perf-measure.mjs), run as `pnpm perf`. Its header lists every option.

```bash
pnpm perf                                   # mobile, the 8 verification pages
pnpm perf both --runs 3 --label before-4    # mobile and desktop, median of 3 loads
pnpm perf desktop /about/ /works/ --legacy-css --screenshots
```

- **Output.** A summary table in the terminal, and `build/perf/<date>-<label>-<device>.json`. `build/` is gitignored.
- **Default.** Each page gets:
  - FCP, LCP and the LCP element, CLS, approximate TBT, TTFB, DOMContentLoaded and load;
  - the render-blocking resources (from `renderBlockingStatus`) and the synchronous scripts;
  - the 15 largest requests, the full request list (each with its initial priority, and `start`/`end` in ms from navigation), and page weight split first party / third party and by type;
  - CSS coverage per stylesheet.
- **Element tags.** The LCP element is tagged `[fade-first]` or `[gsap fade]` when it sits inside a fade.
- **`--profile`.** Main-thread JS self time by script, from load to the reading. Each `gtag/js?id=` is a separate row. It costs a little: FCP about 0.1 s later, TBT about 20 ms higher.
- **`--legacy-css`.** The used rules of `theme.min.css`, `mainstyle.css` and `typeformstyle.css`, each with its `@media` condition. The JSON's `legacyCssSummary` lists every rule with the pages that use it, i.e. Appendix A.
- **Simulating a change on live (added 13 Oct, #4).** These options load the live pages as if a change were already deployed:
  - **`--block <pattern>`** (repeatable, `*` wildcards): blocks matching requests, which simulates a dequeue.
  - **`--inject-css <file>`:** adds a local CSS file, e.g. a fresh `dist/fdry.*.css` (block the live one too).
  - **`--add-class <names>`:** adds classes to `<body>`, as a PHP change would.
  - **`--reduced-motion`:** emulates `prefers-reduced-motion`, so fades, the hero video and CSS animations hold still and screenshots repeat exactly. Two identical runs diff at 0.00%.
- **`pnpm perf:diff <a.json> <b.json>`** ([`scripts/perf-diff.mjs`](scripts/perf-diff.mjs)) compares two runs' full-page screenshots.
  - **Output:** the % of changed pixels, the page heights, and the bands of changed rows per page. It writes red-overlay images to `build/perf/diff/`.
  - **Reading it:** a shift high up moves everything below it, so the first band shows where a change starts.
  - The PNG helpers both scripts use are in [`scripts/lib/png.mjs`](scripts/lib/png.mjs).
- **`--screenshots`.** A first-screen PNG, plus a full-page PNG taken after the scroll, for #4's visual diff.
  - The full page is stitched from viewport tiles. Chrome's own full-page capture never returns on the homepage once it has been scrolled.
  - Fixed elements (the header and the cookie banner) only appear at the top of the full-page image.
- **How it loads.** Same settings as the baseline: a cold first visit in a fresh headless Chrome profile, so the cookie banner shows.
  - **Mobile:** 412×823 at DPR 1.75, slow 4G (150 ms, 1.6 Mbps) and 4× CPU.
  - **Desktop:** 1440×900, 10 Mbps, 40 ms.
- **Mobile user agent.** It is a phone user agent that keeps "HeadlessChrome", so analytics can filter the visits out.
- **Warm page cache.** Before each load the script requests the page once from Node, with the same user agent. `--no-warm` skips this.
  - **Why.** WP Rocket keeps a **separate cache for phones**, and nothing fills it after a purge until a phone visits. A cold page added 0.7–1.7 s of TTFB in testing.
  - **Baseline caveat.** The baseline used Chrome's desktop user agent, so it always hit the desktop cache.
- **CSS coverage.** It starts only after the metrics are read, then the page is scrolled to the bottom, unthrottled.
  - **Why.** Tracking rule usage during the load made TBT about 5× higher (0.25 → 1.2 s on the service page) and FCP about 0.1 s later.
  - **Side effect.** Rules used only while the page loads aren't counted, so our own CSS reads about 2 points lower. The legacy sheets are unaffected.
- **Runs on.** The local Chrome (or `CHROME_PATH`), over the DevTools protocol, with Node 22's built-in WebSocket and `zlib`. No npm dependencies.
- **Before/after:** run it before and after every item below, and record the numbers in that item's status line. Use `--runs 3` for those: single mobile loads vary by about ±0.1–0.2 s.

## P0: Critical

### #1 Oversized uploads (CMS + plugin)

**Evidence (live, 12 Oct):**

| File | Where | Size | Notes |
|---|---|---|---|
| `2023/05/calling-by-fdry.png` | Service inner work-row slide 1. **LCP on all service inner pages** (High priority, eager) | 551 KB | PNG of a photo |
| `2026/09/hero-banner.jpg` | Service inner image banner (desktop) | 899 KB | Only 1376×600, so badly over-compressed |
| `2025/12/Thumbnail.jpg` | Works grid | 436 KB | Original, below `large` |
| `2025/06/NUYU.jpg` | Homepage parallax card 1 | 315 KB | Loads at 2.4 s on phones |
| `2025/11/image-3-edited-768x432.png` | Insights card | 217 KB | PNG |

- Of the upload URLs on the 6 main pages, 236 are JPG, 140 PNG and 9 WebP.

**Cause.** WordPress re-compresses the sizes it makes at upload (`hero-banner-1024x447.jpg` is 75 KB), but serves the uploaded file byte for byte. A page gets that file when it's smaller than the size asked for: `calling-by-fdry.png` is 640×650, requested as `large`. It also gets it when it's the largest srcset candidate, as with the banner's 1376px original on a 1440px screen. So srcset (#7) can't fix this. Only re-encoding the files can.

**Decision (12 Oct 2026): WebP copies made by the theme.** The ruled-out options:
- **Cloudflare Polish:** Pro-only, and we're on Free.
- **Cloudflare Image Transformations:** 5,000 a month, too few for a library of more than 5,000 images.
- **Imagify:** a plugin plus a monthly fee.
- **WP Rocket:** can't convert images.
- **Re-exporting by hand:** too many files.

**Fix (done in code):** [`library/function-images.php`](library/function-images.php). See FRONTEND.md → Images (WebP).
- **Copies.** Every JPG/PNG upload gets a `{file}.{ext}.webp` copy at q80, made with WordPress's own image editor. New uploads are converted automatically.
- **Swap.** The front end swaps the URL when the copy exists. That covers attachment src, srcset, ACF image and gallery values, and Gutenberg images.
- **The rest.** No plugin, no quota, and the originals are untouched. `FDRY_WEBP` in `wp-config.php` turns it all off.
- **Tested** on a local WordPress 7.1 with GD:
  - The slide drops from 564 KB to 45 KB.
  - The banner original drops from 920 KB to 65 KB.
  - The whole test set is −89%.

**Rollout (live):**
1. **Check WebP support.** In Site Health → Info → Media handling, Imagick or GD lists WEBP. Also check that the Plesk disk quota has room for about 30% more on top of the JPG/PNG volume.
2. **Deploy** `function-images.php` and `functions.php`. No build is needed. Nothing changes until copies exist.
3. **Fix the LCP files first.** Run `wp fdry webp --ids=<calling-by-fdry ID>,<hero-banner ID>`, clear the WP Rocket cache, then check the service page.
4. **Convert the library.** Run `wp fdry webp` off-peak (resumable), then clear the WP Rocket cache. Check `--dry-run` first to see the size of the job. In Plesk's WP-CLI panel, which can time out, run it in chunks with `fdry webp --limit=100`, then each "Next chunk" command it prints.
5. **Record the numbers.** Write the CLI summary and the before/after LCP into this item's status.

**Result (live, 12 Oct 2026):**
- **Run.** Done from Plesk's root SSH Terminal, as the site's user and with Plesk's PHP 8.3 (Imagick):
  ```
  sudo -u elegant-carver_fk7go98rxmv bash -c 'nohup nice -n 10 /opt/plesk/php/8.3/bin/php -d max_execution_time=0 /usr/local/bin/wp --path=/var/www/vhosts/fdry.com/httpdocs fdry webp > /tmp/fdry-webp.log 2>&1 &'
  ```
  - The first attempt stopped after 300 s at 6,574 copies: Plesk's php.ini sets `max_execution_time` for the CLI too. The command now calls `set_time_limit(0)` itself.
- **Library.** 4,157 attachments and 19,651 files. The second run converted 12,794 files, **3.2 GB → 628 MB (−81%)**.
  - 181 files were kept as originals, because the WebP wasn't smaller.
  - 12 failed: truncated PNG sub-sizes from 2018–2023 ("insufficient image data"). They're served as before.
  - Disk: well under 1 GB added, out of 17.7 GB free.
- **Pages.** On Home, Shopify, Works, About and Insights, desktop and mobile, there are 0 JPG/PNG upload references left. WP Rocket's preloads point at `.webp`.
  - The service-page slide went from 564 KB to 45 KB.
- **Found during rollout:**
  - **WP Rocket Priority Elements.** WP Rocket kept preloading the old PNG (`data-rocket-preload`) until its Priority Elements were cleared, which is separate from clearing the cache.
  - **Site icons.** These were swapped too, so `get_site_icon_url` now keeps them PNG.
- **Re-measure (12 Oct 2026, evening).** Headless Chrome 153, cold cache, the same conditions as the baseline: mobile slow 4G with 4× CPU, desktop 10 Mbps.
  - **Integrity.** All 329 unique WebP copies referenced on 10 pages return `200 image/webp` with a valid header.
  - **Broken images.** After scrolling every page, there's one, and it isn't from this item: a deleted attachment (14079, `2026/03/image-3-edited.png`) is still in the content of "Inside our UI toolkit". Re-insert it in the editor.

  | Page | Mobile LCP (before → after) | LCP element | Images on first load (was → now) |
  |---|---|---|---|
  | `/services/shopify-agency/` | 11.3 s → **1.7 s** (FCP 5.8 → 1.6 s, #2 also live) | the slide, `calling-by-fdry.png.webp` | 669 → 114 KB (mobile), 1,567 → 178 KB (desktop) |
  | `/services/adobe-commerce-agency/` | — → 2.3 s | title (desktop: work-row image, 1.4 s) | 739 → 141 KB (desktop) |
  | `/works/` | 3.9 s → 4.1 s | first work card, 53 KB WebP | 1,368 → 255 KB |
  | `/` | 7.3 s → 6.4 s | CookieYes banner text (#3) | 651 → 367 KB |
  | `/about/` | 3.3 s → 2.4 s | title | 511 → 391 KB |
  | `/insights/` | 2.7 s → 1.6 s | intro text | 587 → 146 KB (whole page 13 MB → 2.9 MB) |
  | A single insight | 3.8 s → 1.9 s | paragraph (desktop: hero WebP, 0.5 s) | 536 → 252 KB |
  | A case study (`/works/nikki-makeup/`, legacy) | — → 2.6 s | heading | 5.7 MB → 1.1 MB |

  - **"Was" numbers.** They're the original JPG/PNG sizes of the same files the browser downloaded.
  - **Remaining LCP problems aren't image weight:**
    - **Homepage:** the cookie banner, #3.
    - **Works:** the cards wait for the JS fade on `.work-archive__results`, which is #2's territory, and #7 for srcset.

**Regression found on live (12 Oct), fixed in code, deploy pending.**
- **What happened.** `hero_image` on single insights is a URL-format ACF field, so the swap hands `components/single/insight.php` the `.webp` URL. `attachment_url_to_postid()` can't resolve that URL, so the hero fell back to a bare `<img>` with no srcset, and phones downloaded the 611 KB full-size `-scaled.jpg.webp`. Mobile LCP went from 3.8 s to 9.6 s.
- **Fix.** Look up `fdry_webp_original_url($hero_url)` instead.
- **Rule.** FRONTEND.md → Images (WebP) now has the rule for any URL → attachment lookup.

**Follow-ups:** #7 (srcset on work images) still matters for choosing the right size. #12 (upload guard) still stops 5 MB uploads.

**Verify:**
- **Markup.** On `/services/shopify-agency/` the work-row `<img src>` ends in `calling-by-fdry.png.webp`, and the banner srcset entries end in `.webp`.
- **Delivery.** `curl -sI` on the `.webp` returns `200` and `content-type: image/webp`.
- **LCP.** On mobile, the LCP image is ≤ 150 KB, and LCP is < 4 s before #2 and < 3 s after.
- **Social previews.** `og:image` is still a JPG/PNG.
- **wp-admin.** The media library and the block editor still show the original URLs.

### #2 First screen hidden until JS runs (supersedes SEO #17)

**Evidence:**
- `fdry_fade_gate()` adds `html.fdry-fade` from `<head>`, and `_helper.scss` sets `opacity: 0` on every `[data-fade-up]` and `[data-fade-down]`.
- The header (`.site-header__inner data-fade-down` in `header-new.php`) and the first section's tagline, title, body and actions all carry fades.
- They become visible only when `gsapFade.js` runs, after the bundle downloads.
- On service pages the bundle lands at 5.1 s on slow 4G, so **FCP is 5.8 s**: nothing is painted before it. The fallback only removes the gate on `load` (about 11 s).

**Fix: don't JS-gate the first viewport.**
- **Recommended: CSS-only entrance for first-screen elements.**
  1. Add a `data-fade-first` attribute (or `fade-first` class) for the header and `is_first` sections.
  2. Animate it with a CSS `@keyframes` (opacity and translateY, about 0.8 s, `power3.out`-like `cubic-bezier`), in `_helper.scss`. It runs from first paint, with no JS. Delays come from inline `animation-delay`.
  3. Leave it out of the `.fdry-fade` hide rule, and keep `prefers-reduced-motion` → no animation.
- **Where to change:**
  - `header-new.php`: `.site-header__inner`.
  - `centered-content.php` when `$is_first`: About, Services, Service child, Service inner, Careers, Sectors.
  - The first `intro-content.php` on the homepage, if it is above the fold.
  - The intros in `work-archive.php` and `insight-archive.php`, `template-contact.php`, `template-one-column.php`, `404.php` and `components/single/insight.php`.
- **Alternative:** drop the fade on those elements entirely. This is simpler but loses the motion.
- **Keep** `gsapFade.js` and the gate for everything below the fold. No JS changes are needed.

**Decision (12 Oct 2026): CSS fade, same motion as the GSAP one.** That's a 50px rise over 2 s, power3.out, with the same delays.

**Status: live (12 Oct 2026).**
- **Helper.** `fdry_fade_attrs()` in `function-dev.php` prints `data-fade-first` (and an inline `--fade-delay`) on the first screen, and `data-fade-up` everywhere else.
- **Animation.** The `fdry-fade-first-*` keyframes in `_helper.scss` run it from first paint.
- **Where.** It's on everything in the "Where to change" list except the homepage `intro-content`, which is always below the 100vh hero. It is also on the first centered-content's inner work row, because that `<section>` is the LCP slide on service inner pages and was gated as well. The Works results are included too.

**Found while testing (Chrome 153, local test page, 12 Oct):** a CSS fade that starts at `opacity: 0` holds FCP and LCP back until it **ends**. Chrome runs the animation on the compositor and doesn't count content under opacity 0 as painted.

| Variant | FCP | LCP (image) |
|---|---|---|
| No fade | 96–160 ms | 96–160 ms |
| CSS fade from opacity 0 | 2,084 ms | 2,084 ms |
| CSS fade from opacity 0.01 (shipped) | 80–88 ms | 96–100 ms |
| JS gate revealed at 1.5 s (today's model) | 3,556 ms | 3,556 ms |

So the keyframes start at `opacity: 0.01`, which looks the same. Any future CSS fade on an LCP element needs the same treatment. The metric now fires on the first frame, about 0.3 s before an element is half visible (later for delayed ones).

**Measured live (12 Oct, after deploy).** Headless Chrome 153, mobile, slow 4G and 4× CPU, cold cache, the same settings as the baseline:

| Page | FCP (baseline) | LCP (baseline) | LCP element | fdry CSS done | Bundle done |
|---|---|---|---|---|---|
| Service inner | 1.87 s (5.8) | 1.87 s (11.3) | work-row slide (WebP, fade-first) | 1.02 s | 4.02 s |
| Contact | 1.66 s (2.0) | 1.66 s (2.7) | title (fade-first) | 0.94 s | 1.59 s |
| Services | 1.65 s | 1.65 s | showreel poster | 0.87 s | 3.98 s |
| Home | 1.98 s (2.1) | 6.0 s (7.3) | cookie banner (#3) | 0.95 s | 5.11 s |
| Single insight | 1.82 s (2.1) | 9.6 s (3.8) | hero image: a #1 regression, see #1 | 0.90 s | 2.60 s |

- **What the numbers show.** The service-inner LCP now lands about 2 s before the bundle finishes, so #2 is doing its job. The LCP time itself is shared with #1, since the slide is now 45 KB.
- **Functional checks, all passed:**
  - With JS disabled: no gate, and all first-screen elements are at opacity 1 (service inner, Home, Contact, Privacy, 404).
  - With reduced motion: no CSS fade, and nothing transparent.
  - Scroll fades below the fold: 47–104 per page start hidden, and all play, with none stuck (service inner, Home, About).
  - Works filter: dims to 0.65, swaps the cards and leaves no inline opacity.

**Re-measured with `pnpm perf` (#0), after the purge.** 12 Oct, `pnpm perf mobile --runs 3 --label after-2`, median of 3 loads, warm page cache:

| Page | FCP (baseline) | LCP (baseline) | LCP element | TBT |
|---|---|---|---|---|
| Home | 1.91 s (2.1) | 6.08 s (7.3) | cookie banner (#3) | 150 ms |
| About | 1.75 s (2.8) | 1.75 s (3.3) | centred-content title (fade-first) | 195 ms |
| Services | 1.70 s | 1.70 s | showreel poster | 151 ms |
| Service inner | 1.76 s (5.8) | 1.76 s (11.3) | work-row slide (fade-first) | 165 ms |
| Works | 1.87 s (2.7) | 1.87 s (3.9) | first work card (fade-first) | 155 ms |
| Insights | 1.55 s (2.7) | 1.55 s (2.7) | intro text (fade-first) | 144 ms |
| Single insight | 1.78 s (2.1) | 9.60 s (3.8) | hero image (the #1 regression; fix not deployed yet) | 130 ms |
| Contact | 1.60 s (2.0) | 1.60 s (2.7) | title (fade-first) | 165 ms |

- **What's left on the first screen.** Every first-screen LCP now paints with the CSS. What still holds FCP at about 1.6–1.9 s is the 8 render-blocking files: `theme.min.css` finishes last, after our own CSS. That's #4.
- **TBT isn't comparable with the baseline.** The baseline's 0.8–1.5 s was probably inflated by CSS coverage tracking during the load. The baseline included coverage, and tracking it during the load measured 0.9–1.4 s while building #0. Compare TBT only between `pnpm perf` runs.

**Verify:**
- Mobile FCP on a service page is about the CSS arrival time (≤ 2 s now, ≤ 1.3 s after #4).
- With JS blocked in DevTools, the header and the first section are visible at once.

**Docs:** FRONTEND.md → "Fade up / fade down" → **First screen** (done).

### #3 Homepage LCP is the cookie banner

**Evidence (mobile timeline):**
1. The inline Ads `gtag/js` (197 KB) starts at 0.22 s and takes bandwidth from everything else.
2. Six font files (about 180 KB, at VeryHigh priority) follow from 2.1 s.
3. Our bundle (Medium priority) lands at 5.35 s, so DOMContentLoaded is at about 5.4 s.
4. Only then does CookieYes fetch its config, translations and audit table (5.4 → 6.65 s), and the banner paints at **7.3 s**, which is the LCP.

Chrome doesn't count the full-viewport hero poster, so for a first visit the banner paragraph is the largest element. Returning visitors (consent given) get the showreel label at about 2.4 s.

**Why the effort is "S, result depends on #4 and #5"** (checked 12 Oct): #3 has no cause of its own.
- **The banner waits for DOMContentLoaded.** CookieYes's `banner.js` does nothing until DOMContentLoaded. It then fetches 4 JSON files from `cdn-cookieyes.com` **one after another**: the ruleset, the config, the translations and the audit table.
- **DOMContentLoaded waits for the bundle.** It waits for the 5 synchronous scripts at the end of `<body>`, and our bundle is the last big one.
- **So the banner paints at DOMContentLoaded plus 4 round trips.** It only moves when bytes before the bundle go away: the tags (#5) and the legacy CSS/JS with their old font faces (#4).
- **#6 saves almost no bytes.** Fonts are already `swap` on live, and unused kit weights aren't downloaded (see #6).

**Found along the way:**
- **WP Rocket self-hosts several third parties.** It serves the CookieYes loader, the Typekit kit CSS and jQuery from its minify cache (`/wp-content/cache/min/1/…`). So:
  - `use.typekit.net` (font files) and `p.typekit.net` (the kit's `@import`) are first contacted late, on cold connections.
  - Nothing loads from `ajax.googleapis.com`, which makes its dns-prefetch stale already.
- **The CookieYes preconnect needs `crossorigin`.** `banner.js` loads its JSON with `fetch()` without credentials, which uses a different connection from the one `banner.js` itself came on.

**Fix:**
1. **Done in code (12 Oct):** `fdry_resource_hints()` in `function-dev.php` (`wp_resource_hints`, new header only).
   - **Preconnect:** to `use.typekit.net` (crossorigin), `p.typekit.net` and `cdn-cookieyes.com` (crossorigin).
   - **dns-prefetch:** drops core's `ajax.googleapis.com` entry.
   - **CMS:** also remove `ajax.googleapis.com` from WP Rocket → Preload → Prefetch DNS Requests.
2. **Done in code (12 Oct):** #5 step 1, the inline Ads tag out of `header-new.php`. This removes 197 KB that started at 0.22 s.
3. **The rest is #4 step 3 and #5 step 2.** They remove the legacy CSS, the old font faces and the extra `gtag/js` loads.
4. **Fallback, only if the banner is still over 3 s after those:** load the bundle `async`, with a `readyState` check in `main.js`, so it stops holding back DOMContentLoaded. This overlaps #10.
5. **Compact CookieYes layout on mobile.** Decided 12 Oct: not now. It fixes the metric, not what visitors see. Revisit after #4 and #5.

**Verify:**
- **Before/after:** `pnpm perf mobile / --runs 3 --label before-3`, then `after-3` once deployed (and desktop).
- **Homepage mobile LCP targets:** < 4 s after #5 steps 1–2, and < 3 s after #4 step 3. The LCP element is either the hero label or the banner.
- **Markup:** the homepage HTML has the 3 preconnects (2 with `crossorigin`), no `ajax.googleapis.com` dns-prefetch, and no inline `gtag/js?id=AW-11543866131` request.

**Status: live (12 Oct).** Steps 1–2 deployed. The WP Rocket cache was cleared after the deploy: the cached page kept the old markup until then.

**Before, measured on live with the current code** (`build/perf/2026-10-12-2316-before-3-mobile.json`, median of 3, cold first visit):

| Home, mobile | Time |
|---|---|
| FCP | 2.02 s |
| Bundle end | 5.11 s |
| DOMContentLoaded | 5.17 s |
| CookieYes JSON chain (ruleset → config → translations → audit table) | 5.35 → 5.97 s |
| LCP: the banner, `p` in `div.cky-notice-des` | **6.05 s** |
| Inline Ads `gtag/js` (196 KB, Low priority) ends | 6.63 s |
| `gtm.js` (175 KB) ends | 6.43 s |

- **Desktop** (`2026-10-12-2317-before-3-desktop.json`): FCP 0.43 s, DOMContentLoaded 0.74 s, LCP 1.00 s. The banner is the LCP there too, but well inside "good".

**After, measured on live once deployed and the cache cleared** (`build/perf/2026-10-12-2329-after-3-mobile.json`, median of 3; the 3 runs agree to within 0.07 s):

| Home, mobile | Before | After |
|---|---|---|
| FCP | 2.02 s | 1.90 s |
| First font file | 3.90 s | 3.56 s |
| Bundle end | 5.11 s | 4.76 s |
| DOMContentLoaded | 5.17 s | 4.78 s |
| CookieYes JSON chain | 5.35 → 5.97 s | 4.97 → 5.66 s |
| **LCP (still the banner)** | **6.05 s** | **5.72 s** |
| Ads `gtag/js` loads | 2 (6.6 s, 8.9 s) | 1 (8.0 s, from GTM) |
| Third-party bytes | 1,063 KB | 866 KB |
| Page weight | 1,714 KB | 1,516 KB |

- **Desktop:** FCP 0.43 s, LCP 1.02 s, DOMContentLoaded 0.76 s. That's unchanged, within noise.
- **What worked:**
  - Removing the inline Ads tag took 197 KB out of the load. DOMContentLoaded and LCP moved about 0.35 s.
  - The fonts arrive about 0.35 s sooner (the Typekit preconnects).
- **What didn't:** the CookieYes preconnect. The first JSON still starts about 0.19 s after DOMContentLoaded, the same as before. It costs one early connection, so drop it if a preconnect is ever needed elsewhere.
- **Not at the target yet.** About 1 MB still downloads before the banner. Ranked by size:

  | What | Size | Fixed by |
  |---|---|---|
  | `gtm.js` | 175 KB | #5 step 2 |
  | Mobile hero poster | 157 KB | needed |
  | `btn-circle.svg` | 92 KB | content fix above |
  | The bundle | 80 KB | #10 |
  | `theme.min.css` and the old 400 face | 57 + 32 KB (the italic, 33 KB, comes later) | #4 step 3 |
  | Desktop poster | 54 KB | #8 |
  | jQuery and `theme.min.js` | 48 KB | #4 step 5 |

- **Next for #3:** `btn-circle.svg` and #8 are small and save 146 KB. Then #4 step 3. Re-measure with `--label after-3b`.
- **The banner lands about 0.9 s after DOMContentLoaded.** For LCP < 3 s, DOMContentLoaded has to land by about 2.1 s.
- **The CookieYes connection was already warm.** Each JSON took about one round trip (about 200 ms), so the `cdn-cookieyes.com` preconnect may gain little. `after-3` will show it.
- **Found along the way:**
  - `btn-circle.svg` (92 KB, ends at 4.9–5.4 s) is a 40×40 arrow icon in the homepage WYSIWYG content (3 links: About, Works, Services, all the same file). It carries a hidden 2816×746 PNG, placed outside the viewBox by the Illustrator export. **Fix (content, S):** re-export it without that layer, or delete the `<image>` element. Then replace the file in the media library. It should come out under 1 KB.
  - Phones also download the desktop hero poster (54 KB, the showreel modal `poster`). That's #8.

## P1: High

### #4 Legacy CSS and JS on new templates (absorbs SEO #18 and #23)

**Status (14 Oct 2026): the CSS (steps 1–3) and the JS (step 5) are live, with the skip-link fix (redeployed 14 Oct).** Measured below. A small follow-up for `/works/` (first card eager) is live too. The posts (step 4) and the dead-code deletion come next.

**Measured after the deploy (14 Oct, `after-4`, 3 runs, slow 4G and 4× CPU).** Mobile, against `p2-base` (13 Oct, 1 run; `after-8b` 3 runs in brackets):

| Page | FCP | LCP | DOMContentLoaded | Requests | First-party | CSS |
|---|---|---|---|---|---|---|
| Home | 1.82 → **0.80 s** | (4.86) → **3.88 s** (the banner) | (3.91) → 2.85 s | 59 → 50 | 596 → 278 KB | 85 → 16 KB |
| About | 1.86 → **0.74 s** | 1.86 → **0.74 s** | 1.94 → 2.23 s | 54 → 44 | 543 → 385 KB | 85 → 16 KB |
| Services | 1.96 → **0.88 s** | (1.72) → 2.36 s | 3.63 → 2.98 s | 54 → 46 | 404 → 283 KB | 85 → 16 KB |
| Shopify agency | 1.82 → **0.85 s** | 1.82 → 1.69 s | 3.46 → 2.76 s | 53 → 44 | 366 → 246 KB | 85 → 16 KB |
| Works | 1.85 → **0.79 s** | 1.85 → 3.26 s | 1.93 → 2.62 s | 54 → 47 | 500 → 388 KB | 85 → 16 KB |
| Insights | 1.63 → **0.73 s** | 1.63 → **0.79 s** | 1.73 → 2.28 s | 53 → 45 | 355 → 236 KB | 85 → 16 KB |
| Contact | 1.61 → **0.82 s** | 1.61 → **0.82 s** | 1.64 → 2.10 s | 59 → 51 | 549 → 428 KB | 85 → 16 KB |

- **Render-blocking files 8 → 4 on every new page.** Font files 6–7 (184–216 KB) → 5 (150 KB): the old `theme.min.css` faces are gone (#6).
- **Desktop:** FCP 0.41–0.98 → 0.29–0.36 s on all 7 pages. LCP is equal or better within one-run noise (About 1.08 → 0.65 s, Works 0.98 → 0.71 s).
- **The single insight** (still legacy, step 4) is unchanged: 8 render-blocking files.
- **What got worse, and why: the tags now start about 1 s earlier.** The GTM snippet is an inline script, and it used to wait for `theme.min.css` (inline scripts wait for the stylesheets before them). Now it runs at about 0.7 s, so `gtm.js` (176 KB) downloads with our bundle (81 KB), the fonts and the images. GTM now finishes 1–1.9 s sooner.
  - **DOMContentLoaded** follows the end of our bundle. It improved where the bundle was the bottleneck (Home, Services), and rose 0.3–0.7 s on About, Works, Insights and Contact. Those pages already paint at 0.7–0.8 s, so it doesn't delay anything visible.
  - **TBT** (long tasks after FCP) rose from about 200 to 400–600 ms. FCP moved about 1 s earlier, so the tags' work that used to run before first paint now counts. Main-thread JS shrank (788 → 728–736 KB of script).
  - **Services LCP** 1.72 → 2.25–2.36 s. The poster starts at 0.28 s as before, but now shares the line with the bundle, GTM, the fonts and the work-row slider (3 eager images, 98 KB; see #7). Note that DevTools throttling shares bandwidth evenly and ignores HTTP/2 priorities, so the lab penalty is probably larger than on a real connection.
  - **Works LCP** 1.85 → 3.26 s. The first card was `loading="lazy"`: it started only after layout at Low priority. On 13 Oct WP Rocket's "critical images" data had upgraded it, but that data was being re-collected after the cache clear (its beacon was back on `/works/`; see #11).
    - **Fix (live 14 Oct):** `fdry_render_work_cards($query, true)` makes the archive's first card `loading="eager" fetchpriority="high"`. AJAX pages stay lazy (checked: Load more returns 18 lazy cards).
    - **Measured on live** (`after-4-works`, 3 runs): mobile LCP 3.26 → **2.01 s** (1.92–2.10), the image starting at 0.28 s at High; desktop 0.71 → **0.31 s**. WP Rocket's beacon was still on the page, so this no longer depends on its data. No JS errors.
    - **Tested on local copies of live `/works/`** (3 runs each): LCP 4.25 → **2.33 s**; the image starts at once at High instead of after layout at Low.
  - **Next lever:** delay the tags (#5 step 2, #11 "Delay JS"), now that they, not the legacy files, are what competes with the first screen.
- **Legacy pages after the skip-link fix:** 412px wide on a 412px phone on all 7 (`/brief/`, 2 case studies, a category, the job, a post, search), and on Contact and Home. No JS errors. Screenshots against 13 Oct:
  - `/brief/` and the category page: 0.00% once the new footer Sitemap link is hidden.
  - **Content edits, not code:** `/works/no-straight-lines/` now says "**Send FDRY your brief**" (bold, in the post content). The search results show new "modified" dates.
  - **Noise:** the scrollbar flash (`/brief/` desktop), and a different frame of the autoplaying video on `/works/calling/`.
  - **`/careers/account-executive/` mobile: 0.40% sub-pixel.** The CTA subtitle, the "Start your brief" circle and the footer icons re-rasterise. Everything below the job text sits at a fractional offset (.547 px, from `.single-job .entry-content li+li { margin-top: .35em }`), so a drift of a few hundredths of a pixel changes the anti-aliasing. Not traced further: the bundle's only changes reaching legacy pages are scoped (rule-by-rule diff), the stylesheet order is unchanged, the content was last edited 10 Oct and Chrome is the same version. Not visible.

**Checked after the deploy (14 Oct).**
- **Requests.** On the new pages (Home, About), none of these load any more:
  - `theme.min.css`, the mainstyle sheets
  - jQuery, `theme.min.js`, lozad, `footer.js`
  - TweenMax, `about.js`

  `/works/calling/` still loads all the legacy files.
- **Tracking.** `gtm-check` matches the baseline. The `790752334` email conversion now comes from `fdry_legacy_conversions()`. No JS errors.
- **Screenshots of the new pages against the 13 Oct baseline.** Every difference traced to something other than #4:
  - **The privacy policy page was edited at 16:20 on 13 Oct.** Its "Pages on this website" list was removed (REST `modified`), so the page is 3.7k px shorter.
  - **A "Sitemap" link was added to the footer's legal links** (`site-footer.php`). On mobile it wraps, so every page is 22px taller.
  - **The showreel button** was missing in the first post-deploy capture, while the page was cached during the new reel's upload (`FDRY_SHOWREEL_BASENAME` → `showreel-2026-10`). A recapture shows it.
  - **The Sectors italic** is still pending its Adobe kit change.
  - **Sub-pixel noise:** the scrollbar flash, and the tech-banner logo edges. Those are already in the 13 Oct simulation with the new CSS, so they're not the deploy.
- **Legacy pages: one regression found and fixed.** On phones, `/brief/`, case studies, category pages, the job, posts and search were 413px wide on a 412px screen. The header button and the cookie banner shifted, and the page could scroll sideways by 1px.
  - **Cause.** The new skip-link CSS was global and met `theme.css`'s `a.skip-link { right: 0 }`.
  - **Fix.** `_skip-link.scss` is now scoped to `.fdry-legacy-free`, like `_legacy-base.scss`.
  - **Checked on live** (live bundle CSS blocked, fix injected): `/brief/` and `/works/calling/` are back to 412px with the skip link as before. On Contact the skip link stays hidden, and on focus appears at (6, 7).

The approved implementation plan has 6 phases:
- **0:** the tooling;
- **1:** these quick wins;
- **2:** the CSS;
- **3:** the JS;
- **4:** posts;
- **5:** deleting dead code, all three tiers plus the rollback copies (decided 13 Oct).

**In code.**
- `fdry_is_legacy_free()` covers the homepage, the 404 page, the 8 ACF-only templates and `template-one-column.php`, which is 36 of the 37 published pages (not `/brief/`).
- `fdry_dequeue_legacy_assets()` removes `theme.min.css`, TweenMax and `about.js`.
- The mainstyle sheets are enqueued only on legacy pages.
- `fdry-scripts` no longer depends on jQuery.
- The front-page `#loading-animation` jQuery is gone (SEO #23).
- [`_legacy-base.scss`](src/styles/common/_legacy-base.scss) holds the ported rules: the body colour and font stack, the heading weights, line-heights and colours, and `.alignleft`/`.alignright`.
- The skip link's `.screen-reader-text` is now in our CSS. Without it, "Skip to content" showed on every page and pushed it down 24 px.
- See FRONTEND.md → Legacy assets.

**Simulated on live before deploying** (`pnpm perf --block … --inject-css … --add-class fdry-legacy-free`, then `pnpm perf:diff`, 12 pages × mobile and desktop):

| Round | Ported | Pages pixel-identical |
|---|---|---|
| 1 | skip link only | 6 of 12 desktop (Privacy, Build, Sectors, Shopify, About and Home differed) |
| 2 | + headings, alignment | 11 of 12 desktop |
| 3 | + body font stack and colour | **11 of 12 on both widths** |

- **One accepted difference left: the italic word "your" on `/sectors/`.**
  - **Cause.** The old `theme.css` faces included a true 400 italic (`6aec08`, downloaded on every page). The current Adobe kit has no italic, so Chrome slants the regular. The paragraph is 1–4 px shorter, and the content below moves up by that much.
  - **Fix (CMS).** Add Proxima Nova 400 italic to the Adobe Fonts web project. It's downloaded only where italic text appears.
- **Font stack.** The kit has no "→". Without Bootstrap's longer `body` font stack, the arrows in the homepage and Shopify content fell back to Lucida Grande instead of Arial, hence the ported stack.
- **Desktop FCP in the simulation** dropped on most pages (Services 0.41 → 0.27 s, Insights 0.41 → 0.29 s), but simulated metrics are approximate. Measure after the deploy.
- **JS (step 5), also in this deploy.**
  - **Dequeued on legacy-free pages:** `theme.min.js`, lozad and jQuery. `footer.php` skips `footer.js`.
  - **The old-account email conversion** (`AW-790752334`, decided 13 Oct: keep, without jQuery) is now an inline vanilla handler, `fdry_legacy_conversions()`.
  - **Simulated:** jQuery, `theme.min.js`, lozad and `footer.js` blocked on all 12 pages.
    - The screenshots are unchanged.
    - The only JS errors came from code this change also removes: live's front-page `#loading-animation` inline script, and `about.js`.
    - `gtm-check` with the handler injected still sends the `790752334` conversion on an email-link mousedown. The Ads, GA4 and `_gcl` results are unchanged.
- **Case studies checked (13 Oct), still fully legacy.**
  - **Nothing is dequeued.** `works_post` singles are not legacy-free, so `theme.min.css`, both mainstyle sheets, jQuery, `theme.min.js`, lozad and `footer.js` all stay, and `_legacy-base.scss` doesn't apply.
  - **They need these files:**
    - the Bootstrap grid and carousel (`content-single-work.php`, which needs `theme.min.js` and jQuery);
    - about 2.4% of `theme.min.css` and 8–9% of `mainstyle.css` in use, against 0.8% and 0% on the new pages.
  - **Cascade order unchanged.** On live `/works/calling/` only scripts sit between `fdry.css` and the hand-printed mainstyle links, so enqueuing the mainstyle sheets right after `fdry.css` keeps the order.
  - **What reaches them:** only the unscoped `.screen-reader-text`/skip-link rules (the skip link now shows on keyboard focus) and `html { scroll-behavior: smooth }` (already set by `theme.css`).
  - **Baseline:** `legacy-works-before` (`/works/calling/`, `/works/no-straight-lines/`, both widths). A repeat run diffs at 0.00%, so the carousel doesn't add noise. After the deploy, expect 0.00%.
- **Legacy pages:** before-screenshots taken (`p2-legacy-before`) for `/brief/`, a case study, a work category, a job, a post and search. Diff them after the deploy: the mainstyle sheets now print through `wp_enqueue_style` right after our CSS, rather than by hand after `wp_head()`.

**Evidence:** see the table above. The legacy files block rendering on every new-template page, and are 0–1 % used:
- `theme.min.css` also carries old Proxima Nova `@font-face` rules (see #6).
- `inc/enqueue.php` loads TweenMax 2.0.2 and `js/about.js` on `is_page('about')`, so /about/ downloads an extra 37 KB.
- The `is_page('work')` branches are already dead, since the page is `works`.

**Approach:** follow FRONTEND.md's override technique.
- Don't edit `inc/enqueue.php`. Dequeue from `function-dev.php`, and only on pages that are fully new.
- Legacy templates keep everything.

**Steps (each one shippable on its own):**
1. **Quick wins (S).**
   - `fdry_dequeue_legacy_page_scripts()` on `wp_enqueue_scripts` priority 20 dequeues `tweenmax` and `about` when the page uses `template-about.php`.
   - Remove the `#loading-animation` inline jQuery from `header-new.php`. The element only exists in `legacy-home.php`. This is SEO #23.
   - Drop `jquery` from `fdry-scripts`' dependencies in `fdry_enqueue_assets()`, since `src/` never uses it.
2. **Allowlist helper (S).**
   - Add `fdry_is_legacy_free(): bool` in `function-dev.php`.
   - **Returns true for:**
     - the templates in `fdry_acf_only_page_templates()`;
     - `template-one-column.php`, `front-page.php` and `404.php`;
     - posts (`components/single/insight.php`), only after step 4;
     - `single-job.php`, only after its legacy markup is ported (`#career`, `.defaultpage` and the CF7 application form styles; see Appendix A).
   - **Returns false for** `/brief/` (typeform), `page-templates/*`, works singles, work category archives, `page.php`, `archive.php`, `search.php` and `author.php`, until each is checked.
3. **CSS (M).** Do this when `fdry_is_legacy_free()` is true.
   - Dequeue `understrap-styles`, and remove it from `fdry-overrides`' dependencies (keep `foundry-typekit`).
   - Move the two hard-coded `mainstyle/*.css` `<link>`s in `header-new.php` into `wp_enqueue_style()` (handles `fdry-legacy-mainstyle` and `fdry-legacy-typeform`), so they can be skipped on legacy-free pages and kept on `/brief/`.
   - Port the base rules the new templates really use into `src/styles/common/_legacy-base.scss` (Appendix A).
     - **Where:** `@use` it in `main.scss` right after `preflight` and **unlayered**. That keeps today's cascade: legacy beats the Preflight layer, and our later rules beat legacy.
     - **What:** only the rules our components don't already set. Screenshots decide (see Verify).
4. **Insight typography (M).**
   - The Gutenberg output on single insights gets its heading sizes and bullets from `theme.css`/`mainstyle.css` (FRONTEND.md → Single insight → Legacy typography).
   - Include the existing `wysiwyg-content` mixin (`src/styles/common/_wysiwyg.scss`) on `.single-insight .entry-content`, check a long post, then add posts to the allowlist.
5. **JS (M).** Do this when `fdry_is_legacy_free()` is true.
   - Dequeue `understrap-scripts` (`theme.min.js`) and `lazy-jquery` (`lozad`).
   - Stop printing `mainjs/footer.js` from `footer.php`. Wrap it in `if (! fdry_is_legacy_free())`, since `footer.php` is shared.
   - **footer.js fires Google Ads conversions for the old account `AW-790752334`** (brief-1 visits and `mailto:` clicks). Ask marketing whether that account is still used. If yes, recreate the events in GTM. If no, drop them.
   - Then dequeue `jquery` itself on legacy-free pages, if no plugin script on them depends on it. Klaviyo's `kl-identify-browser.js` doesn't. Check with `wp_scripts()->registered` deps at runtime.

**Rollback:** `fdry_is_legacy_free()` returns `false`, which restores everything.

**Verify:**
- The #0 script on Home, About, Services, Service child, Service inner, Works, Insights, an Insight, Contact, Privacy and a Job: no `theme.min.css`, `mainstyle.css` or jQuery requests.
- Visual diff against before at 1440 and 390, using screenshots from the same script.
- `/brief/`, `/brief-1/`, a case study and `/works/category/{slug}/` still load the legacy files and look unchanged.

**Docs:** FRONTEND.md → new "Legacy assets" section (the allowlist, how to add a template, rollback).

### #5 Marketing tags (absorbs SEO #25)

**Evidence (network, every page):**

| Tag | How it loads | Size |
|---|---|---|
| Google Ads `AW-11543866131` | Inline in `header-new.php` **and** again from GTM | 197 KB × 2 |
| GA4 `G-37MNQT14EJ` | GTM | 176 KB |
| GA4 `G-7TCWMDF3MD` | Not in the container. It rides on the Ads Google tag `AW-11543866131` (a destination linked in that tag's settings in Google Ads), so it loads with whichever Ads `gtag/js` runs | — |
| Universal Analytics `analytics.js` | GTM. UA stopped processing data in July 2024 | 20 KB |
| GTM container `GTM-5D5B7P` | Inline | 176 KB |
| Meta CAPI param builder (`capi-automation…/clientParamBuilder.bundle.js`) | GTM Custom HTML on DOM Ready, before consent | 56 KB |
| Meta Pixel `fbevents.js`, Hotjar | Inline, but CookieYes blocks them until consent, so they only add weight after "Accept" (not measured). The Pixel is **also** a GTM Custom HTML tag (DOM Ready), so it's set up twice | — |
| LinkedIn Insight | GTM (`__bzi`), All Pages | — |
| Apollo | Inline, loads **before** consent, with a random `?nocache=` on every load, so it is never cached | small |
| Klaviyo | Plugin | small |

- In total, about 1 MB of third-party transfer per page.
- On slow 4G the first `gtag/js` starts before our CSS finishes and holds the connection for 7 s.

**GTM container `GTM-5D5B7P`** (read from the public `gtm.js`, 12 Oct; no GTM login needed):

| Tag | Trigger |
|---|---|
| Google tag `AW-11543866131` | Initialization, All Pages |
| Conversion Linker | All Pages |
| Ads conversion `11543866131` / `5vTcCJm50JYaEJPWxYAr` | Custom event `ec_form_submit` |
| Google tag `G-37MNQT14EJ`, plus a GA4 event tag that forwards every custom event | DOM Ready / every non-`gtm.` event |
| Universal Analytics `UA-9590086-32` | All Pages, plus link-click and form-submit tags |
| LinkedIn Insight | All Pages |
| Meta Pixel base, Meta event forwarder, CAPI param builder (Custom HTML) | DOM Ready / every non-`gtm.` event |

- **How the Ads script reuses itself.** When the inline Ads `gtag/js` has loaded, GTM's Ads tag reuses it, and an unthrottled load fetches it once. On slow 4G GTM gets there first and fetches its own copy (`cx=c`): the second 196 KB in the #3 mobile run.

**Fix:**
1. **Code (S):** delete the inline Ads `gtag.js` block from `header-new.php`. GTM already loads `AW-11543866131`.
   - **Live (12 Oct, with #3).** The after-deploy check (`build/perf/2026-10-12-gtm-check-after.txt`) matches the baseline:
     - one Ads `gtag/js`, loaded by GTM (`cx=c`);
     - the same Ads page-view, remarketing and `G-7TCWMDF3MD` hits;
     - the `AW-790752334` email-click conversion still fires through the stub;
     - `_gcl_aw` and `_gcl_au` are set;
     - no JS errors.
   - **Checked without GTM access.** The container fires the Ads Google tag on Initialization (All Pages) and the Conversion Linker on All Pages (table above).
   - **Baseline.** A one-off headless check on live (`build/perf/2026-10-12-gtm-check-before.txt`):
     - **Page load:** a `ccm/collect` page view for `AW-11543866131` and `G-7TCWMDF3MD` hits.
     - **After Accept:** remarketing hits.
     - **On a `mailto:` mousedown:** an `AW-790752334` conversion.
     - **Conversion Linker:** `_gcl_aw` holds the test `gclid`.
     - No JS errors.
   - **After deploy, the same check should show:**
     - one Ads `gtag/js`, with `cx=c` (loaded by GTM);
     - the same Ads, GA4 and remarketing hits;
     - `_gcl_aw` and `_gcl_au` still set;
     - no `gtag is not defined` error.
     - **Watch the `mailto:` conversion to `AW-790752334`.** It now runs through the stub and GTM's Ads script. If it stops, that's the #4 step 5 question (is the old account still used?), not a regression in the new account.
   - **Stub kept.** A `gtag()` stub (the `dataLayer` push only) stays, because `mainjs/footer.js` calls `gtag()` for the old `AW-790752334` conversions (#4 step 5). Without the stub those calls would throw. Remove it with `footer.js`.
   - **Legacy header.** `header.php` (legacy) still has the inline tag.
2. **GTM (CMS, S):**
   - Delete the Universal Analytics tags (the page view and the link-click and form-submit events).
   - Two GA4 IDs: `G-37MNQT14EJ` is the container's GA4 tag (it also goes to a server-side tagging endpoint). `G-7TCWMDF3MD` comes from the Ads Google tag's linked destinations (in Google Ads, not GTM). Ask marketing which property is live, and unlink or remove the other.
   - Check that the Ads and GA4 tags use the same Google tag, so `gtag/js` loads once, not three times.
3. **Move into GTM behind CookieYes consent (M):** Hotjar, Apollo (without the `nocache` cache-buster) and the Meta Pixel. Remove their inline blocks from `header-new.php`. Fire Hotjar and Apollo on "Window Loaded" or after consent, never in `<head>`.
4. **Consent check.** The inline Ads tag currently runs **before** CookieYes in `<head>`, so it fires before consent. Moving it into GTM with Consent Mode fixes that too.

**Decision needed:** marketing confirms which Ads account, GA4 property, Hotjar and Apollo are still in use.

**Verify:**
- Exactly one `gtag/js` per destination. No `analytics.js`.
- Third-party transfer under about 500 KB per page before consent.
- The conversions still fire: GTM Preview, or the headless check in step 1.
- Mobile JS time from Google tags under about 250 ms in the profile.

### #6 Fonts

**Evidence:**
- **The kit** (`use.typekit.net/rdq4arx.css`, enqueued as `foundry-typekit`) serves Proxima Nova in 100, 300, 400, 500, 600, 700 and 800, with `font-display: auto`.
- **Already `swap` on live (checked 12 Oct).** WP Rocket self-hosts the kit CSS and `theme.min.css` in its minify cache, and rewrites every `@font-face` there to `font-display: swap`. So text isn't hidden today. It would be again if WP Rocket's CSS minify were turned off.
- **`src/` uses** 300, 400, 500, 600 and 700.
- **Unused weights cost no bytes.** A browser only downloads a face that text on the page uses, so 100 and 800 in the kit add no font requests.
- **800 is still needed.** `mainstyle.css` uses it 13 times, in rules for legacy templates: case-study `.work-single-intro`, `.darkbluebanner`, `.lastsectionservice`, `.greybanner` and `.bluedesign`. 900 comes from `theme.min.css`'s old faces.
- **Old faces in `theme.min.css`.** Its own `@font-face` rules point at an older kit: 300, 400, 400 italic, 700 and 900. So the 400 file downloads twice (`d7ff92` and `4838bd`), and an italic (`6aec08`, 33 KB) arrives at 7.3 s. **These are the font bytes worth removing.**
- Pages download 5–7 font files, 154–216 KB, at VeryHigh priority, which competes with the bundle.
- The kit CSS `@import`s `p.typekit.net/p.css`: a chained, render-blocking request.

**Fix:**
1. **Adobe Fonts web project** (CMS, S, hygiene). Set **Font display → swap**, so nothing depends on WP Rocket's rewrite, and remove weight 100. **Keep 800** (legacy templates).
2. **Old faces:** they go away with #4 step 3 on legacy-free pages. Don't edit `theme.css`.
3. **Preconnect:** to `use.typekit.net` (crossorigin) and `p.typekit.net`, in the same `fdry_resource_hints()` as #3. **Done in code (12 Oct, with #3).**
4. **Optional:** ask Adobe support or check the kit settings for turning off the `p.typekit.net` tracking import. Some kits allow it, others don't.

**Verify:** at most 4–5 font requests on Home, all from `rdq4arx`, and text paints before the fonts arrive.

### #7 Responsive work images (supersedes SEO #16)

**Evidence:**
- `work-row.php`, `work-parallax.php` and `fdry_render_work_card()` (`library/function-work.php`) output a single `large` URL with no srcset or sizes.
- Desktop gets 768×1024 portrait crops at roughly double the displayed size in the 3-up grid. Retina still lacks a 2× candidate.
- `NUYU.jpg` (parallax card 1) is lazy but still loads at 2.4 s on phones. It sits within Chrome's lazy-load distance, so it competes with the hero.
- **Services (14 Oct, after #4):** the work-row slider's 3 eager images (NUYU, Soie Labs, Goodnest: 98 KB) start at 0.28 s with the hero poster, below the fold on phones. Smaller srcset candidates would cut that. `fetchpriority="low"` would also help, but not on `/services/shopify-agency/`, where a work-row image is the LCP.
- **Work cards:** the archive's first card is now eager with `fetchpriority="high"` (#4 follow-up, 14 Oct). Keep that when switching to `wp_get_attachment_image()`.

**Fix:**
- Switch these three to `wp_get_attachment_image()` with an explicit `sizes`, as `insight-archive.php` and `components/single/insight.php` already do. For example:
  - Work row: `(max-width: 1139px) 100vw, 33vw`.
  - Work card: `(max-width: 767px) 100vw, (max-width: 1139px) 50vw, 33vw`.
  - Parallax: `100vw`.
- Keep the existing loading rules: slider mode eager, stack mode lazy, parallax lazy.
- Add `fetchpriority="low"` to the parallax images.
- Apply the image decisions recorded in SEO #16: the 3:4 1200×1600 featured image and the parallax field.

**Verify:**
- At 390px, the work-row and card requests are ≤ 600w candidates.
- At 1440px DPR 1 they are ≤ 768w, and DPR 2 picks the larger one.
- The sticky stack still works on the homepage, and CLS stays at about 0.

## P2: Nice to have

### #8 Showreel modal poster

- **Problem:** `hero-video.php` gives the modal's `<video>` a `poster` attribute. Browsers fetch posters as soon as the element exists, `preload="none"` or not, so the desktop poster (55 KB) downloads with the dialog closed. That includes phones (seen in the mobile run).
- **Fix:**
  - Output `data-poster` instead.
  - In `showreelModal.js`, copy it to `poster` in the same place `src` is primed (pointerenter, focus or click).
- **Verify:** a phone homepage load requests only `hero-home-poster-mobile.webp`.
- **Status: done (12 Oct), including the Services follow-up fix.**
  - **Code.** `hero-video.php` prints `data-poster`. `attach()` in `showreelModal.js` copies it to `poster` the first time it runs (pointerenter, focus or click), and close leaves it in place.
  - **Tested** in headless Chrome on a local copy of the live homepage with the new bundle:
    - At 412px the page load requests only `hero-home-poster-mobile.webp`, and `hero-home-poster.webp` arrives on `pointerenter`.
    - Open, close and reopen all work, with the poster kept and `src` dropped and re-attached.
    - At 1440px there's still one poster request: the hero's, which the modal reuses.
  - **Before** (`build/perf/2026-10-12-2336-before-8-mobile.json`, median of 3):
    - **Home, mobile:** FCP 1.96 s, DOMContentLoaded 4.81 s, LCP 5.75 s (the banner). The desktop poster is 54 KB, ending at 1.75 s, in the same window as the CSS, the fonts and the mobile poster (156 KB, ends 4.48 s).
    - **`/services/`, mobile:** LCP 1.69 s. That's the showreel `<picture>`, which uses the same poster, so no change is expected there.
  - **After** (`build/perf/2026-10-12-2341-after-8-mobile.json`, median of 3):

    | Mobile | Before | After |
    |---|---|---|
    | Home, bundle end | 4.75 s | 3.69 s |
    | Home, DOMContentLoaded | 4.81 s | 3.49 s |
    | **Home, LCP (the banner)** | **5.75 s** | **4.54 s** (runs 4.54–4.74) |
    | `/services/`, LCP | 1.69 s | **3.54 s** (regression, see below) |

    - **Why Home gained more than 54 KB would suggest.** The poster was a Medium-priority request discovered early by the parser, so it sat ahead of the bundle (also Medium, at the end of `<body>`) on the same connection. Without it the bundle lands about 1 s sooner, and DOMContentLoaded and the banner follow.
  - **Regression on `/services/`, fixed and live (12 Oct).**
    - **Cause.** The Services showreel (the inline variant, right under the first section) had `loading="lazy"` on its poster `<img>`, yet it is that page's mobile LCP element. Before #8 the modal's `poster` attribute fetched the same file early, which hid the problem. Now the lazy image starts only after layout, at Low priority.
    - **Fix.** The `<img>` in `hero-video.php` is now always `loading="eager"`. The hero keeps `fetchpriority="high"`, and the inline variant doesn't need it.
    - **Tested** on local copies of `/services/` (3 runs each, slow 4G and 4× CPU): lazy gives LCP 4.26–4.39 s; eager gives **1.44–1.51 s**, equal to FCP.
    - **After the fix** (`build/perf/2026-10-12-2349-after-8b-mobile.json`): `/services/` mobile LCP is **1.72 s** (runs 1.65–1.85), back to its pre-#8 1.69 s. Desktop `/services/` LCP is 0.44 s.
  - **Run-to-run spread on Home.** after-8 gave LCP 4.54–4.74 s, and after-8b, with no Home change in between, gave 4.86–4.88 s. Compare sessions as ranges: Home's gain from #8 is about 0.9–1.2 s.
  - **Found along the way: the desktop hero loops are heavy and have WebM files.**
    - **Sizes.** The homepage loop is `home-short.webm` at 9.6 MB (MP4 9.9 MB). The Services loop is `hero-home.webm` at 5.9 MB (MP4 7.8 MB, 6 MB of the 7.2 MB desktop Services load).
    - **Against the guide.** `video-encode.md` says to leave the WebM fields empty and serve MP4 only.
    - **Impact.** They're desktop-only and load at Low priority after the LCP, so they don't touch Core Web Vitals, but they cost a lot of bandwidth.
    - **Not an item yet.** Candidate for a P3 alongside #14: re-encode shorter or lighter, and decide WebM or MP4.

### #9 Marquee HTML weight

- **Problem:** `components/page/marquee.php` prints the 9 logos **twice**. `marquee.js` then deletes the second list and builds its own clones, so the second list is pure HTML weight: 58 KB raw. The SVGs are unoptimised (11 KB for one logo).
- **Fix:**
  - Print one `<ul>`.
  - Optimise the 9 SVG files once with SVGO (`--precision=2`, no metadata) and replace them in the media library.
  - **Optional:** cache the optimised markup in `acfFile_toSvg()` with a transient keyed on the file's modified time.
- **Verify:** homepage HTML goes from 240 KB to under about 140 KB raw, and the marquee still loops seamlessly at 390 and 2560px.

### #10 Bundle size and splitting

- **Remove `smoothscroll-polyfill` (S).**
  - Native smooth scrolling is in every supported browser (Safari 15.4+), and Lenis handles the anchors.
  - Delete the import and the `run()` call in `main.js` and the dependency in `package.json`.
  - Check anchor links where Lenis is off: touch, reduced motion, legacy header.
- **Code-split (M).**
  - Load these with `import()` when their root element exists:
    - `workRowSlider` and `teamSlider` (Swiper JS and CSS);
    - `techGrid`;
    - `showreelModal`;
    - `workArchive`.
  - This needs the bundle as an ES module:
    - Add `type="module"` with `wp_script_add_data('fdry-scripts', 'type', 'module')`, or with a `script_loader_tag` filter.
    - Vite then emits chunks with `modulepreload`.
    - Keep `isolateGsap` as the first import. Module scripts are deferred by default, so check the `DOMContentLoaded` handler still runs (it does: deferred scripts run before DOMContentLoaded).
  - Swiper's CSS moves with its chunk.
- **Verify:** the entry chunk is ≤ 50 KB br on pages without sliders, and nothing breaks on the homepage, Works, About (team slider) and a service inner page.

### #11 WP Rocket settings (CMS, after #4 and #5)

- **Load JavaScript deferred: on.**
  - It is safe once the inline jQuery and `footer.js` are gone from new pages.
  - Exclude CookieYes, which must stay blocking for auto-blocking.
- **Delay JavaScript execution: on**, only for whatever third parties remain outside GTM (Klaviyo onsite, and Hotjar if still inline).
- **Remove Unused CSS:** not needed on new templates after #4. Only consider it for the legacy brief and case study pages.
- **Clear Priority Elements and the cache** after each deploy that changes the hero (SEO #9 step 2).
- **Don't rely on its "critical images" data for an LCP image (found 14 Oct).** After a cache clear its beacon (`wpr-beacon.min.js`) comes back on some pages while it re-collects, and until then those pages get none of its priority hints. `/works/`' lazy first card went from High priority (LCP 1.85 s) to Low (3.26 s) that way. Set `fetchpriority`/`loading` on the LCP image in the template instead, as the hero posters and the first work card now do.
- **"Separate cache files for mobile devices": turn it off (found 12 Oct while building #0).**
  - **The problem.** Phones get their own cache, and preload doesn't fill it. After a purge, the first phone on each page waits for WordPress to render it: TTFB 0.8–1.9 s, against 0.1 s cached.
  - **Why it's safe.** The theme never serves different HTML to phones: there's no `wp_is_mobile()` or user-agent check anywhere.
  - **Check first** that no plugin needs it.
  - **Verify.** After a purge and preload, `curl -A "<Android UA>"` on a page returns the `cached@` comment on the first request.

### #12 Image upload guard

- **Less urgent since #1.** Every upload now gets a WebP copy. The guard still stops 5 MB uploads, which are slow to convert and weigh a lot on disk.
- **What:** add `fdry_validate_image_upload()` on `wp_handle_upload_prefilter`, next to the video guard in `function-dev.php`.
- **Rules:**
  - Reject JPG or PNG over 1 MB, or over 3000px on the long edge.
  - Warn (ACF `acf/validate_value`) for PNGs over 300 KB used as photos.
  - Return copy-paste export instructions, like `fdry_video_encode_instructions()`.
- **Skip** SVGs and the favicon.

## P3: Optional

- **#13 Edge HTML caching.**
  - HTML is `cf-cache-status: DYNAMIC`, so every view goes to the origin (WP Rocket's file cache).
  - TTFB was 60–250 ms from Milan.
  - Measure from London and the US (WebPageTest) before acting.
  - If it is worth it, use Cloudflare APO, or a Cache Rule that bypasses on WordPress cookies, plus WP Rocket's Cloudflare add-on for purges.
- **#14 Nav videos.** Re-export the three menu videos at 560×560 (FRONTEND.md → Nav overlay videos). `pnpm encode` has no size option, so add one to `scripts/encode-video.mjs`, or ask the designer.
- **#15 Leftovers.**
  - **Global styles:** dequeue the `global-styles` inline CSS (9.6 KB, 33 % used) and `classic-theme-styles` on ACF-only templates.
  - **Prefetching:** WordPress core's speculation rules and WP Rocket's "preload links" both prefetch on hover. Keep one. Optionally try `moderate` prerender via the `wp_speculation_rules_configuration` filter, after checking GA4 handles prerendered pages.
  - **Uncached queries:**
    - `fdry_template_page_url()` runs a meta query on every call. Use a static plus transient cache, flushed on `save_post_page`.
    - `fdry_work_categories()` loads every work ID on each Work page request. Use a transient, flushed on `save_post_works_post`.
    - Both only matter on cache misses and for logged-in users.
- **Found along the way, not performance:** `components/footer/tech-banner.php` loads `img/img/*.svg`, which exist on the server but aren't in git. Commit them, or a fresh deploy breaks the tech grid.

---

## Appendix A: legacy CSS still used by new templates

Rule coverage on 12 new-template pages at 1440 and 390px, scrolled top to bottom (12 Oct):
- Home, About, Services, Services/Build, Shopify, Works, Insights, one insight, Contact, Privacy, Careers and one job.
- Of `theme.min.css`'s 464 KB, **71 rules (6.5 KB) are used**. `mainstyle.css` has 10 rules (0.5 KB), all on single insight and single job. `typeformstyle.css` has 1.

| Group | Used on | Rules | Port to |
|---|---|---|---|
| **Bootstrap reboot.** `*,::before,::after` border-box. `html` font and `-webkit-text-size-adjust`. `article…section{display:block}`. Form element font inheritance and `[hidden]`. `img`/`svg` vertical-align. `figure`, `address`, `dl/ol/ul` and `p` bottom margins. | every page | ~25 | `_legacy-base.scss`. Check each against `_preflight.scss` and keep only what differs. |
| **Base type.** `body` (proxima-nova stack, 1rem/24px, `#212529`). `a` (`#5472c6`, no underline). `a,p` 16px (15px at one breakpoint). `h1–h4` margins, weights and sizes, including `h1{font-size:68px;line-height:70px}` from 992px. | every page | ~20 | `_legacy-base.scss`. **The h1 rule matters:** taglines became `<h1>` (SEO #4), so check every tagline keeps its size and line-height. |
| **`html{scroll-behavior:smooth}`** | every page | 1 | `_reset.scss`, outside `html.lenis` and under `prefers-reduced-motion: no-preference`, so touch devices keep smooth anchors. |
| **`.btn` base.** `display`, `white-space`, `user-select`, cursor. Our BTN partial uses the class name `.btn`. | every page | 2 | `_button.scss` |
| **Screen-reader and skip link.** `.screen-reader-text`, `a.skip-link` | every page | 3 | `_skip-link.scss` |
| **Single insight.** `.wrapper` padding, `.insight-content` padding and `p`, `.insight-content .entry-content h2`, `.insight-content ul` disc bullets, `.alignleft`, `h3/h4` sizes. | insight | ~10 | #4 step 4: the `wysiwyg-content` mixin on `.single-insight .entry-content` |
| **Single job.** `#career .career-main`, `.defaultpage*`, `.single-job .entry-footer`, `.container`/`.row` with breakpoints. CF7: `.wpcf7`, `.form-group`, `label`, `.form-control`, `input[type=submit]` as `.btn-outline-primary`, `.wpcf7-response-output`. | job | ~25 | `_single-job.scss`, before `single-job.php` joins the allowlist |
| `html,body{height:100%;background:#FFF}` (typeform) | every page | 1 | Already undone by `lenis.css` (FRONTEND.md). Drop it. |

Re-run the coverage with the #0 script (`--legacy-css` mode) after porting. It should report 0 legacy rules on legacy-free pages, because the files are no longer loaded.

## Suggested order

1. #0 (tooling), then a field-data check in Search Console.
2. **P0:**
   - #1 (WebP rollout on live) and #2, the biggest LCP/FCP gains.
   - Then #3 with #5 step 1 and the #6 preconnects (code ready 12 Oct), and re-measure.
   - #3 reaches its target only with #4 step 3 and #5 step 2.
3. **P1:**
   - #4 steps 1–3, then 4–5.
   - #5 steps 2–4 with marketing.
   - #7.
4. **P2:**
   - #8 and #9 (small).
   - #11 after #4 and #5.
   - #10 and #12.
5. **P3:** as time allows.

## Verification (every item)

1. Run `php -l` on changed PHP, then `pnpm build`. Deploy `dist/` and `.vite/manifest.json`, then clear the WP Rocket cache (and Priority Elements if the hero changed).
2. Run `pnpm perf` (#0) on Home, About, Services, `/services/shopify-agency/`, Works, Insights, one insight and Contact, mobile and desktop. Compare against the baseline table, and write the new numbers into the item's status.
3. **Legacy pages unchanged:** `/brief/`, `/brief-1/`, one case study, `/works/category/{slug}/`.
4. **Field data:** Search Console → Core Web Vitals (mobile and desktop) and PageSpeed Insights in the browser, 28 days after deploy.
5. **Docs:** update FRONTEND.md with the "Legacy assets", fade-first, font, tag and image-guard rules, as each item lands.
