<?php

/**
 * Theme 301 redirects.
 *
 * Every redirect the theme sends lives in fdry_redirect_moved_urls(). Use it
 * for URL patterns that move with the code (a CPT slug, a page template).
 * One-off page moves belong in the 301 Redirects plugin.
 *
 * @package Foundry
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Send moved URLs to their new address with a single 301.
 *
 * Priority 1 runs before redirect_canonical's 404 guess, so there is only
 * one hop. To add a rule, add a branch that sets $url, with a comment saying
 * what moved. Rules for URLs that no longer resolve go under is_404().
 */
function fdry_redirect_moved_urls(): void
{
	$path = trim((string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH), '/');
	$url  = '';

	if (is_404()) {
		if (preg_match('#^work/category/([^/]+)$#', $path, $matches)) {
			// Work: /work/category/{slug}/ moved to /works/category/{slug}/.
			$url = home_url('/works/category/' . $matches[1] . '/');
		} elseif ($path === 'work' || $path === 'works/feed') {
			// Work: the page moved from /work/ to /works/, and works_post has
			// no archive (see functions.php), so its old feed is gone too.
			$url = fdry_template_page_url('template-work.php', '/works/');
		} elseif (preg_match('#^job(?:/([^/]+))?$#', $path, $matches)) {
			// Jobs: /job/{slug}/ moved to /careers/{slug}/. /job/ and unknown
			// or unpublished jobs go to the Careers page.
			$job = ! empty($matches[1]) ? get_page_by_path($matches[1], OBJECT, 'job') : null;
			$url = ($job && $job->post_status === 'publish') ? get_permalink($job) : home_url('/careers/');
		}
	} elseif (get_query_var('post_type') === 'works_post' && ! is_singular() && get_query_var('category_name') === '') {
		// Work: ?post_type=works_post lists every case study, a duplicate of
		// the Works page. Requests with a category_name are
		// /works/category/{slug}/ and must keep rendering.
		$url = fdry_template_page_url('template-work.php', '/works/');
	}

	if ($url === '') {
		return;
	}

	wp_safe_redirect($url, 301, 'FDRY theme');
	exit;
}
add_action('template_redirect', 'fdry_redirect_moved_urls', 1);
