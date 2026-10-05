<?php

/**
 * Site footer: contact, menus, logo, social and legal links, copyright bar.
 *
 * Rebuilt from the inline "FDRY 2025" footer without Tailwind. Styled in
 * _site-footer.scss.
 *
 * The menu grid is the "Footer menu 1" to "Footer menu 4" locations
 * (Appearance → Menus). Each column's heading is the menu's "Footer title"
 * ACF field (group_fd_footer_menu.json). A location with no menu assigned
 * prints nothing, and a menu with no title prints no heading. Below 768px
 * the same menus render as an accordion instead (menu-accordion.php).
 * The newsletter button opens the Klaviyo form (newsletterForm.js), which
 * finds it by the site-footer__newsletter class.
 *
 * The wrapper is a div with role="contentinfo", not <footer>: linktree.css
 * hides every <footer> on the linktree template.
 *
 * @author Andrea Musso
 *
 * @package foundry
 */

$contact = [
  'email'   => 'studio@fdry.com',
  'phone'   => '+44 (0) 20 81234669',
  'tel'     => '+4402081234669',
  'address' => '123 Buckingham Palace Rd, London SW1W 9SH',
];

// Only the locations with a menu assigned, so the grid has no empty columns.
// The label names the menu where there is no title: the nav's aria-label and
// the accordion trigger.
$footer_menus = [];

foreach (['footermenu_1', 'footermenu_2', 'footermenu_3', 'footermenu_4'] as $location) {
  if (! has_nav_menu($location)) {
    continue;
  }

  $title = (string) fdry_get_nav_menu_acf_field('footer_title', $location);

  $footer_menus[] = [
    'location' => $location,
    'title'    => $title,
    'label'    => $title !== '' ? $title : wp_get_nav_menu_name($location),
  ];
}
?>

