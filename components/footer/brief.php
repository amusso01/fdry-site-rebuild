<?php

/**
 * Footer brief: "Let's talk / Send your brief" with the START circle.
 *
 * Text on the left, the pulsing START link to the brief form on the right,
 * over a static background image. The image only renders once the file is
 * on the server, so a missing upload leaves the flat background colour
 * instead of a broken image.
 *
 * @author Andrea Musso
 *
 * @package foundry
 */

$bg_path = '/img/footer/brief-bg.jpg';
$has_bg  = file_exists(get_template_directory() . $bg_path);
?>

<section class="footer-brief" aria-labelledby="footer-brief-title">
  <?php if ($has_bg) : ?>
    <img class="footer-brief__bg" src="<?php echo esc_url(get_template_directory_uri() . $bg_path); ?>" alt="" width="2880" height="720" loading="lazy" decoding="async">
  <?php endif; ?>

  <div class="footer-brief__inner content-block content-block--footer">
    <div class="footer-brief__text">
      <p class="footer-brief__eyebrow" data-fade-up><?php esc_html_e('Let’s talk', 'foundry'); ?></p>
      <h2 class="footer-brief__title" id="footer-brief-title" data-fade-up data-fade-up-delay="0.1"><?php esc_html_e('Send your brief', 'foundry'); ?></h2>
      <p class="footer-brief__lead" data-fade-up data-fade-up-delay="0.2"><?php esc_html_e('and calculate your budget and timescale', 'foundry'); ?></p>
    </div>

    <a class="footer-brief__button" href="<?php echo esc_url(site_url('/brief-1/')); ?>" data-fade-up data-fade-up-delay="0.3">
      <span><?php esc_html_e('Start', 'foundry'); ?></span>
    </a>
  </div>
</section>
