<?php

/**
 * WebP copies of uploads.
 *
 * Every JPG/PNG upload gets a WebP copy saved next to it as {file}.{ext}.webp
 * (calling-by-fdry.png → calling-by-fdry.png.webp), made with WordPress's own
 * image editor (Imagick or GD). On the front end, upload URLs are swapped for
 * their copy wherever one exists. The originals are never changed.
 *
 * - New uploads, regenerated sizes and media-library edits are converted as
 *   their metadata is saved (fdry_webp_on_metadata_update()).
 * - The existing library is converted with `wp fdry webp` (fdry_webp_cli()).
 * - Deleting an upload deletes its copy (fdry_webp_delete_copy()).
 *
 * WebP only, with no <picture> fallback: every browser since Safari 14 (2020)
 * reads WebP, so the markup stays the same and Cloudflare caches one file per
 * URL. See FRONTEND.md → Images (WebP).
 *
 * @package Foundry
 */

if (! defined('ABSPATH')) {
	exit;
}

/*
 * Master switch. Define FDRY_WEBP as false in wp-config.php to stop both the
 * conversion of new uploads and the URL swap, then clear the WP Rocket cache.
 * The copies stay on disk until `wp fdry webp --delete` removes them.
 */
if (! defined('FDRY_WEBP')) {
	define('FDRY_WEBP', true);
}

const FDRY_WEBP_QUALITY  = 80;
const FDRY_WEBP_META_KEY = '_fdry_webp';
const FDRY_WEBP_VERSION  = '1';

/**
 * Whether a path or URL is a JPG or PNG, the formats that get a WebP copy.
 */
function fdry_webp_is_source(string $path): bool
{
	return (bool) preg_match('/\.(?:jpe?g|png)$/i', $path);
}

/**
 * Path or URL of a file's WebP copy.
 */
function fdry_webp_path(string $path): string
{
	return $path . '.webp';
}

/**
 * Path a copy is written to before it's renamed into place.
 *
 * It ends in .webp because the image editor picks the format from the
 * extension. A run cut off mid-write can leave one behind. It is never served,
 * and the next conversion of that file overwrites it.
 */
function fdry_webp_temp_path(string $path): string
{
	return $path . '.tmp.webp';
}

/**
 * Whether an attachment is a JPG or PNG image.
 */
function fdry_webp_is_source_attachment(int $attachment_id): bool
{
	return in_array(get_post_mime_type($attachment_id), array('image/jpeg', 'image/png'), true);
}

/**
 * Whether the server's image editor can write WebP.
 *
 * Checked before every conversion: an editor without WebP support falls back
 * to its default format and would save a JPG instead.
 */
function fdry_webp_supported(): bool
{
	static $supported = null;

	if ($supported === null) {
		$supported = wp_image_editor_supports(array('mime_type' => 'image/webp'));
	}

	return $supported;
}

/**
 * WebP quality while fdry_webp_convert_file() saves a copy.
 *
 * @param int    $quality   Quality from core or earlier filters.
 * @param string $mime_type Format being written.
 */
function fdry_webp_editor_quality($quality, $mime_type)
{
	return $mime_type === 'image/webp' ? FDRY_WEBP_QUALITY : $quality;
}

/**
 * Save the WebP copy of one JPG or PNG.
 *
 * The copy is kept only if it is smaller than the source, so a file that
 * doesn't compress better keeps being served as it is.
 *
 * @param string $file      Absolute path of the source.
 * @param bool   $overwrite Make the copy again even if it exists.
 * @return array{status: string, before: int, after: int, message: string}
 *         Status is converted, exists, no-gain, skipped (not a JPG/PNG, or
 *         missing on disk) or error.
 */
