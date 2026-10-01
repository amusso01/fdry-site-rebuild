<?php

/**
 * Footer menu accordion: Footer menu 1–4 as one panel each, below 768px.
 *
 * Included from site-footer.php, which hides its menu grid at the same
 * breakpoint. footerAccordion.js starts accordion-js on .footer-accordion,
 * one panel open at a time. Styled in _footer-accordion.scss.
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     @type array $menus Footer menus from site-footer.php, each with
 *                        'location', 'title' and 'label'.
 * }
 */

$menus = $args['menus'] ?? [];

if ($menus === []) {
  return;
}
?>

<div class="site-footer__accordion" data-fade-up>
  <div class="accordion-container footer-accordion">
    <?php foreach ($menus as $menu) : ?>
      <div class="ac footer-accordion__item">
        <div class="ac-header footer-accordion__header">
          <button type="button" class="ac-trigger footer-accordion__trigger">
            <?php echo esc_html($menu['label']); ?>
            <span class="footer-accordion__icon" aria-hidden="true"></span>
          </button>
        </div>
        <div class="ac-panel footer-accordion__panel">
          <div class="ac-panel-inner footer-accordion__panel-inner">
            <?php
            wp_nav_menu([
              'theme_location' => $menu['location'],
              'container'      => false,
              'menu_class'     => 'footer-accordion__list',
              'depth'          => 1,
              'fallback_cb'    => false,
            ]);
            ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
