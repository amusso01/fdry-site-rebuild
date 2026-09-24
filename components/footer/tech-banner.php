<?php

/**
 * Tech banner: platform logos on a line grid.
 *
 * 8×3 grid on desktop with the logos in the middle row; the other cells are
 * empty so the lines run edge to edge. Below 768px only the logos show, 2 per
 * row. techGrid.js draws the plus marks and the travelling line on top.
 *
 * @author Andrea Musso
 *
 * @package foundry
 */

$logos = array(
  'shopify'        => 'Shopify',
  'woocommerce'    => 'WooCommerce',
  'meta'           => 'Meta',
  'google-partner' => 'Google Partner',
  'big-commerce'   => 'BigCommerce',
  'square'         => 'Square',
);

$cols      = 8;
$rows      = 3;
$logo_row  = 1;
$logo_urls = array();

foreach ($logos as $file => $name) {
  $logo_urls[] = array(
    'src' => get_template_directory_uri() . '/img/img/' . $file . '.svg',
    'alt' => $name,
  );
}
?>

<section class="tech-grid-section" aria-label="<?php esc_attr_e('Platforms we build on', 'foundry'); ?>">
  <div class="tech-grid" data-tech-grid>
    <?php for ($row = 0; $row < $rows; $row++) : ?>
      <?php for ($col = 0; $col < $cols; $col++) : ?>
        <?php $logo = $row === $logo_row ? ($logo_urls[$col - 1] ?? null) : null; ?>
        <?php if ($logo) : ?>
          <?php
          // Fade the logo, not the cell: the cell hides the line-coloured grid
          // background, so fading it would flash a grey block.
          $fade_delay = ($col - 1) * 0.1;
          ?>
          <div class="tech-grid__cell">
            <img class="tech-grid__logo" src="<?php echo esc_url($logo['src']); ?>" alt="<?php echo esc_attr($logo['alt']); ?>" loading="lazy" data-fade-up<?= $fade_delay > 0 ? ' data-fade-up-delay="' . esc_attr(number_format($fade_delay, 1)) . '"' : ''; ?>>
          </div>
        <?php else : ?>
          <div class="tech-grid__cell tech-grid__cell--ghost" aria-hidden="true"></div>
        <?php endif; ?>
      <?php endfor; ?>
    <?php endfor; ?>
  </div>
</section>