function fdry_webp_convert_file(string $file, bool $overwrite = false): array
{
	static $tried = array();

	$result = array(
		'status'  => 'skipped',
		'before'  => 0,
		'after'   => 0,
		'message' => '',
	);

	if (! fdry_webp_is_source($file) || ! is_file($file)) {
		return $result;
	}

	$webp = fdry_webp_path($file);

	if (! $overwrite && is_file($webp)) {
		$result['status'] = 'exists';

		return $result;
	}

	// Core saves an upload's metadata once per sub-size, so the same file
	// comes through several times in one request. Don't retry a file that
	// failed or gained nothing.
	if (! $overwrite && isset($tried[$file])) {
		return $tried[$file];
	}

	$result['before'] = (int) filesize($file);
	$result['status'] = 'error';

	if (! fdry_webp_supported()) {
		$result['message'] = __('The image editor cannot write WebP.', 'foundry');

		return $tried[$file] = $result;
	}

	// The editor resets the quality to the WebP default (86) when it switches
	// format at save time, so set_quality() alone doesn't hold. The filter does.
	add_filter('wp_editor_set_quality', 'fdry_webp_editor_quality', 99, 2);

	// Write under a temporary name and rename once complete. A run that gets
	// cut off (a timeout, a dropped connection) then never leaves a
	// half-written copy that the front end would serve.
	$temp = fdry_webp_temp_path($file);

	try {
		$editor = wp_get_image_editor($file, array('output_mime_type' => 'image/webp'));
		$saved  = is_wp_error($editor) ? $editor : $editor->save($temp, 'image/webp');
	} finally {
		remove_filter('wp_editor_set_quality', 'fdry_webp_editor_quality', 99);
	}

	if (is_wp_error($saved)) {
		$result['message'] = $saved->get_error_message();

		return $tried[$file] = $result;
	}

	// The editor builds the file name from the format it actually wrote. Any
	// other path means it fell back to another format, so remove that file.
	$saved_path = is_array($saved) && isset($saved['path']) ? (string) $saved['path'] : '';

	if (wp_normalize_path($saved_path) !== wp_normalize_path($temp)) {
		if ($saved_path !== '' && $saved_path !== $file && is_file($saved_path)) {
			wp_delete_file($saved_path);
		}

		$result['message'] = __('The image editor did not write a WebP file.', 'foundry');

		return $tried[$file] = $result;
	}

	clearstatcache(true, $temp);
	$size = (int) filesize($temp);

	if ($size <= 0 || $size >= $result['before']) {
		wp_delete_file($temp);
		$result['status'] = 'no-gain';

		return $tried[$file] = $result;
	}

	if (! rename($temp, $webp)) {
		wp_delete_file($temp);
		$result['message'] = __('Could not move the WebP file into place.', 'foundry');

		return $tried[$file] = $result;
	}

	$result['status'] = 'converted';
	$result['after']  = $size;

	return $tried[$file] = $result;
}

/**
 * Absolute paths of the files an image attachment serves.
 *
 * That is the attached file (the -scaled copy for big uploads), every
 * sub-size, and the files an edit replaced, since older content can still
 * point at them. The pre-scaled original is never served, so it's left out.
 *
 * @param int                       $attachment_id Attachment ID.
 * @param array<string, mixed>|null $meta          Attachment metadata. Default: read it.
 * @return string[]
 */
function fdry_webp_attachment_files(int $attachment_id, ?array $meta = null): array
{
	if ($meta === null) {
		$meta = wp_get_attachment_metadata($attachment_id);
		$meta = is_array($meta) ? $meta : array();
	}

	$main = ! empty($meta['file']) && is_string($meta['file'])
		? path_join(wp_get_upload_dir()['basedir'], $meta['file'])
		: (string) get_attached_file($attachment_id, true);

	if ($main === '') {
		return array();
	}

	$dir    = dirname($main);
	$files  = array($main);
	$backup = get_post_meta($attachment_id, '_wp_attachment_backup_sizes', true);
	$sizes  = array_merge(
		is_array($meta['sizes'] ?? null) ? array_values($meta['sizes']) : array(),
		is_array($backup) ? array_values($backup) : array()
	);

	foreach ($sizes as $size) {
		if (is_array($size) && ! empty($size['file']) && is_string($size['file'])) {
			$files[] = path_join($dir, wp_basename($size['file']));
		}
	}

	return array_values(array_unique($files));
}

/**
 * Make the WebP copies of one attachment's files.
 *
 * Marks the attachment as done (FDRY_WEBP_META_KEY) when no file failed, so
 * `wp fdry webp` skips it next time.
 *
 * @param int                       $attachment_id Attachment ID.
 * @param array<string, mixed>|null $meta          Attachment metadata. Default: read it.
 * @param bool                      $overwrite     Make existing copies again.
 * @return array{converted: int, exists: int, no-gain: int, skipped: int, error: int, before: int, after: int, messages: string[]}
 */
