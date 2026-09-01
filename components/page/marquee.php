<?php

/**
 * Logo marquee
 *
 * @author Andrea Musso
 *
 * @package foundry
 */

$post_id = get_queried_object_id();

if (! $post_id || ! have_rows('marquee_logos', $post_id)) {
  return;
}

$logos = array();

while (have_rows('marquee_logos', $post_id)) {
  the_row();

  $svg = acfFile_toSvg(get_sub_field('logo'));

  if ($svg) {
    $logos[] = $svg;
  }
}

if (! $logos) {
  return;
}
?>

<section class="marquee" aria-label="<?php esc_attr_e('Clients', 'foundry'); ?>">
  <div class="marquee__track">
    <ul class="marquee__list">
      <?php foreach ($logos as $svg) : ?>
        <li class="marquee__item">
          <div class="marquee__logo">
            <?= $svg; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <ul class="marquee__list" aria-hidden="true">
      <?php foreach ($logos as $svg) : ?>
        <li class="marquee__item">
          <div class="marquee__logo">
            <?= $svg; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>