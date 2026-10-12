<?php

/**
 * Theme security headers.
 *
 * Sent on every front-end response WordPress renders (pages and its own
 * redirects). Static files served directly by the host don't get them.
 *
 * @package Foundry
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Add the security headers Site Health checks for.
 *
 * A header that is already set (by a plugin, or an earlier filter) is left
 * as it is. HSTS and the HTTPS upgrade are only sent when the site's URLs are
 * HTTPS, so a local http:// copy keeps loading its assets.
 *
 * @param array<string, string> $headers Headers WordPress is about to send.
 * @return array<string, string>
 */
function fdry_security_headers(array $headers): array
{
	$security = array(
		'X-Content-Type-Options' => 'nosniff',
		// Device APIs the site never uses. fullscreen and autoplay stay
		// allowed for video and the Google Maps embeds.
		'Permissions-Policy'     => 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()',
	);

	if (wp_is_using_https()) {
		// No includeSubDomains: WordPress also answers on fdry.com, so it
		// would force HTTPS on every *.fdry.com host.
		$security['Strict-Transport-Security'] = 'max-age=31536000';
		$security['Content-Security-Policy']   = 'upgrade-insecure-requests';
	}

	return $headers + $security;
}
add_filter('wp_headers', 'fdry_security_headers');