function fdry_webp_convert_attachment(int $attachment_id, ?array $meta = null, bool $overwrite = false): array
{
	$totals = array(
		'converted' => 0,
		'exists'    => 0,
		'no-gain'   => 0,
		'skipped'   => 0,
		'error'     => 0,
		'before'    => 0,
		'after'     => 0,
		'messages'  => array(),
	);

	foreach (fdry_webp_attachment_files($attachment_id, $meta) as $file) {
		$result = fdry_webp_convert_file($file, $overwrite);

		++$totals[$result['status']];

		if ($result['status'] === 'converted') {
			$totals['before'] += $result['before'];
			$totals['after']  += $result['after'];
		} elseif ($result['status'] === 'error') {
			$totals['messages'][] = wp_basename($file) . ': ' . $result['message'];
		}
	}

	if ($totals['error'] === 0) {
		update_post_meta($attachment_id, FDRY_WEBP_META_KEY, FDRY_WEBP_VERSION);
	}

	return $totals;
}

/**
 * Convert an image's files whenever its metadata is saved.
 *
 * Core saves the metadata after each sub-size it makes on upload (and when it
 * resumes an upload that failed), after Regenerate Thumbnails and after an
 * edit in the media library, so this one hook covers all of them. Files that
 * already have a copy are skipped. A failure never reaches the upload.
 *
 * @param array|false $data          Attachment metadata.
 * @param int         $attachment_id Attachment ID.
 * @return array|false Unchanged.
 */
