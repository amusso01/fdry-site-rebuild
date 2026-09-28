# SEO plan: new (2026/27) templates

> **Status:** recorded 28 September 2026. Not started; to be implemented later.
> The team-facing version is `SEO audit and improvement plan.docx` in the project root. It uses the same item numbers.

## Context

The site now runs on the new templates: `header-new.php`, `components/`, `library/function-dev.php` and the Vite `src/`. Legacy code will be dropped later. This plan covers SEO improvements to the **new implementation only**. Decisions agreed:
- The eyebrow tagline stays the H1. We only guarantee exactly one H1 per template.
- Single work and single insight templates are out of scope.

Findings come from two sources: the code, and the live site (www.fdry.com, fetched 2026-09-28).
- **Plugins:** Yoast SEO 25.6 and WP Rocket 3.23 are active.
- **Live state:** live pages already render the new templates, but the live Insights page and footer are still behind the local code.

**Already good:**
- Yoast canonicals, sitemaps and the base schema graph.
- `lang="en-GB"`.
- The hero poster preload.
- Lazy images with width and height on most components.
- Alt fallbacks on work cards.
- The hero video never downloads on mobile.

## Priority legend

| Priority | Meaning |
|---|---|
| **P0 · Critical** | Actively costs rankings, link equity or crawl budget today. Fix first. |
| **P1 · High** | Clear SEO or Core Web Vitals win, affects every page or key pages. Next sprint. |
| **P2 · Nice to have** | Real but smaller gain, or needs measuring first. |
| **P3 · Optional** | Polish, housekeeping or future-facing. Do when convenient. |

## Priority list

| # | Priority | Issue | Type | Effort |
|---|---|---|---|---|
| 1 | **P0** | Every 404 returns a **301 to the homepage** (`404.php:14-16`). This causes soft-404s, hides broken URLs, and sends all missing URLs' equity to `/`. | Code | M |
| 2 | **P0** | **18 of the 28 footer links redirect**, on every page. They use `/service/…` and root slugs, but live is `/services/…` (`site-footer.php:28-78`). | Code | S |
| 3 | **P0** | `/works/` → 301 `?page_id=50` → 404 → 301 home. That is a redirect chain ending on the homepage. | Code + CMS | S |
| 4 | **P0** | **Missing H1** on `/work/` (the tagline is a `<p>`) and `/sectors/`. The Service and Service Child templates only get an H1 if an editor picks it (the ACF default is h2). | Code | S |
| 5 | **P0** | **Titles**: the WP Site Title is "Ecommerce Web Design \| WooCommerce and Shopify Agency". Every title ends in that 50-char suffix and the brand never appears. The homepage title is 127 chars and repeats itself. | CMS | S |
| 6 | **P1** | Mobile nav accordion prints **three `<h2>`s before the H1** on every page (`navigation/mobile.php:72`). | Code | S |
| 7 | **P1** | **No og:image** on Home, About, Services, Work or Contact. ACF-only pages have no content or featured image for Yoast to use. | Code + CMS | S |
| 8 | **P1** | The nav overlay embeds **3 × ~2.9 MB webm with `src` + `autoplay`** on every page (`navigation/secondary.php:140-155`). | Code | S |
| 9 | **P1** | **Homepage LCP contention**: all parallax cards are preloaded, and card 1 gets `fetchpriority="high"` alongside the hero poster. The work-row images are `eager`. | Code | S |
| 10 | **P1** | Contact has **no meta description**. Other new pages are unchecked. | CMS | S |
| 11 | **P1** | robots.txt blocks `AdsBot` (a Yoast setting) while the Google Ads tag `AW-11543866131` runs. Verify, then turn it off if Ads land on the site. | CMS | S |
| 12 | **P2** | Organization schema has only name and logo: no address, phone, email or sameAs. | Code + CMS | S |
| 13 | **P2** | No **Service schema** on service pages. | Code | M |
| 14 | **P2** | **Work archive not crawlable**: 9 of 150 works are shown, the filters are `<button>`s with no URLs, and there are no category landing pages. | Code | L |
| 15 | **P2** | Tag (22) and category (5) archives are indexable legacy pages that are thin and duplicated. Set them to noindex. | CMS | S |
| 16 | **P2** | Work images have **no srcset**, so phones download 1024px files. | Code | M |
| 17 | **P2** | First-section heading and title **fade in after JS plus a 0.5 s delay**, which may push LCP on text-led pages. Measure first. | Code | S |
| 18 | **P2** | Render-blocking legacy CSS (`theme.min.css` is 476 KB) plus third-party tags in `<head>`. Use WP Rocket Remove Unused CSS and Delay JS. | CMS | M |
| 19 | **P2** | Editor content: vague eyebrows (they are the H1 now), a piped homepage H1, a duplicate homepage H2, and a thin `/services/` page. | Content | M |
| 20 | **P3** | One-column template has no guaranteed H1. The Insights and Contact H1s vanish when the tagline is empty. | Code | S |
| 21 | **P3** | `CollectionPage` schema type for Work and Insights. | Code | S |
| 22 | **P3** | Housekeeping: the unused `jquery` dependency on `fdry-scripts`, the dead `#loading-animation` script, `<link rel="pingback">`, and the hard-coded `© 2025`. | Code | S |
| 23 | **P3** | Load more as a real `/work/page/N/` link. | Code | M |
| 24 | **P3** | Move the Ads tag, Hotjar, Apollo and Meta Pixel into GTM behind CookieYes. The inline pixel plus the plugin probably fire PageView twice. | CMS | M |
| 25 | **P3** | Yoast extras: "ACF Content Analysis for Yoast SEO" and llms.txt. llms.txt fits the GEO services you sell. | CMS | S |

