<?php

/**
 * The template for displaying 404 pages (not found).
 *
 * Styled in src/styles/templates/_error-page.scss. The old markup used
 * #error-404-wrapper / .overlay-404, which theme.css still styles, so the new
 * classes avoid those names.
 *
 * @package understrap
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// WordPress has already set the 404 status, and Yoast adds noindex. Missing
// URLs used to 301 to the homepage here, which Google treats as soft 404s.
// Send pages that moved to their new address with a redirect rule instead.

get_header('new');
?>

<main class="main error-page" role="main">
	<div class="content-block">
		<div class="content-max">
			<div class="error-page__inner">
				<h1 class="error-page__title" data-fade-up>Oops!</h1>
				<h2 class="error-page__subtitle" data-fade-up data-fade-up-delay="0.1">Page not found (404)</h2>
				<p class="error-page__text" data-fade-up data-fade-up-delay="0.2">Go back to our <a class="error-page__link" href="<?php echo esc_url(home_url('/')); ?>">homepage</a>.</p>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>