function fdry_webp_on_metadata_update($data, $attachment_id)
{
	if (! FDRY_WEBP || ! is_array($data) || ! fdry_webp_is_source_attachment((int) $attachment_id)) {
		return $data;
	}

	try {
		fdry_webp_convert_attachment((int) $attachment_id, $data);
	} catch (Throwable $e) {
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('fdry webp: attachment ' . (int) $attachment_id . ': ' . $e->getMessage()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	return $data;
}
add_filter('wp_update_attachment_metadata', 'fdry_webp_on_metadata_update', 20, 2);

/**
 * Delete a file's WebP copy along with it.
 *
 * Every upload and sub-size WordPress deletes goes through wp_delete_file(),
 * which covers deleting an attachment, regenerating sizes and restoring an
 * edited image.
 *
 * @param string $file Path about to be deleted.
 * @return string Unchanged.
 */
function fdry_webp_delete_copy($file)
{
	if (is_string($file) && fdry_webp_is_source($file)) {
		foreach (array(fdry_webp_path($file), fdry_webp_temp_path($file)) as $copy) {
			if (is_file($copy)) {
				wp_delete_file($copy);
			}
		}
	}

	return $file;
}
add_filter('wp_delete_file', 'fdry_webp_delete_copy');

/**
 * Front-end AJAX actions whose HTML gets WebP URLs.
 *
 * Every other admin-ajax request comes from wp-admin (the media modal,
 * "Insert into post"), where a swapped URL could end up saved in content.
 *
 * @return string[]
 */
function fdry_webp_ajax_actions(): array
{
	return array('fdry_load_more_works', 'load_more_posts');
}

/**
 * Whether URLs are swapped for WebP copies in this request: the front end and
 * its AJAX only. Never wp-admin, the block editor (REST), feeds or WP-CLI.
 */
function fdry_webp_active(): bool
{
	if (! FDRY_WEBP || (defined('WP_CLI') && WP_CLI)) {
		return false;
	}

	if (wp_doing_ajax()) {
		$action = isset($_REQUEST['action']) ? sanitize_key(wp_unslash($_REQUEST['action'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return in_array($action, fdry_webp_ajax_actions(), true);
	}

	if (is_admin()) {
		return false;
	}

	$is_rest = function_exists('wp_is_serving_rest_request')
		? wp_is_serving_rest_request()
		: defined('REST_REQUEST') && REST_REQUEST;

	return ! $is_rest && ! is_feed();
}

/**
 * The URL of an upload's WebP copy, when the copy exists.
 *
 * Anything that isn't a JPG/PNG in the uploads folder, or has no copy on
 * disk, comes back unchanged. WP Rocket's page cache means the file check
 * only runs on cache misses.
 */
function fdry_webp_url(string $url): string
{
	static $cache = array();
	static $base  = null;
	static $dir   = null;

	if ($url === '' || ! fdry_webp_active()) {
		return $url;
	}

	if (isset($cache[$url])) {
		return $cache[$url];
	}

	$cache[$url] = $url;

	if ($base === null) {
		$uploads = wp_get_upload_dir();
		$base    = empty($uploads['error']) ? trailingslashit((string) preg_replace('#^https?:#i', '', $uploads['baseurl'])) : '';
		$dir     = trailingslashit($uploads['basedir']);
	}

	// Compare without the scheme: srcset URLs can be http while the upload
	// base URL is https, or the other way round.
	$bare = (string) preg_replace('#^https?:#i', '', $url);

	if ($base === '' || ! str_starts_with($bare, $base)) {
		return $url;
	}

	$relative = substr($bare, strlen($base));

	if (strpbrk($relative, '?#') !== false || str_contains($relative, '..') || ! fdry_webp_is_source($relative)) {
		return $url;
	}

	if (is_file(fdry_webp_path($dir . rawurldecode($relative)))) {
		$cache[$url] = fdry_webp_path($url);
	}

	return $cache[$url];
}

/**
 * Swap the URL in wp_get_attachment_image_src() results.
 *
 * Covers the work row, work cards and parallax (which print one `large`
 * URL), and the src of wp_get_attachment_image().
 *
 * @param array|false $image Array of URL, width, height and is-intermediate.
 * @return array|false
 */
function fdry_webp_attachment_image_src($image)
{
	if (is_array($image) && isset($image[0]) && is_string($image[0])) {
		$image[0] = fdry_webp_url($image[0]);
	}

	return $image;
}
add_filter('wp_get_attachment_image_src', 'fdry_webp_attachment_image_src', 20);

/**
 * Swap every srcset candidate, including the srcsets of Gutenberg images.
 *
 * Core matches the src against the sizes with str_contains(), so a src that
 * already ends in .webp still matches.
 *
 * @param array|false $sources Sources keyed by width.
 * @return array|false
 */
function fdry_webp_srcset($sources)
{
	if (! is_array($sources)) {
		return $sources;
	}

	foreach ($sources as $width => $source) {
		if (is_array($source) && isset($source['url']) && is_string($source['url'])) {
			$sources[$width]['url'] = fdry_webp_url($source['url']);
		}
	}

	return $sources;
}
add_filter('wp_calculate_image_srcset', 'fdry_webp_srcset', 20);

/**
 * Swap the URLs in an ACF image value (array or URL return format).
 *
 * Runs after ACF's own formatting (priority 10). Covers fdry_image_parts()
 * and every component that reads `url` from an ACF image, including the hero
 * posters, so their preload and <img> keep the same URL.
 *
 * @param mixed $value Formatted field value.
 * @return mixed
 */
function fdry_webp_acf_image($value)
{
	if (is_string($value)) {
		return fdry_webp_url($value);
	}

	if (! is_array($value)) {
		return $value;
	}

	if (isset($value['url']) && is_string($value['url'])) {
		$value['url'] = fdry_webp_url($value['url']);
	}

	if (isset($value['sizes']) && is_array($value['sizes'])) {
		foreach ($value['sizes'] as $key => $size) {
			if (is_string($size)) {
				$value['sizes'][$key] = fdry_webp_url($size);
			}
		}
	}

	return $value;
}
add_filter('acf/format_value/type=image', 'fdry_webp_acf_image', 20);

/**
 * Swap the URLs in an ACF gallery value, image by image.
 *
 * @param mixed $value Formatted field value.
 * @return mixed
 */
function fdry_webp_acf_gallery($value)
{
	return is_array($value) ? array_map('fdry_webp_acf_image', $value) : $value;
}
add_filter('acf/format_value/type=gallery', 'fdry_webp_acf_gallery', 20);

/**
 * Swap the src of Gutenberg images. Their srcset is already swapped by
 * fdry_webp_srcset().
 *
 * @param string $image The <img> tag.
 * @return string
 */
function fdry_webp_content_img_tag($image)
{
	if (! is_string($image) || ! fdry_webp_active() || ! class_exists('WP_HTML_Tag_Processor')) {
		return $image;
	}

	$tags = new WP_HTML_Tag_Processor($image);

	if (! $tags->next_tag('img')) {
		return $image;
	}

	$src = $tags->get_attribute('src');

	if (! is_string($src)) {
		return $image;
	}

	$webp = fdry_webp_url($src);

	if ($webp === $src) {
		return $image;
	}

	$tags->set_attribute('src', $webp);

	return $tags->get_updated_html();
}
add_filter('wp_content_img_tag', 'fdry_webp_content_img_tag', 20);

/**
 * Keep the URLs that other software reads on the JPG/PNG.
 *
 * - **Site icons.** The apple-touch-icon (iPhone home screen), the Windows
 *   tile and Google's search favicon expect PNG, and the files are a few KB.
 * - **Yoast's social previews.** A safety net: Yoast mostly stores og images
 *   when a post is saved in wp-admin, where nothing is swapped.
 *
 * @param mixed $url Image URL.
 * @return mixed
 */
function fdry_webp_original_url($url)
{
	return is_string($url) ? (string) preg_replace('/(\.(?:jpe?g|png))\.webp$/i', '$1', $url) : $url;
}
add_filter('get_site_icon_url', 'fdry_webp_original_url', 20);
add_filter('wpseo_opengraph_image', 'fdry_webp_original_url', 20);
add_filter('wpseo_twitter_image', 'fdry_webp_original_url', 20);

/**
 * SQL condition for the JPG/PNG attachments the CLI goes through.
 *
 * @param bool $skip_done Leave out attachments already marked as done.
 * @param int  $after     Only attachments with a higher ID.
 */
function fdry_webp_cli_where(bool $skip_done, int $after): string
{
	global $wpdb;

	$where = $wpdb->prepare(
		"p.post_type = 'attachment' AND p.post_mime_type IN ('image/jpeg', 'image/png') AND p.ID > %d",
		$after
	);

	if ($skip_done) {
		$where .= $wpdb->prepare(
			" AND NOT EXISTS (SELECT 1 FROM {$wpdb->postmeta} m WHERE m.post_id = p.ID AND m.meta_key = %s AND m.meta_value = %s)",
			FDRY_WEBP_META_KEY,
			FDRY_WEBP_VERSION
		);
	}

	return $where;
}

/**
 * Count the JPG/PNG attachments the CLI will go through.
 *
 * @param bool $skip_done Leave out attachments already marked as done.
 * @param int  $after     Only attachments with a higher ID.
 */
function fdry_webp_cli_count(bool $skip_done, int $after = 0): int
{
	global $wpdb;

	$where = fdry_webp_cli_where($skip_done, $after);

	return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} p WHERE {$where}"); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- prepared in fdry_webp_cli_where().
}

/**
 * The next batch of JPG/PNG attachment IDs after $after, in ID order.
 *
 * Walking by ID keeps the batches stable while attachments get marked done,
 * and moves past an attachment that keeps failing instead of retrying it.
 *
 * @param int  $after     Last ID handled.
 * @param int  $limit     Batch size.
 * @param bool $skip_done Leave out attachments already marked as done.
 * @return int[]
 */
function fdry_webp_cli_next_ids(int $after, int $limit, bool $skip_done): array
{
	global $wpdb;

	$where = fdry_webp_cli_where($skip_done, $after);
	$ids   = $wpdb->get_col($wpdb->prepare("SELECT p.ID FROM {$wpdb->posts} p WHERE {$where} ORDER BY p.ID ASC LIMIT %d", $limit)); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- prepared in fdry_webp_cli_where().

	return array_map('intval', $ids);
}

/**
 * `wp fdry webp`: make the WebP copies for the existing library.
 *
 * A plain run converts every JPG/PNG attachment not yet marked as done, so it
 * can be stopped and started again. With --limit it stops after that many
 * attachments and prints the command for the next chunk, for consoles that
 * time out (Plesk's WP-CLI panel).
 *
 * @param string[]              $args       Positional arguments (none).
 * @param array<string, string> $assoc_args Options, see the synopsis below.
 */
function fdry_webp_cli(array $args, array $assoc_args): void
{
	$batch   = max(1, (int) ($assoc_args['batch'] ?? 200));
	$limit   = max(0, (int) ($assoc_args['limit'] ?? 0));
	$after   = max(0, (int) ($assoc_args['after'] ?? 0));
	$force   = (bool) WP_CLI\Utils\get_flag_value($assoc_args, 'force', false);
	$dry_run = (bool) WP_CLI\Utils\get_flag_value($assoc_args, 'dry-run', false);
	$delete  = (bool) WP_CLI\Utils\get_flag_value($assoc_args, 'delete', false);
	$ids     = array_values(array_filter(array_map('absint', explode(',', (string) ($assoc_args['ids'] ?? '')))));
	$by_ids  = $ids !== array();

	if (! $delete && ! $dry_run && ! fdry_webp_supported()) {
		WP_CLI::error("The server's image editor (Imagick or GD) can't write WebP. Check Tools → Site Health → Info → Media handling.");
	}

	// PHP on the command line normally has no time limit, but Plesk's php.ini
	// sets one (300 s), which stopped the first full run part-way.
	if (function_exists('set_time_limit')) {
		set_time_limit(0);
	}

	// --ids, --force and --delete go through every attachment they cover. A
	// plain run (and a dry run) skips the ones already done.
	$skip_done = ! $by_ids && ! $force && ! $delete;
	$total     = $by_ids ? count($ids) : fdry_webp_cli_count($skip_done, $after);

	if ($total === 0) {
		WP_CLI::success('Nothing to do: every JPG/PNG attachment already has its WebP copies.');

		return;
	}

	$label    = $delete ? 'Deleting WebP copies' : ($dry_run ? 'Checking' : 'Converting');
	$progress = WP_CLI\Utils\make_progress_bar($label, $limit > 0 ? min($total, $limit) : $total);
	$totals   = array(
		'attachments' => 0,
		'converted'   => 0,
		'exists'      => 0,
		'no-gain'     => 0,
		'error'       => 0,
		'before'      => 0,
		'after'       => 0,
		'messages'    => array(),
	);
	$cursor   = $after;
	$handled  = 0;
	$stopped  = false;

	while (! $stopped) {
		$batch_ids = $by_ids ? array_splice($ids, 0, $batch) : fdry_webp_cli_next_ids($cursor, $batch, $skip_done);

		if ($batch_ids === array()) {
			break;
		}

		foreach ($batch_ids as $id) {
			if ($limit > 0 && $handled >= $limit) {
				$stopped = true;
				break;
			}

			++$handled;
			$cursor = max($cursor, $id);
			$progress->tick();

			if (! fdry_webp_is_source_attachment($id)) {
				$totals['messages'][] = sprintf('#%d is not a JPG or PNG attachment.', $id);
				continue;
			}

			++$totals['attachments'];
			$files = fdry_webp_attachment_files($id);

			if ($delete) {
				foreach ($files as $file) {
					if (! fdry_webp_is_source($file)) {
						continue;
					}

					foreach (array(fdry_webp_path($file), fdry_webp_temp_path($file)) as $copy) {
						if (is_file($copy)) {
							$totals['before'] += (int) filesize($copy);
							++$totals['converted'];
							wp_delete_file($copy);
						}
					}
				}

				delete_post_meta($id, FDRY_WEBP_META_KEY);
				continue;
			}

			if ($dry_run) {
				foreach ($files as $file) {
					if (! fdry_webp_is_source($file) || ! is_file($file)) {
						continue;
					}

					if (! $force && is_file(fdry_webp_path($file))) {
						++$totals['exists'];
					} else {
						++$totals['converted'];
						$totals['before'] += (int) filesize($file);
					}
				}

				continue;
			}

			$result = fdry_webp_convert_attachment($id, null, $force);

			foreach (array('converted', 'exists', 'no-gain', 'error', 'before', 'after') as $key) {
				$totals[$key] += $result[$key];
			}

			foreach ($result['messages'] as $message) {
				$totals['messages'][] = sprintf('#%d %s', $id, $message);
			}
		}

		// Keep memory flat over thousands of attachments.
		if (function_exists('WP_CLI\Utils\wp_clear_object_cache')) {
			WP_CLI\Utils\wp_clear_object_cache();
		}
	}

	$progress->finish();

	foreach ($totals['messages'] as $message) {
		WP_CLI::warning($message);
	}

	// Attachments past the cursor that this run didn't reach.
	$left = $by_ids ? count($ids) : fdry_webp_cli_count($skip_done, $cursor);
	$next = '';

	if ($left > 0) {
		$flags = array_filter(
			array(
				$by_ids ? '--ids=' . implode(',', $ids) : '',
				$limit > 0 ? '--limit=' . $limit : '',
				! $by_ids ? '--after=' . $cursor : '',
				$force ? '--force' : '',
				$dry_run ? '--dry-run' : '',
				$delete ? '--delete' : '',
			)
		);
		$next = sprintf('Attachments left: %d. Next chunk: wp fdry webp %s', $left, implode(' ', $flags));
	}

	if ($delete) {
		WP_CLI::success(sprintf('Deleted %d WebP files (%s) from %d attachments.', $totals['converted'], size_format($totals['before'], 1), $totals['attachments']));
	} elseif ($dry_run) {
		WP_CLI::success(sprintf('%d files (%s) in %d attachments would be converted. %d already have a copy.', $totals['converted'], size_format($totals['before'], 1), $totals['attachments'], $totals['exists']));
	} else {
		$saving = $totals['before'] > 0 ? round(100 * (1 - $totals['after'] / $totals['before'])) : 0;

		WP_CLI::log(sprintf('Attachments: %d', $totals['attachments']));
		WP_CLI::log(sprintf('Files converted: %d (%s → %s, -%d%%)', $totals['converted'], size_format($totals['before'], 1), size_format($totals['after'], 1), $saving));
		WP_CLI::log(sprintf('Already had a copy: %d', $totals['exists']));
		WP_CLI::log(sprintf('Kept as original (WebP not smaller): %d', $totals['no-gain']));

		if ($totals['error'] > 0) {
			WP_CLI::warning(sprintf('%d files failed (listed above). Their attachments are not marked done, so a plain `wp fdry webp` run tries them again.', $totals['error']));
		}
	}

	if ($next !== '') {
		WP_CLI::log($next);

		return;
	}

	if (! $delete && ! $dry_run) {
		WP_CLI::success('Done. Clear the WP Rocket cache so pages pick up the WebP URLs.');
	}
}

if (defined('WP_CLI') && WP_CLI) {
	WP_CLI::add_command(
		'fdry webp',
		'fdry_webp_cli',
		array(
			'shortdesc' => 'Make WebP copies of JPG and PNG uploads ({file}.{ext}.webp).',
			'synopsis'  => array(
				array(
					'type'        => 'assoc',
					'name'        => 'ids',
					'optional'    => true,
					'description' => 'Comma-separated attachment IDs. Done attachments are not skipped.',
				),
				array(
					'type'        => 'assoc',
					'name'        => 'limit',
					'optional'    => true,
					'description' => 'Stop after this many attachments and print the command for the next chunk. Use it where the console times out (Plesk WP-CLI panel).',
				),
				array(
					'type'        => 'assoc',
					'name'        => 'after',
					'optional'    => true,
					'description' => 'Start after this attachment ID. The next-chunk command sets it for you.',
				),
				array(
					'type'        => 'assoc',
					'name'        => 'batch',
					'optional'    => true,
					'default'     => 200,
					'description' => 'Attachments per database query.',
				),
				array(
					'type'        => 'flag',
					'name'        => 'force',
					'optional'    => true,
					'description' => 'Ignore the done marker and make existing copies again (e.g. after a quality change).',
				),
				array(
					'type'        => 'flag',
					'name'        => 'dry-run',
					'optional'    => true,
					'description' => 'Count the files to convert without writing anything.',
				),
				array(
					'type'        => 'flag',
					'name'        => 'delete',
					'optional'    => true,
					'description' => 'Delete every WebP copy and done marker.',
				),
			),
			'longdesc'  => "## EXAMPLES\n\n    # Fix the service-page LCP image first\n    wp fdry webp --ids=123,456\n\n    # See how much work is left\n    wp fdry webp --dry-run\n\n    # Convert the rest of the library (safe to stop and re-run)\n    wp fdry webp\n\n    # The same in chunks, for a console that times out\n    wp fdry webp --limit=100",
		)
	);
}