**Flagged, out of scope:** `single.php:31` "BACK TO WORK" → `get_the_permalink(50)` → `?page_id=50` → 404 → home, on all 150 case studies. It's a one-line fix whenever you want it.

---

## P0: Critical

### #1 Real 404 page
- Move the current `404.php` body (the redirect plus old markup) to `components/page/legacy-404.php` so it can be rolled back. This follows the `legacy-home.php` / `legacy-footer.php` pattern and the keep-replaced-files rule.
- The new `404.php` calls `get_header('new')` and renders `components/page/not-found.php`:
  - an H1 "Page not found" and a short line of text;
  - links to Services, Work, Insights and Contact, reusing `centered-content` and `components/partials/button.php` where possible.
- WordPress already sends the 404 status, and Yoast adds noindex.
- If styles are needed, add `src/styles/templates/_not-found.scss` and import it from `main.scss`.
- **Before deploying:**
  1. Export the "Not found (404)" and "Page with redirect" URLs from Search Console, plus server and Cloudflare 404 logs.
  2. For every URL with backlinks or traffic, add a 301 using Yoast Premium redirects or the Redirection plugin.

### #2 Footer links → final URLs
Update the `$menus` arrays in `components/footer/site-footer.php`:

| Current path | New path |
|---|---|
| `/service/brand-creative/` | `/services/create/` |
| `/service/ux-ui/` | `/services/ux-ui-design/` |
| `/service/ecommerce/` | `/services/ecommerce-web-agency/` |
| `/service/b2b-seo-agency-london/` | `/services/b2b-seo-agency/` |
| `/service/ai-seo-agency-london/` | `/services/ai-search-optimisation/` |
| `/service/` | `/services/` |
| `/service/web-design-agency/`, `/service/seo-agency/`, `/service/geo-marketing-agency/`, `/service/ecommerce-seo-agency/`, `/service/technical-seo-agency/`, `/service/paid-advertising/`, `/service/social-media-marketing/`, `/service/email-marketing/` | Same slug under `/services/` |
| `/shopify-agency/`, `/woocommerce-agency/`, `/wordpress-agency/`, `/adobe-commerce-agency/` | Same slug under `/services/` |

### #3 `/works/` archive
- In `library/function-work.php`, add a `template_redirect` hook:
  - When `is_post_type_archive('works_post')` is true, 301 with `wp_safe_redirect()` to the page using `template-work.php`.
  - Find that page with `get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'template-work.php'])`, falling back to `home_url('/work/')`.
- In the CMS, find and delete the stale `/works/ → ?page_id=50` rule (Yoast Premium, Redirection or `.htaccess`).

### #4 H1 on every new template (tagline stays the H1)
Reuse the fallback in `centered-content.php:1134-1137` ("A page H1 must never be empty; fall back to the page title").
- **Service templates:** in `template-service.php` and `template-service-child.php`, pass `'tagline_tag' => 'h1'` to the first `centered-content`. `template-about.php` and `template-service-inner.php` already do this.
- **Homepage:** in `front-page.php` and `template-home.php`, pass `'tagline_tag' => 'h1'` to `intro-content`, and add the empty-H1 fallback to `intro-content.php`.
- **Work archive:** in `components/page/work-archive.php`, change the tagline `<p>` to `<h1>`. It already falls back to "WORK".
- **ACF JSON** (use the `acf-json-sync` skill):
  - Remove `h1` from the `choices` of every non-first `*_tagline_tag`: `two_column_tagline_tag` and `two_column_2_tagline_tag` in `group_6a969320685b0.json` and `group_6aa2aa6f622e4.json`, and `centered_dark_tagline_tag` in `group_6aa185527a283.json`.
  - Delete the now-overridden first-section `intro_tagline_tag` and `centered_tagline_tag` fields.

