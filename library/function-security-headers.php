<?php

/**
 * Theme security headers.
 *
 * WP Rocket serves cached pages as static HTML, so WordPress never runs for
 * them. While WP Rocket is active the headers go into its .htaccess block
 * instead, which Apache applies to cached and uncached pages alike. Until
 * that block has been written, and whenever WP Rocket is off, WordPress
 * sends them itself.
 *
 * WP Rocket only rewrites .htaccess when its own settings change, so the
 * first wp-admin load after the rules change asks it to (see
 * fdry_security_headers_flush_htaccess()).
 *
 * @package Foundry
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * The security headers Site Health checks for.
 *
 * HSTS and the HTTPS upgrade are only included when the site's URLs are
 * HTTPS, so a local http:// copy keeps loading its assets.
 *
 * @return array<string, string> Header name => value.
 */
function fdry_security_header_values(): array
{
	$headers = array(
		'X-Content-Type-Options' => 'nosniff',
		// Device APIs the site never uses. fullscreen and autoplay stay
		// allowed for video and the Google Maps embeds.
		'Permissions-Policy'     => 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()',
	);

	if (wp_is_using_https()) {
		// No includeSubDomains: WordPress also answers on fdry.com, so it
		// would force HTTPS on every *.fdry.com host.
		$headers['Strict-Transport-Security'] = 'max-age=31536000';
		$headers['Content-Security-Policy']   = 'upgrade-insecure-requests';
	}

	return $headers;
}

/**
 * Write the headers into WP Rocket's .htaccess block.
 *
 * "always" also covers redirects and error pages.
 *
 * @param string $rules Rules other code has added before WP Rocket's own.
 * @return string
 */
function fdry_security_headers_htaccess(string $rules): string
{
	$lines = array('# Foundry theme: security headers', '<IfModule mod_headers.c>');

	foreach (fdry_security_header_values() as $name => $value) {
		$lines[] = sprintf("\tHeader always set %s \"%s\"", $name, $value);
	}

	$lines[] = '</IfModule>';

	return $rules . implode(PHP_EOL, $lines) . PHP_EOL . PHP_EOL;
}
add_filter('before_rocket_htaccess_rules', 'fdry_security_headers_htaccess');

/**
 * Whether WP Rocket has written the current rules into .htaccess.
 *
 * The option holds a hash of the rules from the last successful rewrite, so
 * editing a header makes this false until .htaccess is rewritten again.
 */
function fdry_security_headers_in_htaccess(): bool
{
	return defined('WP_ROCKET_VERSION')
		&& get_option('fdry_security_headers_htaccess') === md5(fdry_security_headers_htaccess(''));
}

/**
 * Ask WP Rocket to rewrite .htaccess when the rules have changed.
 *
 * Saving WP Rocket's settings without changing anything doesn't rewrite the
 * file. If the rewrite fails (for example .htaccess isn't writable), the
 * hash isn't stored and the next wp-admin load tries again.
 */
function fdry_security_headers_flush_htaccess(): void
{
	if (! function_exists('flush_rocket_htaccess') || fdry_security_headers_in_htaccess()) {
		return;
	}

	if (false !== flush_rocket_htaccess()) {
		update_option('fdry_security_headers_htaccess', md5(fdry_security_headers_htaccess('')));
	}
}
add_action('admin_init', 'fdry_security_headers_flush_htaccess');

/**
 * Send the headers from WordPress when .htaccess doesn't carry them.
 *
 * A header that is already set (by a plugin, or an earlier filter) is left
 * as it is.
 *
 * @param array<string, string> $headers Headers WordPress is about to send.
 * @return array<string, string>
 */
function fdry_security_headers(array $headers): array
{
	if (fdry_security_headers_in_htaccess()) {
		return $headers;
	}

	return $headers + fdry_security_header_values();
}
add_filter('wp_headers', 'fdry_security_headers');
