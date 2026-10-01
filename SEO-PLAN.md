# SEO plan: new (2026/27) templates

> **Status:** first recorded 28 September 2026, rebuilt 1 October 2026 after the footer rebuild and the new single Insight page. Not started; to be implemented later.
> `SEO audit and improvement plan.docx` predates this rebuild: its item 2 is now resolved and the numbering has changed. Regenerate it from this plan before the meeting.

## What changed since 28 September

Re-checked against the code and the live site (www.fdry.com) on 1 October 2026.

- **Resolved: footer redirects.** The footer now prints the "Footer menu 1" and "Footer menu 2" locations (`footermenu_1`, `footermenu_2`) instead of hard-coded `/service/…` arrays. Every footer link now returns 200 with no redirect.
- **New critical finding: duplicate and orphaned pages.** Four leftover pages are live, self-canonical and in the sitemap. One of them is linked from the footer's "Ecommerce" item (see #2).
- **Partly done: titles.** The homepage now has a custom title ("Ecommerce Web Design Agency London | Shopify + WooCommerce | FDRY", 65 characters). Every other page still ends in the 50-character slogan (see #5).
- **Now in scope: single Insight.** It has been rebuilt as `components/single/insight.php` and gets the basics right: one H1, an eager high-priority hero image with alt fallback and srcset, and links built from page templates instead of hard-coded IDs. The small gaps left are #20 and #27.
- **New helper to reuse:** `fdry_template_page_url()` in `library/function-dev.php` returns the permalink of the page using a given template. Use it for #3 and for the "Back to work" link.
- **Internal linking is still fine.** The new footer menus link to fewer pages, but every `/services/` page is linked site-wide from the nav overlay, and the five sector pages are one click away from `/sectors/`.
- **Still open, unchanged:** the 404 → homepage redirect, the `/works/` redirect chain, the missing H1 on `/work/` and `/sectors/`, the nav H2s before the H1 on every page, and the missing og:image on Home, About, Contact, Work, Services, service pages and Insights. Also unchanged: Contact's missing meta description, the AdsBot block, and all the code items.

## Context

The site runs on the new templates: `header-new.php`, `components/`, `library/function-dev.php` and the Vite `src/`. Legacy code will be dropped later. This plan covers SEO improvements to the **new implementation only**.

**Decisions:**
- The eyebrow tagline stays the H1. We only guarantee exactly one H1 per template.
- Single case studies (works) are out of scope; they still use the legacy template.
- Single Insights are now in scope, because they're on the new design.

**Plugins:** Yoast SEO 25.6 and WP Rocket 3.23 are active.

**Already good:**
- Yoast canonicals, sitemaps and the base schema graph.
- `lang="en-GB"`.
- The hero poster preload.
- Lazy images with width and height on most components.
- Alt fallbacks on work cards.
- The hero video never downloads on mobile.
- The footer menus are now editable in WordPress.
- The single Insight hero is set up for LCP.

## Priority legend

| Priority | Meaning |
|---|---|
| **P0 · Critical** | Actively costs rankings, link equity or crawl budget today. Fix first. |
| **P1 · High** | Clear SEO or Core Web Vitals win, affects every page or key pages. Next sprint. |
| **P2 · Nice to have** | Real but smaller gain, or needs measuring first. |
| **P3 · Optional** | Polish, housekeeping or future-facing. Do when convenient. |

## Priority list

| # | Priority | Issue | Type | Effort | Status |
|---|---|---|---|---|---|
| 1 | **P0** | Every 404 returned a **301 to the homepage** (`404.php`). This caused soft-404s, hid broken URLs, and passed missing URLs' equity nowhere. | Code + CMS | M | **Code done 1 Oct**; pre-deploy redirect export pending |
| 2 | **P0** | **Duplicate and orphaned pages are live and in the sitemap**, two of them linked from the footer. | CMS | S | **Deferred** until the footer menu build is done |
| 3 | **P0** | `/works/` → 301 `?page_id=50` → 404, plus every link to the unpublished old Work page (ID 50), including "BACK TO WORK" on 150 case studies and the Yoast breadcrumbs. | Code + CMS | S | **Done 1 Oct** (live, checked) |
| 4 | **P0** | **Missing H1** on `/work/` (the tagline is a `<p>`) and `/sectors/`. The Service and Service Child templates only get an H1 if an editor picks it (the ACF default is h2). | Code + CMS | S | **Done 1 Oct** (live, checked) |
| 5 | **P0** | **Titles**: every page except Home ends in the 50-character slogan "Ecommerce Web Design \| WooCommerce and Shopify Agency", and the brand never appears. For example, a blog post title runs to 113 characters. | CMS | S | Partly done (Home); fix is the WP Site Title |
| 6 | **P1** | Mobile nav accordion prints **three `<h2>`s before the H1** on every page (`navigation/mobile.php:74`). | Code | S | Open |
| 7 | **P1** | **No og:image** on Home, About, Contact, Work, Services, service pages and Insights. ACF-only pages have no content or featured image for Yoast to use. | Code + CMS | S | Open |
| 8 | **P1** | The nav overlay embeds **3 × ~2.9 MB webm with `src` + `autoplay`** on every page (`navigation/secondary.php`). | Code | S | Open |
| 9 | **P1** | **Homepage LCP contention**: all parallax cards are preloaded, and card 1 gets `fetchpriority="high"` alongside the hero poster. The work-row images are `eager`. | Code | S | Open |
| 10 | **P1** | Contact has **no meta description**. Other new pages are unchecked. | CMS | S | Open |
| 11 | **P1** | robots.txt blocks `AdsBot` (a Yoast setting) while the Google Ads tag `AW-11543866131` runs. Verify, then turn it off if Ads land on the site. | CMS | S | Open |
| 12 | **P2** | Organization schema has only name and logo: no address, phone, email or sameAs. | Code + CMS | S | Open |
| 13 | **P2** | No **Service schema** on service pages. | Code | M | Open |
| 14 | **P2** | **Work archive not crawlable**: 9 of 150 works are shown, the filters are `<button>`s with no URLs, and there are no category landing pages. | Code | L | Open |
| 15 | **P2** | Tag (22) and category (5) archives are indexable legacy pages that are thin and duplicated. Set them to noindex. | CMS | S | Open |
| 16 | **P2** | Work images have **no srcset**, so phones download 1024px files. | Code | M | Open |
| 17 | **P2** | First-section heading and title **fade in after JS plus a delay**, which may push LCP on text-led pages. Measure first. | Code | S | Open |
| 18 | **P2** | Render-blocking legacy CSS (`theme.min.css` is 476 KB) plus third-party tags in `<head>`. Use WP Rocket Remove Unused CSS and Delay JS. | CMS | M | Open |
| 19 | **P2** | Editor content: vague eyebrows (they are the H1 now), a piped homepage H1, a duplicate homepage H2, and a thin `/services/` page. | Content | M | Open |
| 20 | **P2** | **Single Insight** shows no date or author, and ends without links to related insights or services. | Code | M | **New** |
| 21 | **P3** | One-column template has no guaranteed H1. The Insights and Contact H1s vanish when the tagline is empty. | Code | S | Open |
| 22 | **P3** | `CollectionPage` schema type for Work and Insights. | Code | S | Open |
| 23 | **P3** | Housekeeping: the unused `jquery` dependency on `fdry-scripts`, the dead `#loading-animation` script, `<link rel="pingback">`, and the hard-coded `© 2025 FDRY`, which survived the footer rebuild. | Code | S | Open |
| 24 | **P3** | Load more as a real `/work/page/N/` link. | Code | M | Open |
| 25 | **P3** | Move the Ads tag, Hotjar, Apollo and Meta Pixel into GTM behind CookieYes. The inline pixel plus the plugin probably fire PageView twice. | CMS | M | Open |
| 26 | **P3** | Yoast extras: "ACF Content Analysis for Yoast SEO" and llms.txt. llms.txt fits the GEO services we sell. | CMS | S | Open |
| 27 | **P3** | Single Insight images: the URL-only hero fallback and the "Our Work" banner have no width/height, and the banner uses hard-coded absolute PNG URLs. | Code | S | **New** |
| 28 | **P3** | Cloudflare email obfuscation turns the footer email into a `/cdn-cgi/l/email-protection#…` link, which returns 404 to crawlers. | CMS | S | **New** |

**Resolved since 28 September:** the footer links that redirected. The footer now uses WordPress menus, and all its links return 200 directly.

**Flagged, out of scope:** nothing at the moment. The "BACK TO WORK" link on the case studies was fixed under #3.

---

## P0: Critical

### #1 Real 404 page
> **Done in code (1 Oct 2026).** The redirect is removed from `404.php`, which now renders the existing "Oops!" page (`_error-page.scss`) with a 404 status. A search of the theme found no other redirect (no `wp_redirect`, `template_redirect`, JS or `.htaccess`). The live headers show nothing at Cloudflare or server level either. **Confirmed live on 1 Oct:** a non-matching URL now returns 404. **Still to do:** steps 1–2 below, using the 301 Redirects plugin.

- **What changed:** the `header()` / `exit()` redirect is gone. `404.php` renders the "Oops!" page that was already built below it: `main.error-page`, styled in `_error-page.scss`, with an `h1`, an `h2` and a homepage link. The site header and footer navigation are on the page too.
- WordPress sets the 404 status before the template loads, and Yoast adds noindex.
- **Rollback:** re-add the three redirect lines above `get_header('new')`. The exact snippet is in FRONTEND.md → 404.
- **Optional later:** add links to Services, Work, Insights and Contact under the homepage link.
- **Expected after deploy:** WordPress core may still 301 a mistyped URL when it can guess the intended page from the slug (`redirect_guess_404_permalink`). That's normal. Browsers that cached one of the old permanent redirects keep following it until their cache clears; Google will see the 404s on its next crawl.
- **Before deploying:**
  1. Export the "Not found (404)" and "Page with redirect" URLs from Search Console, plus server and Cloudflare 404 logs.
  2. For every URL with backlinks or traffic, add a 301 to the closest live page, using the 301 Redirects plugin that's already installed.
  3. ~~Review CMS redirect rules that target the homepage.~~ Checked 1 Oct: there are none. Every homepage redirect, `/service/build/security/` and `?page_id=50` included, came from `404.php`. The responses carry WordPress's 404 no-cache headers (`Expires: Wed, 11 Jan 1984`). Of the other redirects, some may be rules in the 301 Redirects plugin (it has no REST routes, which is why it was missed at first). Others are WordPress core guessing the target (`redirect_guess_404_permalink`): made-up partial slugs redirect too, e.g. `/shopify-agenc/` → `/services/shopify-agency/`. Make a guess explicit in the plugin if it matters. `/works/` was a plugin rule; see #3.
- **Solid Security 404 detection (found 1 Oct).** Now that missing URLs return real 404s, Solid Security (iThemes) counts them and locks out any IP that hits too many in a short time. It returned a 403 "Your access to this site has been temporarily denied" on every PHP page, wp-login included, after our redirect audit hit about 20 404s from the office IP (151.83.242.197). Before the fix, 404s redirected and never counted.
  - **Risk:** the same lockout can hit Googlebot or real visitors following several old links.
  - **Action:** in Solid Security, turn off 404 Detection (Cloudflare already filters bots) or raise its threshold a lot. Add the office IP to Authorized IPs. Check that proxy detection reads Cloudflare's `CF-Connecting-IP`; otherwise a lockout can land on a Cloudflare edge IP and block everyone behind it.
- **Old rules in the 301 Redirects plugin (found 1 Oct).** Many rules date from before the rebuild and pick their target **by page ID**. When that page was unpublished in the rebuild, the rule fell back to `?page_id=N` and now ends on the 404 page. Example: `/service/create/web-design/` (about 2,900 hits) → `?page_id=6877` → 404.
  - **Spotting rules from outside:** plugin rules send no `x-redirect-by` header. WordPress's own guesses send `x-redirect-by: WordPress`.
  - **Fix:** don't delete rules that still get hits. Repoint each one at the new page, **typed as a URL**, so the next rebuild can't break it again.
  - **Old page IDs referenced in the legacy theme, checked live on 1 Oct:**

    | Old ID | Old page | Now | Repoint rules to |
    |---|---|---|---|
    | 10 | Services (old parent) | 404 | `/services/` |
    | 16 | About | 404 | `/about/` |
    | 21 | Build (old parent) | not public | `/services/build/` |
    | 50 | Work | 404 | `/work/` |
    | 5321 | "Work with us" | 404 | `/careers/` |
    | 6801 | Create | 404 | `/services/create/` |
    | 6821 | Grow | 404 | `/services/growth/` |
    | 6828 | Brand & Creative | 404 | `/services/create/` (or `/services/brand-identity/`) |
    | 6833 | UX & UI | 404 | `/services/ux-ui-design/` |
    | 6877 | Web Design | 404 | `/services/web-design-agency/` |
    | 6892 | Ecommerce (the `__trashed` duplicate, #2) | live, to be removed | `/services/ecommerce-web-agency/` |
    | 6929 | SEO Services & AI Search | 404 | `/services/seo-agency/` |
    | 6940 | Email Marketing | 404 | `/services/email-marketing/` |
    | 6949 | Paid Media Ads | 404 | `/services/paid-advertising/` |
    | 7020 | WooCommerce Agency | 404 | `/services/woocommerce-agency/` |
    | 7042 | Shopify Agency | 404 | `/services/shopify-agency/` |
    | 7634 | Social Media Marketing | not public | `/services/social-media-marketing/` |
    | 14, 1355, 1361 | Insights, Brief, Agency Life | live | Fine; retype as URLs when convenient |
  - **Other rules:** export the full rule list (source, target, hits) and test every source live. Keep rules that land on a 200 in one hop. Retarget chains and homepage targets. Delete any rule whose source is now a live page, because the plugin runs before WordPress and would hide that page.
  - **Rule audit, 1 Oct (after repointing):** all 36 active rules 301 once to a live, indexable page that is its own canonical. None fall back to `?page_id=`. Still to do in the plugin:
    - Turn on rules 1 and 2 (`/service/build/woocommerce-agency/`, `/service/build/shopify-agency/`). Both are OFF, and their URLs now return 404.
    - Delete duplicate rules 16, 17 and 42–48. Each has the same source and target as an older rule.
    - `/service/create/` has no rule. WordPress's slug guess currently sends it to `/services/create/`.
  - The rules target page IDs again, so they follow slug changes. If a page is ever rebuilt as a new page, though, its rules fall back to `?page_id=` again. Re-check this list after any rebuild.
- **Sequencing with #2:** now that this is live, an unpublished page returns 404. Add the duplicate pages' 301s in the 301 Redirects plugin before unpublishing them.

### #2 Duplicate and orphaned pages (CMS) — deferred
> **Deferred** until the footer menu build is finished. Nothing has been changed in the CMS yet. The findings and decisions below are ready for when we pick it up.

**Findings (checked 1 Oct 2026):**
- **Redirect tool: the 301 Redirects plugin is already installed.** It has no REST routes, so the first check missed it. Yoast is the free version, which has no redirect manager.
- **The "-new" sector pages are rebuilds, not leftovers.**
  - `/retail-ecommerce-new/` (page 15272) and `/healthcare-wellness-new/` (page 15296) were published on 21 Sep on the Service Inner template, with about 660 words each.
  - The originals `/sectors/retail-ecommerce/` (8907) and `/sectors/healthcare-wellness/` (8905) still use the legacy subservice template.
- **`/service__trashed/ecommerce/`** (page 6892, legacy, parent 10 trashed) duplicates `/services/ecommerce-web-agency/` (page 15000), which has the same H1 "London Ecommerce Agency". It's no longer in the footer.
- **`/service__trashed/build__trashed/security/`** (page 3070, parent 21 trashed) is a 2020 "Website Security" page with no current equivalent.
- **Only the footer links to these URLs.** A crawl of all 127 sitemap URLs found site-wide links to the two "-new" pages from the footer, and nothing else beyond each page linking to itself.

**Decisions:**
1. **Redirect tool:** use the 301 Redirects plugin that's already installed. Don't add Redirection. Enter the target as a URL (e.g. `/services/ecommerce-web-agency/`) rather than picking a page, so it can't fall back to `?page_id=` if that page is ever unpublished. The plugin won't add redirects when a slug changes, so add the "-new" → `/sectors/` rules by hand during the swap.
2. **Sector pages: not ready yet.**
   - Now: set both "-new" pages to noindex in Yoast (Advanced → "Allow search engines to show this page" → No), which also drops them from the sitemap. Point the footer items back at the `/sectors/` originals.
   - Later, once they're finished:
     1. Trash the legacy originals 8907 and 8905. That frees their slugs.
     2. Give each new page the parent "Sectors" (9015) and the original slug (`retail-ecommerce`, `healthcare-wellness`).
     3. Add the 301 from each "-new" URL to its `/sectors/` URL in the 301 Redirects plugin.
     4. Switch Yoast back to index.
     5. Check the footer: page-type menu items follow the new URL on their own.
3. **Security page: keep it and rebuild later.**
   - Now: noindex it in Yoast.
   - When it's rebuilt under `/services/`: 301 both `/service__trashed/build__trashed/security/` and `/service/build/security/` to the new page.
4. **Ecommerce duplicate:** in the 301 Redirects plugin, add 301s from `/service__trashed/ecommerce/` and `/service/ecommerce/` to `/services/ecommerce-web-agency/`. Then trash page 6892.

**Verify when done:**
- `page-sitemap.xml` has no `__trashed` or `-new` URLs.
- `/service__trashed/ecommerce/` is a single 301 to `/services/ecommerce-web-agency/`.
- The "-new" and Security pages carry `noindex`.
- The footer links only to `/sectors/` and `/services/` URLs.

### #3 `/works/` archive and page-50 links
> **Deployed and checked, 1 Oct 2026.** `/works/`, `/works`, `/?post_type=works_post` and `/works/feed/` each take one 301 to `/work/`. `/work/category/design/` returns 200, and the case studies' "BACK TO WORK" and the category page's "Featured" link go to `/work/` with no `page_id=50`. **Breadcrumb:** Yoast had stored `/works/` in its indexables, so `fdry_works_breadcrumb()` was added. Live, the case studies' BreadcrumbList item 2 is now "Work" → `/work/`. **Plugin:** the `/works` rule is deleted from the 301 Redirects plugin. `/works/` and `/works` now carry `x-redirect-by: FDRY theme`.

**What we found:**
- Page 50, the old Work page, is unpublished. Every link to it now hits the 404 page, including "BACK TO WORK" on all 150 case studies.
- Yoast's case-study breadcrumbs listed "Works" → `/works/`.
- `/?post_type=works_post` rendered the legacy "Works Archive", a duplicate of `/work/`.
- The `/works` rule isn't in the theme or WordPress core. It matches the path exactly, so it's a stored rule on the server.

**Code (done):**
- `library/function-work.php`:
  - `fdry_works_archive_link()` filters `post_type_archive_link` to the Work page, which fixes the breadcrumbs and canonicals.
  - `fdry_redirect_works_archive()` 301s the works archive (`/works/`, its feed and `?post_type=works_post`) to `/work/` on `template_redirect` priority 1. It skips `/work/category/{slug}/`.
- `get_the_permalink(50)` → `fdry_template_page_url('template-work.php', '/work/')` in `single.php`, `archive-works-category.php`, `page-templates/typeform.php`, `pageservices.php`, `mainservice.php` (×2) and `parent.php`.
- **Left for #14:** `archive-works-category.php:7` still prints page 50's content as the intro. Keep page 50 (unpublished) until #14 replaces that.

**CMS:** the `/works` rule lives in the 301 Redirects plugin, now pointing at the Work page. Delete it once the code is live: it stores the target by page ID, which is how it broke when page 50 was unpublished.

**Verify after deploy and deleting the rule:**
- `/works/`, `/works`, `/?post_type=works_post` and `/works/feed/` each return a single 301 to `/work/`.
- `/work/category/design/` returns 200.
- On `/works/nuyu/`, the breadcrumb item 2 is `/work/`, and the page contains no `page_id=50`.

### #4 H1 on every new template (tagline stays the H1)
> **Status, 1 Oct.** A sitemap scan (41 pages) found only `/work/` and `/sectors/` without an H1. Every other page has exactly one.
> - **Work: done, live.** In `work-archive.php` the tagline is now an `<h1>`, with `color: inherit` added in `_work-archive.scss` so it looks the same. The New Work group has no tag field, so this couldn't be set in the CMS. The H1 text is the ACF "Tagline" (currently "WORK").
> - **Sectors: done, live.** The first section's "Tagline tag" is set to H1 in the CMS, so the H1 is "Sectors".
> - **Not done (optional hardening):** the template and ACF changes below. They would stop an editor from removing an H1 by accident, but every page is correct today.

Reuse the fallback in `centered-content.php` ("A page H1 must never be empty; fall back to the page title").
- **Service templates:** in `template-service.php` and `template-service-child.php`, pass `'tagline_tag' => 'h1'` to the first `centered-content`. `template-about.php` and `template-service-inner.php` already do this.
- **Homepage:** in `front-page.php` and `template-home.php`, pass `'tagline_tag' => 'h1'` to `intro-content`, and add the empty-H1 fallback to `intro-content.php`.
- **Work archive:** in `components/page/work-archive.php`, change the tagline `<p>` to `<h1>`, keeping its new `data-fade-up`. It already falls back to "WORK".
- **ACF JSON** (use the `acf-json-sync` skill):
  - Remove `h1` from the `choices` of every non-first `*_tagline_tag`: `two_column_tagline_tag` and `two_column_2_tagline_tag` in `group_6a969320685b0.json` and `group_6aa2aa6f622e4.json`, and `centered_dark_tagline_tag` in `group_6aa185527a283.json`.
  - Delete the now-overridden first-section `intro_tagline_tag` and `centered_tagline_tag` fields.

### #5 Titles and brand (CMS, no code) — partly done
**Where the slogan comes from (checked 1 Oct).** The suffix is Yoast's `%%sitename%%`, which reads the **WordPress Site Title** (Settings → General), not Yoast's "Website name". The Site Title is "Ecommerce Web Design | WooCommerce and Shopify Agency". The same value shows up in `og:site_name` and in the header's `apple-mobile-web-app-title`. Yoast's "Website name" is a separate setting ("Ecommerce Web Design | Digital Marketing Agency"). It only feeds the WebSite schema, which Google uses for the site name shown above results. The earlier note here said that setting fixed the suffix; it doesn't.

- **Done:** the homepage custom title.
- **To do (CMS):**
  1. **Settings → General → Site Title:** `FDRY`. This fixes every title suffix, `og:site_name` and the logo `alt` at once. Side effect: emails that use the site title (WordPress notifications, Contact Form 7's `[_site_title]`) will show "FDRY" in the subject.
  2. **Yoast → Settings → Site basics:** Website name `FDRY`, alternate name `Foundry Digital`, title separator `|` (to match the homepage).
  3. **Yoast → Settings → Content types (Pages, Posts, Case studies) and Categories:** confirm the SEO title template is `%%title%% %%page%% %%sep%% %%sitename%%` and has no typed slogan.
  4. **Custom SEO titles for pages that read badly with the new suffix:**
     - About: "About FDRY | FDRY" → e.g. "About Us | FDRY".
     - Insights: "INSIGHTS | FDRY" → "Insights | FDRY".
     - The post "Boost Your Sales for Free: Discover FDRY's Powerful Lead Generation Tool": its custom title adds `%%sitename%%` with no separator.
  5. **Optional:** about 20 of 86 posts are still over 60 characters with "| FDRY". The longest is 101 ("Black Friday 2024 meets Green Friday…"). Shorten their SEO titles (not the H1) where it matters, starting with posts that get traffic.
- **Then:** write custom titles of 60 characters or fewer for the top service pages, e.g. "Shopify Agency London | FDRY".

## P1: High

### #6 Nav headings
In `components/navigation/mobile.php:74`, change `<h2 class="ac-header …">` to `<div class="ac-header …">`, matching the footer accordion. Check that `navAccordion.js` and accordion-js select by class, not by tag.

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
- In `work-row.php`, change `loading="eager"` to `loading="lazy"`. The row is now "contained" on the homepage, but still sits below the fold.
- Test that the sticky parallax still lines up on cold loads. Per FRONTEND.md, the ScrollTrigger refresh on body-height change should cover late images.

### #10 / #11 CMS
- Add a meta description to Contact, and check every new-template page for missing ones.
- If Google Ads campaigns land on fdry.com, turn off Yoast → Advanced → Crawl optimisation → "Prevent Google AdsBot from crawling".

## P2: Nice to have

### #12 Organization schema
- The footer's `$contact` array is still local to `components/footer/site-footer.php:22-27`. Move it into `fdry_contact_details()` in `function-dev.php`, so both the footer and the schema use one source.
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
- **Category landing pages:** rebuild `archive-works-category.php`. It is already routed by `functions.php:178-199`, and still links "Featured" to page 50.
  1. Move the legacy body to `components/page/legacy-works-category.php` for rollback.
  2. The new body is `get_header('new')`, then `<main>` containing `work-archive` with `category => get_query_var('category_name')`.
  3. The H1 is "{Category name} work".
  4. In `function-seo.php`, use `wpseo_title`, `wpseo_metadesc` and `wpseo_canonical` to give each page a unique title, a description taken from the term description, and a self-canonical.

### #15 Archives (CMS)
In Yoast, turn off "Show in search results" for Tags and Categories.

### #16 Responsive work images
In `work-row.php`, `work-parallax.php` and `fdry_render_work_card()`, output images with `wp_get_attachment_image()` plus `sizes`, as `insight-archive.php` and `components/single/insight.php` already do. Parallax card 1 stays eager.

### #17 First-section fade
Run Lighthouse on About, Services, Contact and Work. If the LCP element is the fading tagline or title, remove the delay or the fade itself for `is_first` sections. The work archive intro now fades too, with delays of 0–0.6 s.

### #18 WP Rocket (CMS)
Turn on Remove Unused CSS and Delay JS for third-party tags. Test the legacy templates afterwards, because Remove Unused CSS can drop rules they need.

### #19 Content notes for editors
- Write descriptive eyebrows, e.g. "SERVICES" → "Ecommerce, web & growth services".
- Remove the pipes from the homepage H1 (still "FDRY Ecommerce Web Design | Woocommerce, Adobe Commerce, Wordpress & Shopify Agency").
- De-duplicate the two homepage "LONDON ECOMMERCE WEB DESIGN AGENCY" H2s.
- Expand `/services/`, which has only an H1 and one h4.

### #20 Single Insight: date, author, related links — new
In `components/single/insight.php`:
- **Date:** show the published date, and the updated date when it differs, in a `<time datetime>` element. This matches Yoast's Article schema, which already outputs `datePublished`, `dateModified` and the author.
- **Author:** show a byline, linking to the author only if author archives stay indexable.
- **Related links:** before the "Our Work" banner, add two or three related insights (same category, via `get_posts()`) and, where the category maps to one, a link to the matching service page.
- **Question for the meeting:** several posts date from 2019. Do we show "Published", "Updated", or both?

## P3: Optional

- **#21 H1 fallbacks:**
  - In `template-one-column.php`, print `<h1 class="one-column__title">` with the page title when `post_content` has no `<h1`.
  - In `insight-archive.php` and `template-contact.php`, fall back to `get_the_title()` when the tagline is empty.
- **#22 CollectionPage:** hook `wpseo_schema_webpage_type` and return `CollectionPage` for `template-work.php` and `template-insight.php`.
- **#23 Housekeeping:**
  - Drop `jquery` from the `fdry-scripts` dependencies in `fdry_enqueue_assets()` (`library/function-dev.php`). `src/` does not use jQuery.
  - In `header-new.php`, remove the `#loading-animation` inline script (the element only exists in `legacy-home.php`) and `<link rel="pingback">`.
  - In `components/footer/site-footer.php:94`, replace `© 2025 FDRY` with `wp_date('Y')`.
- **#24 Load more as a link:** make "Load more" an `<a href="…/page/2/">` that JS intercepts, and have `work-archive.php` honour `get_query_var('paged')`.
- **#25 Tags into GTM:** move the Google Ads tag, Hotjar, Apollo and Meta Pixel from `header-new.php` into GTM, triggered by CookieYes consent.
- **#26 Yoast extras:** install "ACF Content Analysis for Yoast SEO", and turn on Yoast's llms.txt feature.
- **#27 Single Insight images (new):**
  - Hero fallback: the `<img>` used when `hero_image` is a URL outside the media library has no width or height. Read the dimensions with `getimagesize()` on the local path, or skip the fallback.
  - "Our Work" banner: it uses two hard-coded absolute `https://www.fdry.com/…png` URLs with no dimensions. Move them to theme images or an ACF option, add width/height, and export them as WebP.
- **#28 Cloudflare email link (new):** Cloudflare's Email Address Obfuscation rewrites `mailto:` links into `/cdn-cgi/l/email-protection#…`, which crawlers see as 404s. It's harmless to rankings. Ignore it unless it clutters Search Console; if it does, turn it off in Cloudflare → Scrape Shield, since the address is public anyway.

---

## New file
`library/function-seo.php` holds all the Yoast hooks (#7, #12, #13, #14, #22).
- Require it from `functions.php` next to `function-work.php`.
- Guard every hook with `defined('WPSEO_VERSION')`.

## Documentation
Add an "SEO" section to `FRONTEND.md` covering:
- the one-H1 rule and the `tagline_tag` args;
- the `function-seo.php` hooks;
- the og:image fallback order;
- the 404 page and its rollback;
- the work category URLs;
- the nav video lazy-load;
- `fdry_template_page_url()` as the way to link to template pages, never hard-coded IDs.

Also update the "Homepage" preload note.

## Suggested order
1. **P0:**
   - #2 first, because it's CMS-only and fast. Fix the footer menu item, then add the 301s before unpublishing.
   - Then #1, with the redirect map from Search Console done first, alongside #3, #4 and #5.
2. **P1:** #6–#11.
3. **P2:**
   - #12, #13, #16 and #20, which are small code changes.
   - Then #14 as its own piece of work.
   - #15, #17 and #18 after measuring.
4. **P3:** as time allows.

## Verification
Measure a baseline before starting, with pagespeed.web.dev or Lighthouse in Chrome on Home, About, Services / Shopify, Work and one Insight.
1. Run `php -l` on every changed PHP file, then `pnpm build`. Deploy `dist/` and `.vite/manifest.json`.
2. `curl -sI https://www.fdry.com/does-not-exist/` returns `404`, and the page uses the new layout.
3. **Duplicate pages:** the four URLs from #2 each return a single 301 to their target, and `page-sitemap.xml` contains no `__trashed` or `-new` URLs.
4. **Redirects:** `/works/` is a single 301 to `/work/`. Every footer link still returns 200; loop over the footer `href`s with `curl -w '%{http_code}'`.
5. **Headings:** fetch Home, About, Services, a service child, a service inner, Sectors, Work, Insights, an Insight and Contact. Count the H1s with a script: exactly one per page, with no `<h2>` before it.
6. **Share images:** every new-template page has an `og:image`.
7. **Schema:** run the Rich Results Test or validator.schema.org on Home and a service page. The Organization node has an address and phone, and the Service node is linked from the WebPage.
8. **Work categories:** `/work/category/design/` returns 200 with the new layout, an H1, a unique title and a self-canonical. Clicking a filter on `/work/` updates the URL.
9. **Network:** in DevTools with a cold cache, no nav `.webm` loads until the menu opens. On Home, only the hero poster has `fetchpriority=high`.
10. **Lighthouse:** compare mobile LCP, TBT and page weight against the baseline.
11. **Search Console:** resubmit the sitemap, then watch "Not found", "Page with redirect", "Duplicate without user-selected canonical" and Core Web Vitals for 2–4 weeks.