### #5 Titles and brand (Yoast, no code)
- Set Yoast "Website name" to `FDRY` (or change the WP Site Title), the alternate name to `Foundry Digital`, and the separator to `|`.
- Write custom SEO titles of 60 characters or fewer for Home and the top service pages, e.g. "Ecommerce Web Design Agency London | FDRY".

## P1: High

### #6 Nav headings
In `components/navigation/mobile.php:72`, change `<h2 class="ac-header …">` to `<div class="ac-header …">`, matching the footer accordion. Check that `navAccordion.js` and accordion-js select by class, not by tag.

### #7 og:image
- **Code:** in the new `library/function-seo.php` (see "New file" below), hook `wpseo_add_opengraph_images`.
  - It applies to pages that use one of `fdry_acf_only_page_templates()` and have no featured image.
  - It adds the first available of `hero_poster`, `showreel_poster`, `banner_desktop_image`, `banner_image`, then the first `work_row` card thumbnail.
  - Use `$container->add_image_by_id()`, and normalise ACF values with `fdry_image_parts()`.
- **CMS:** set Yoast → Site basics → Site image (1200×630) as the last fallback.

### #8 Nav overlay videos
- In `components/navigation/secondary.php`, change `src` to `data-src`, drop `autoplay`, and set `preload="none"`.
- In `navMenu.js` `playPanelVideos()`, copy `dataset.src` to `src` on first play.
- Delete the visible "Video placeholder" text and leave the placeholder empty.

### #9 Homepage LCP
- In `function-dev.php` `fdry_preload_work_parallax_images()`, drop `fetchpriority="high"` from card 1 and stop preloading cards 2 onwards.
- In `work-parallax.php`, give cards after the first `loading="lazy"`.
- In `work-row.php`, change `loading="eager"` to `loading="lazy"`.
- Test that the sticky parallax still lines up on cold loads. Per FRONTEND.md, the ScrollTrigger refresh on body-height change should cover late images.

### #10 / #11 CMS
- Add a meta description to Contact, and check every new-template page for missing ones.
- If Google Ads campaigns land on fdry.com, turn off Yoast → Advanced → Crawl optimisation → "Prevent Google AdsBot from crawling".

## P2: Nice to have

### #12 Organization schema
- Move the footer's `$contact` array into `fdry_contact_details()` in `function-dev.php`. Both `site-footer.php` and the schema use it, so the data lives in one place.
- In `function-seo.php`, hook `wpseo_schema_organization` and add:
  - `address` as a PostalAddress (123 Buckingham Palace Rd, London, SW1W 9SH, GB);
  - `telephone`, `email`, and `areaServed: GB`.
- Add the Instagram and LinkedIn URLs in Yoast → Site representation. They become `sameAs`.

### #13 Service schema
Hook `wpseo_schema_graph`.
- **Applies to:** pages that are `/services/` descendants and use the Service, Service Child or Service Inner templates.
- **Appends this node:**

  ```json
  {
    "@type": "Service",
    "@id": "<url>#service",
    "name": "<H1 tagline or title>",
    "description": "<Yoast meta description>",
    "provider": { "@id": "<home>#organization" },
    "areaServed": "GB",
    "url": "<url>"
  }
  ```
- Set the WebPage node's `mainEntity` to that node.

### #14 Crawlable work archive
- **Filters as links:** in `work-archive.php`, render each filter as `<a class="work-archive__filter" href="/work/category/{slug}/" data-category="…">` instead of a `<button>`. "Featured" links to `/work/`.
- **JS:** in `workArchive.js`, prevent the default click, keep the current AJAX swap, and `history.pushState` the category URL. Use `aria-current` instead of `aria-pressed`.
- **Category argument:** `work-archive.php` accepts an optional `category` arg, passes it to `fdry_work_query()` (which already supports slugs), and pre-selects that filter.
- **Category landing pages:** rebuild `archive-works-category.php`. It is already routed by `functions.php:178-199`.
  1. Move the legacy body to `components/page/legacy-works-category.php` for rollback.
  2. The new body is `get_header('new')`, then `<main>` containing `work-archive` with `category => get_query_var('category_name')`.
  3. The H1 is "{Category name} work".
  4. In `function-seo.php`, use `wpseo_title`, `wpseo_metadesc` and `wpseo_canonical` to give each page a unique title, a description taken from the term description, and a self-canonical.

### #15 Archives (CMS)
In Yoast, turn off "Show in search results" for Tags and Categories. The work category landing pages from #14 replace the category archives.

