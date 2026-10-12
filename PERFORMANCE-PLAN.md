# Performance plan: new (2026/27) templates

> **Status:** first recorded 12 October 2026. In progress: #1 live (12 Oct), LCP re-measure pending. #2 live (About, Works and Insights still to measure once their WP Rocket cache is purged). Measured against the live site (www.fdry.com) and the code on `main` (74c3662).
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
| 0 | — | Write and commit a measurement script, so each item can be measured before and after. It doesn't exist yet: the baseline was taken with throwaway tools. | Code | S | Open |
| 1 | **P0** | **Oversized uploads are the LCP image.** The service-page slide is a 551 KB PNG (11.3 s LCP). The banner is a 1376×600 JPG of 899 KB. Nothing converts uploads to WebP/AVIF. | Code + CLI | M | Live 12 Oct: library converted (3.2 GB → 628 MB in the main run); LCP re-measure pending |
| 2 | **P0** | **The first screen is hidden until our JS runs.** The header (`data-fade-down`) and the first section fade in from `opacity: 0`, so FCP waits for the bundle: 5.8 s on service pages. (SEO #17, was P2) | Code | S | Live 12 Oct. Service inner mobile FCP 5.8 → 1.87 s, LCP 11.3 → 1.87 s (with #1) |
| 3 | **P0** | **Homepage LCP is the cookie banner (7.3 s).** CookieYes waits for DOMContentLoaded, which waits for our bundle, which is starved of bandwidth by the tags, fonts and legacy CSS. | Code + CMS | S (+ #4–#6) | Open |
| 4 | **P1** | **Legacy CSS/JS on new templates**: `theme.min.css`, `mainstyle.css`, `typeformstyle.css`, jQuery in `<head>`, `theme.min.js`, `lozad`, `footer.js`, plus TweenMax 2 and `js/about.js` on /about/. (SEO #18, #23) | Code | L | Open |
| 5 | **P1** | **Marketing tags: ~1 MB and ~0.5 s of main thread per page.** The Ads tag loads twice, there are two GA4 IDs, the dead Universal Analytics `analytics.js` still loads, and Hotjar, Apollo and Meta are hard-coded. (SEO #25, was P3) | GTM + Code | M | Open |
| 6 | **P1** | **Fonts.** The kit has 7 weights (100 and 800 unused). `font-display: auto` hides text. `theme.min.css` adds its own old Proxima Nova faces, so 400 downloads twice and 400 italic once more. There is no preconnect. | Adobe + Code | S | Open |
| 7 | **P1** | **No srcset on work images.** The work row, work cards and parallax request `large` with no srcset. Parallax card 1 (`NUYU.jpg`, 315 KB) loads at 2.4 s on phones. (SEO #16, was P2) | Code | M | Open |
| 8 | **P2** | **Showreel modal poster.** The modal's `<video poster>` downloads the desktop poster (55 KB) on every page with a showreel, phones included, with the modal closed. | Code | S | Open |
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

The baseline was measured with three throwaway tools that were never committed, so there is no script yet. Write `scripts/perf-measure.mjs` to the spec below, and add `"perf": "node scripts/perf-measure.mjs"` to `package.json`.

- **Modes:**
  - **Default:** FCP, LCP and the LCP element, CLS, approximate TBT, the render-blocking list, the 15 largest requests, and CSS coverage per stylesheet.
  - `--profile`: main-thread JS time by script.
  - `--legacy-css`: the legacy rules each page uses (Appendix A).
- **Input:** a URL list and `mobile|desktop`.
- **Runs on:** the local Chrome, through the DevTools protocol, using Node 22's built-in WebSocket and no npm dependencies. It writes JSON to `build/perf/`, which is already gitignored.
- **Before/after:** run it before and after every item below, and record the numbers in that item's status line.

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
- **Still to do:** the LCP re-measure (PageSpeed Insights or #0).

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
- **Still to do.** About, Works and Insights were still served from a WP Rocket cache rebuilt mid-upload, with the old markup. Purge, then measure them.

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

**Fix:**
1. The bandwidth fixes do most of the work: #5 (remove the inline Ads tag), #6 (fonts) and #4 (legacy CSS/JS), so the bundle and DOMContentLoaded land around 2 s.
2. Add `<link rel="preconnect">` for `cdn-cookieyes.com`, and `use.typekit.net` with `crossorigin`.
   - Do it with `wp_resource_hints` in `function-dev.php` (new `fdry_resource_hints()`).
   - Drop the stale `dns-prefetch` for `ajax.googleapis.com` once #4 is done, since jQuery is served from the WP Rocket cache.
3. **Optional (decision):** use a more compact CookieYes layout on mobile (e.g. "box" or a shorter text) so the banner isn't the LCP element. This is a CookieYes dashboard setting, not code.

**Verify:** homepage mobile LCP < 4 s after #5 and #6, and < 3 s after #4. Check that the LCP element is either the hero label or the banner, and that it paints before about 3 s.

## P1: High

### #4 Legacy CSS and JS on new templates (absorbs SEO #18 and #23)

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
| GA4 `G-7TCWMDF3MD` | A second GA4 destination (seen in the collect hits) | — |
| Universal Analytics `analytics.js` | GTM. UA stopped processing data in July 2024 | 20 KB |
| GTM container `GTM-5D5B7P` | Inline | 176 KB |
| Meta CAPI param builder (`capi-automation…/clientParamBuilder.bundle.js`) | Loads before consent; source to confirm (probably GTM) | 56 KB |
| Meta Pixel `fbevents.js`, Hotjar | Inline, but CookieYes blocks them until consent, so they only add weight after "Accept" (not measured) | — |
| Apollo | Inline, loads **before** consent, with a random `?nocache=` on every load, so it is never cached | small |
| Klaviyo | Plugin | small |

- In total, about 1 MB of third-party transfer per page.
- On slow 4G the first `gtag/js` starts before our CSS finishes and holds the connection for 7 s.

**Fix:**
1. **Code (S):** delete the inline Ads `gtag.js` block from `header-new.php`. GTM already loads `AW-11543866131`. Confirm in GTM Preview that the Ads tag and its conversion linker fire from the container first.
2. **GTM (CMS, S):**
   - Delete the Universal Analytics tag.
   - Find out why there are two GA4 IDs (`G-37MNQT14EJ` and `G-7TCWMDF3MD`), and keep one.
   - Check that the Ads and GA4 tags use the same Google tag, so `gtag/js` loads once, not three times.
3. **Move into GTM behind CookieYes consent (M):** Hotjar, Apollo (without the `nocache` cache-buster) and the Meta Pixel. Remove their inline blocks from `header-new.php`. Fire Hotjar and Apollo on "Window Loaded" or after consent, never in `<head>`.
4. **Consent check.** The inline Ads tag currently runs **before** CookieYes in `<head>`, so it fires before consent. Moving it into GTM with Consent Mode fixes that too.

**Decision needed:** marketing confirms which Ads account, GA4 property, Hotjar and Apollo are still in use.

**Verify:**
- Exactly one `gtag/js` per destination. No `analytics.js`.
- Third-party transfer under about 500 KB per page before consent.
- GTM Preview shows the conversions still firing.
- Mobile JS time from Google tags under about 250 ms in the profile.

### #6 Fonts

**Evidence:**
- **The kit** (`use.typekit.net/rdq4arx.css`, enqueued as `foundry-typekit`) serves Proxima Nova in 100, 300, 400, 500, 600, 700 and 800, with `font-display: auto`.
- **`src/` uses** 300, 400, 500, 600 and 700.
- **Old faces in `theme.min.css`.** Its own `@font-face` rules point at an older kit: 300, 400, 400 italic, 700 and 900. So the 400 file downloads twice (`d7ff92` and `4838bd`), and an italic (`6aec08`, 33 KB) arrives at 7.3 s.
- Pages download 5–7 font files, 154–216 KB, at VeryHigh priority, which competes with the bundle.
- The kit CSS `@import`s `p.typekit.net/p.css`: a chained, render-blocking request.

**Fix:**
1. **Adobe Fonts web project** (CMS, S): remove weights 100 and 800, and set **Font display → swap**. Check first that no legacy template needs 800: grep `theme.css` and `mainstyle.css` for `font-weight: 800`/`900`.
2. **Old faces:** they go away with #4 step 3 on legacy-free pages. Don't edit `theme.css`.
3. **Preconnect:** to `use.typekit.net` (crossorigin) and `p.typekit.net`, in the same `fdry_resource_hints()` as #3.
4. **Optional:** ask Adobe support or check the kit settings for turning off the `p.typekit.net` tracking import. Some kits allow it, others don't.

**Verify:** at most 4–5 font requests on Home, all from `rdq4arx`, and text paints before the fonts arrive.

### #7 Responsive work images (supersedes SEO #16)

**Evidence:**
- `work-row.php`, `work-parallax.php` and `fdry_render_work_card()` (`library/function-work.php`) output a single `large` URL with no srcset or sizes.
- Desktop gets 768×1024 portrait crops at roughly double the displayed size in the 3-up grid. Retina still lacks a 2× candidate.
- `NUYU.jpg` (parallax card 1) is lazy but still loads at 2.4 s on phones. It sits within Chrome's lazy-load distance, so it competes with the hero.

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
   - Then #5 step 1 and #6 step 1, which are mostly settings and unblock #3.
   - Re-measure #3.
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