<div class="site-footer" role="contentinfo">
  <div class="site-footer__main content-block content-block--footer">
    <div class="site-footer__top">
      <div class="site-footer__contact">
        <p class="site-footer__title" data-fade-up><?php esc_html_e('Come & say hello', 'foundry'); ?></p>
        <p class="site-footer__address" data-fade-up data-fade-up-delay="0.1"><?php echo esc_html($contact['address']); ?></p>
        <div class="site-footer__actions" data-fade-up data-fade-up-delay="0.2">
          <?php
          get_template_part('components/partials/button', null, [
            'variant' => 'white',
            'label'   => __('Book an appointment', 'foundry'),
            'url'     => 'https://www.fdry.com/brief/',
          ]);

          get_template_part('components/partials/button', null, [
            'variant' => 'transparent',
            'label'   => __('Join our newsletter', 'foundry'),
            'tag'     => 'button',
            'class'   => 'site-footer__newsletter',
          ]);
          ?>
        </div>
      </div>

      <address class="site-footer__info" data-fade-up data-fade-up-delay="0.3">
		<p><a class="site-footer__info-link" href="mailto:<?php echo esc_attr($contact['email']); ?>"><?php echo esc_html($contact['email']); ?></a></p>
        <p><a class="site-footer__info-link" href="tel:<?php echo esc_attr($contact['tel']); ?>"><?php echo esc_html($contact['phone']); ?></a></p>
        <p><?php echo esc_html($contact['address']); ?></p>
      </address>
    </div>

    <?php if ($footer_menus !== []) : ?>
      <div class="site-footer__menus">
        <?php foreach ($footer_menus as $i => $menu) : ?>
          <nav class="site-footer__nav" aria-label="<?php echo esc_attr($menu['label']); ?>" data-fade-up data-fade-up-delay="<?php echo esc_attr($i / 10); ?>">
            <?php if ($menu['title'] !== '') : ?>
              <p class="site-footer__menu-title"><?php echo esc_html($menu['title']); ?></p>
            <?php endif; ?>
            <?php
            wp_nav_menu([
              'theme_location' => $menu['location'],
              'container'      => false,
              'menu_class'     => 'site-footer__menu',
              'depth'          => 1,
              'fallback_cb'    => false,
            ]);
            ?>
          </nav>
        <?php endforeach; ?>
      </div>

      <?php get_template_part('components/footer/menu-accordion', null, ['menus' => $footer_menus]); ?>
    <?php endif; ?>

    <svg class="site-footer__logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1260 320" preserveAspectRatio="xMidYMid meet" fill="none" role="img" aria-label="FDRY" data-fade-up>
      <path d="M232.571 70.2705H83.6914V140.521H214.486V210.318H83.6914V320H0V293.712H29.0186V283.288H0V0.0195312H232.571V70.2705ZM519.393 22.2256C542.103 34.4628 560.607 52.5923 574.065 75.707C587.944 101.088 595.515 130.095 594.674 160.461C595.515 189.921 588.365 219.381 574.065 244.762C560.187 269.236 539.579 288.272 515.607 300.509C488.271 314.106 458.831 319.997 428.972 319.997H296.074L372.616 218.021V247.934H425.607C451.262 247.934 471.449 240.229 487.01 224.819C502.57 209.41 510.141 187.655 510.141 160.461C510.141 133.267 502.571 111.511 487.01 96.1016C482.804 91.5693 477.757 87.9441 472.71 84.7715L519.393 22.2256ZM1156.12 187.652L1146.45 205.329V319.996H1062.76V203.969L1018.18 124.2L1156.12 187.652ZM770.053 71.6299H733.884V165.448H780.566C797.809 165.448 810.847 161.369 819.679 153.211C828.51 144.6 833.558 131.909 832.717 118.766C833.558 105.622 828.51 92.9318 819.679 83.8672C810.847 75.7091 797.809 71.6299 780.566 71.6299H779.726V0.472656H785.613C809.585 0.0194275 833.137 5.00458 855.427 14.9756C874.352 23.5869 889.912 38.0907 901.268 56.2197C912.202 74.8021 917.67 96.5574 917.249 118.766C917.67 140.067 912.203 160.916 902.109 179.045C891.595 196.721 876.876 210.771 859.212 219.383L923.558 319.547H833.558L779.726 234.792H733.884V319.547H650.192V0.0195312H770.053V71.6299ZM428.972 0.0244141C457.149 -0.428816 484.907 5.46309 510.562 17.2471L463.879 79.793C451.683 74.8074 438.645 72.541 425.607 72.541H372.617V201.258L288.925 313.206V0.0244141H428.972ZM1108.6 126.924L1178.83 0.0195312H1260L1161.17 178.592L1010.19 108.795L949.625 0.0195312H1038.36L1108.6 126.924Z" fill="#212121" />
    </svg>

    <div class="site-footer__meta">
      <ul class="site-footer__social">
        <li><a class="site-footer__meta-link" href="https://www.instagram.com/FDRY_digital/" target="_blank" rel="noopener">Instagram</a></li>
        <li><a class="site-footer__meta-link" href="https://www.linkedin.com/company/fdry" target="_blank" rel="noopener">LinkedIn</a></li>
      </ul>

      <div class="site-footer__legal">
        <a class="site-footer__meta-link" href="/terms-and-conditions/"><?php esc_html_e('Terms', 'foundry'); ?></a>
        <a class="site-footer__meta-link" href="/privacy-policy/"><?php esc_html_e('Privacy Policy', 'foundry'); ?></a>
        <span class="site-footer__meta-text">&#169; <?php echo esc_html(wp_date('Y')); ?> FDRY</span>
      </div>
    </div>
  </div>

  <div class="site-footer__bottom">
    <div class="site-footer__bottom-inner content-block content-block--footer">
      <p class="site-footer__copyright">&#169; <?php echo esc_html(wp_date('Y')); ?> <br> FDRY Ecommerce Agency - Adobe, WordPress, Woo and Shopify Web Design Agency.</p>
      <p class="site-footer__copyright">FDRY is a trading name of Foundry Digital Limited</p>
    </div>
  </div>
</div>