### #16 Responsive work images
In `work-row.php`, `work-parallax.php` and `fdry_render_work_card()`, output images with `wp_get_attachment_image()` plus `sizes`, as `insight-archive.php` already does. Parallax card 1 stays eager.

### #17 First-section fade
Run Lighthouse on About, Services and Contact. If the LCP element is the fading tagline or title, remove `data-fade-up-delay="0.5"` or the fade itself for `is_first` sections.

### #18 WP Rocket (CMS)
Turn on Remove Unused CSS and Delay JS for third-party tags. Test the legacy templates afterwards, because Remove Unused CSS can drop rules they need.

### #19 Content notes for editors
- Write descriptive eyebrows, e.g. "SERVICES" → "Ecommerce, web & growth services".
- Remove the pipes from the homepage H1.
- De-duplicate the two homepage "LONDON ECOMMERCE WEB DESIGN AGENCY" H2s.
- Expand `/services/`, which has only an H1 and one h4.

## P3: Optional

- **#20 H1 fallbacks:**
  - In `template-one-column.php`, print `<h1 class="one-column__title">` with the page title when `post_content` has no `<h1`.
  - In `insight-archive.php` and `template-contact.php`, fall back to `get_the_title()` when the tagline is empty.
- **#21 CollectionPage:** hook `wpseo_schema_webpage_type` and return `CollectionPage` for `template-work.php` and `template-insight.php`.
- **#22 Housekeeping:**
  - Drop `jquery` from the `fdry-scripts` dependencies (`function-dev.php:303`). `src/` does not use jQuery.
  - In `header-new.php`, remove the `#loading-animation` inline script (lines 32-41; the element only exists in `legacy-home.php`) and `<link rel="pingback">`.
  - In the footer, replace `© 2025 FDRY` with `wp_date('Y')`.
- **#23 Load more as a link:** make "Load more" an `<a href="…/page/2/">` that JS intercepts, and have `work-archive.php` honour `get_query_var('paged')`.
- **#24 Tags into GTM:** move the Google Ads tag, Hotjar, Apollo and Meta Pixel from `header-new.php` into GTM, triggered by CookieYes consent.
- **#25 Yoast extras:** install "ACF Content Analysis for Yoast SEO", and turn on Yoast's llms.txt feature.

---

## New file
`library/function-seo.php` holds all the Yoast hooks (#7, #12, #13, #14, #21).
- Require it from `functions.php` next to `function-work.php`.
- Guard every hook with `defined('WPSEO_VERSION')`.

## Documentation
Add an "SEO" section to `FRONTEND.md` covering:
- the one-H1 rule and the `tagline_tag` args;
- the `function-seo.php` hooks;
- the og:image fallback order;
- the 404 page and its rollback;
- the work category URLs;
- the nav video lazy-load.

Also update the "Homepage" preload note.

## Suggested order
1. **P0 code and CMS:** #1–#5. Ship together, with the redirect map from Search Console done first.
2. **P1:** #6–#11.
3. **P2:** #12, #13 and #16, which are small code changes; then #14 as its own piece of work; #15, #17 and #18 after measuring.
4. **P3:** as time allows.

## Verification
Measure a baseline before starting. PageSpeed's anonymous API quota was used up today, so use pagespeed.web.dev or Lighthouse in Chrome on Home, About, Services / Shopify, and Work.
1. Run `php -l` on every changed PHP file, then `pnpm build`. Deploy `dist/` and `.vite/manifest.json`.
2. `curl -sI https://www.fdry.com/does-not-exist/` returns `404`, and the page uses the new layout.
3. Re-run the footer link loop (`curl -w '%{http_code}'` over the `site-footer.php` paths). Every link returns 200, and `/works/` is a single 301 to `/work/`.
4. Fetch Home, About, Services, a service child, a service inner, Sectors, Work, Insights and Contact. Count the H1s with a script: exactly one per page, with no `<h2>` before it.
5. Every new-template page has an `og:image`.
6. Run the Rich Results Test or validator.schema.org on Home and a service page. The Organization node has an address and phone, and the Service node is linked from the WebPage.
7. `/work/category/design/` returns 200 with the new layout, an H1, a unique title and a self-canonical. Clicking a filter on `/work/` updates the URL.
8. In DevTools Network with a cold cache, no nav `.webm` loads until the menu opens. On Home, only the hero poster has `fetchpriority=high`.
9. Compare Lighthouse mobile LCP, TBT and page weight against the baseline.
10. Resubmit the sitemap in Search Console. Watch "Not found", "Page with redirect" and Core Web Vitals for 2–4 weeks.
