<?php

/**
 * Template Name: NEW template Contact
 *
 * Template Post Type: page
 *
 * This template show Contact page
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header('new');

$post_id = (int) get_queried_object_id();

$tagline = $post_id ? (string) get_field('contact_tagline', $post_id) : '';
$title   = $post_id ? (string) get_field('contact_title', $post_id) : '';
$content = $post_id ? (string) get_field('contact_content', $post_id) : '';

$button       = $post_id ? get_field('contact_button', $post_id) : null;
$button_parts = fdry_acf_link_parts($button);
$has_card_link = $button_parts['url'] !== '#';

$thumbnail = $post_id ? fdry_image_parts(get_field('contact_thumbnail', $post_id)) : fdry_image_parts(null);

$phone = $post_id ? trim((string) get_field('contact_phone', $post_id)) : '';
$email = $post_id ? trim((string) get_field('contact_email', $post_id)) : '';

$address_eyebrow = $post_id ? trim((string) get_field('contact_address_eyebrow', $post_id)) : '';
$address_main    = $post_id ? trim((string) get_field('contact_address_main', $post_id)) : '';
$address_url     = $post_id ? trim((string) get_field('contact_address_url', $post_id)) : '';

$phone_href = $phone !== '' ? 'tel:' . preg_replace('/[^\d+]/', '', $phone) : '';

$has_address = ($address_eyebrow !== '' || $address_main !== '') && $address_url !== '';
?>

<main class="main contact-main" role="main">
	<div class="contact-main__col contact-main__col--left">
		<?php if ($tagline !== '') : ?>
			<h1 class="contact-main__tagline"><?= esc_html($tagline); ?></h1>
		<?php endif; ?>

		<?php if ($title !== '') : ?>
			<p class="contact-main__title"><?= esc_html($title); ?></p>
		<?php endif; ?>

		<?php if ($content !== '') : ?>
			<div class="contact-main__body wysiwyg">
				<?= wp_kses_post($content); ?>
			</div>
		<?php endif; ?>

		<?php if ($has_card_link) : ?>
			<a
				class="contact-main__card"
				href="<?= esc_url($button_parts['url']); ?>"
				<?php if ($button_parts['target'] !== '') : ?>
					target="<?= esc_attr($button_parts['target']); ?>"
					<?php if ($button_parts['target'] === '_blank') : ?>
						rel="noopener noreferrer"
					<?php endif; ?>
				<?php endif; ?>
			>
		<?php else : ?>
			<div class="contact-main__card">
		<?php endif; ?>
				<p class="contact-main__card-tagline"><?= esc_html__('LETS TALK', 'foundry'); ?></p>
				<p class="contact-main__card-title"><?= esc_html__('Send your brief', 'foundry'); ?></p>
				<p class="contact-main__card-text"><?= esc_html__('and calculate your budget and timescale', 'foundry'); ?></p>
				<span class="contact-main__card-action">
					<?php if ($thumbnail['url'] !== '') : ?>
						<img
							class="contact-main__card-thumb"
							src="<?= esc_url($thumbnail['url']); ?>"
							alt=""
							<?php if ($thumbnail['width'] > 0) : ?>
								width="<?= (int) $thumbnail['width']; ?>"
							<?php endif; ?>
							<?php if ($thumbnail['height'] > 0) : ?>
								height="<?= (int) $thumbnail['height']; ?>"
							<?php endif; ?>
							loading="lazy"
							decoding="async"
						>
					<?php endif; ?>
					<span class="contact-main__card-label"><?= esc_html__('SCHEDULE A MEETING', 'foundry'); ?></span>
					<span class="contact-main__card-arrow" aria-hidden="true">
						<?php get_template_part('svg-template/svg-arrow'); ?>
					</span>
				</span>
		<?php if ($has_card_link) : ?>
			</a>
		<?php else : ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="contact-main__col contact-main__col--right">
		<?php if ($phone !== '' && $phone_href !== 'tel:') : ?>
			<a class="contact-main__row" href="<?= esc_attr($phone_href); ?>">
				<span class="contact-main__row-text"><?= esc_html($phone); ?></span>
				<span class="contact-main__row-arrow" aria-hidden="true">
					<?php get_template_part('svg-template/svg-arrow'); ?>
				</span>
			</a>
		<?php endif; ?>

		<?php if ($email !== '') : ?>
			<a class="contact-main__row" href="<?= esc_url('mailto:' . antispambot($email)); ?>">
				<span class="contact-main__row-text"><?= esc_html($email); ?></span>
				<span class="contact-main__row-arrow" aria-hidden="true">
					<?php get_template_part('svg-template/svg-arrow'); ?>
				</span>
			</a>
		<?php endif; ?>

		<?php if ($has_address) : ?>
			<a
				class="contact-main__row contact-main__row--address"
				href="<?= esc_url($address_url); ?>"
				target="_blank"
				rel="noopener noreferrer"
			>
				<span class="contact-main__row-stack">
					<?php if ($address_eyebrow !== '') : ?>
						<span class="contact-main__row-eyebrow"><?= esc_html($address_eyebrow); ?></span>
					<?php endif; ?>
					<?php if ($address_main !== '') : ?>
						<span class="contact-main__row-sub"><?= esc_html($address_main); ?></span>
					<?php endif; ?>
				</span>
				<span class="contact-main__row-arrow" aria-hidden="true">
					<?php get_template_part('svg-template/svg-arrow'); ?>
				</span>
			</a>
		<?php endif; ?>
	</div>
</main>

<?php get_footer(); ?>
