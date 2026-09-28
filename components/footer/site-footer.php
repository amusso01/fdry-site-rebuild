<?php

/**
 * Site footer: contact, menus, logo, social and legal links, copyright bar.
 *
 * Rebuilt from the inline "FDRY 2025" footer without Tailwind. The old one is
 * kept in legacy-footer.php as a rollback. Styled in _site-footer.scss.
 *
 * Below 768px the contact details and menus move into the footer accordion
 * (footerAccordion.js). The newsletter button opens the Klaviyo form
 * (newsletterForm.js).
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
  'address' => '123 BPR, London, SW1W 9SH',
];

$menus = [
  'create'  => [
    'title' => __('Create', 'foundry'),
    'links' => [
      '/service/brand-creative/'     => __('Brand & Create', 'foundry'),
      '/service/ux-ui/'              => __('UX & UI', 'foundry'),
      '/service/web-design-agency/'  => __('Web Design', 'foundry'),
      '/service/ecommerce/'          => __('Ecommerce', 'foundry'),
      '/shopify-agency/'             => __('Shopify Agency', 'foundry'),
      '/woocommerce-agency/'         => __('WooCommerce Agency', 'foundry'),
      '/wordpress-agency/'           => __('Wordpress Agency', 'foundry'),
      '/adobe-commerce-agency/'      => __('Adobe Commerce Agency', 'foundry'),
    ],
  ],
  'grow'    => [
    'title' => __('Grow', 'foundry'),
    'links' => [
      '/service/seo-agency/'             => __('SEO Marketing', 'foundry'),
      '/service/geo-marketing-agency/'   => __('GEO Marketing', 'foundry'),
      '/service/ecommerce-seo-agency/'   => __('Ecommerce SEO', 'foundry'),
      '/service/b2b-seo-agency-london/'  => __('B2B SEO', 'foundry'),
      '/service/ai-seo-agency-london/'   => __('AI SEO', 'foundry'),
      '/service/technical-seo-agency/'   => __('Technical SEO', 'foundry'),
      '/service/paid-advertising/'       => __('Paid Media Ads', 'foundry'),
      '/service/social-media-marketing/' => __('Social Media Marketing', 'foundry'),
      '/service/email-marketing/'        => __('Email Marketing Campaigns', 'foundry'),
    ],
  ],
  'sectors' => [
    'title' => __('Sectors', 'foundry'),
    'links' => [
      '/sectors/retail-ecommerce/'          => __('Retail and Ecommerce', 'foundry'),
      '/sectors/healthcare-wellness/'       => __('Healthcare and Wellness', 'foundry'),
      '/sectors/financial-services/'        => __('Financial Services', 'foundry'),
      '/sectors/manufacturing-industrials/' => __('Manufacturing and Industrials', 'foundry'),
      '/sectors/professional-services/'     => __('Professional Services', 'foundry'),
    ],
  ],
  'company' => [
    'title' => __('Company', 'foundry'),
    'links' => [
      '/about/'    => __('About', 'foundry'),
      '/careers/'  => __('Careers', 'foundry'),
      '/service/'  => __('Services', 'foundry'),
      '/work/'     => __('Work', 'foundry'),
      '/insights/' => __('Insights', 'foundry'),
      '/contact/'  => __('Contact', 'foundry'),
    ],
  ],
];

// The mobile accordion groups the three service menus under one panel.
$accordion_groups = [
  __('Services', 'foundry') => ['create', 'grow', 'sectors'],
  __('Company', 'foundry')  => ['company'],
];
?>

<div class="site-footer" role="contentinfo">
  <div class="site-footer__main content-block content-block--footer">
    <div class="site-footer__top">
      <div class="site-footer__contact">
        <p class="site-footer__title" data-fade-up><?php esc_html_e('Come & say hello', 'foundry'); ?></p>
        <p class="site-footer__address" data-fade-up data-fade-up-delay="0.1"><?php esc_html_e('123 Buckingham Palace Rd, London SW1W 9SH', 'foundry'); ?></p>
        <div class="site-footer__actions" data-fade-up data-fade-up-delay="0.2">
          <a class="site-footer__button site-footer__button--solid" href="/brief-1/"><?php esc_html_e('Book an appointment', 'foundry'); ?></a>
          <button type="button" class="site-footer__button site-footer__button--outline site-footer__newsletter"><?php esc_html_e('Join our newsletter', 'foundry'); ?></button>
        </div>
      </div>

      <address class="site-footer__info" data-fade-up data-fade-up-delay="0.3">
        <p><a class="site-footer__info-link" href="mailto:<?php echo esc_attr($contact['email']); ?>"><?php echo esc_html($contact['email']); ?></a></p>
        <p><a class="site-footer__info-link" href="tel:<?php echo esc_attr($contact['tel']); ?>"><?php echo esc_html($contact['phone']); ?></a></p>
        <p><?php echo esc_html($contact['address']); ?></p>
      </address>

      <div class="site-footer__accordion">
        <div class="accordion-container footer-accordion">
          <div class="ac footer-accordion__item">
            <div class="ac-header footer-accordion__header">
              <button type="button" class="ac-trigger footer-accordion__trigger">
                <?php esc_html_e('Contact', 'foundry'); ?>
                <span class="footer-accordion__icon" aria-hidden="true"></span>
              </button>
            </div>
            <div class="ac-panel footer-accordion__panel">
              <div class="ac-panel-inner footer-accordion__panel-inner">
                <p><a href="mailto:<?php echo esc_attr($contact['email']); ?>"><?php echo esc_html($contact['email']); ?></a></p>
                <p><a href="tel:<?php echo esc_attr($contact['tel']); ?>"><?php echo esc_html($contact['phone']); ?></a></p>
                <p><?php echo esc_html($contact['address']); ?></p>
              </div>
            </div>
          </div>

          <?php foreach ($accordion_groups as $label => $keys) : ?>
            <div class="ac footer-accordion__item">
              <div class="ac-header footer-accordion__header">
                <button type="button" class="ac-trigger footer-accordion__trigger">
                  <?php echo esc_html($label); ?>
                  <span class="footer-accordion__icon" aria-hidden="true"></span>
                </button>
              </div>
              <div class="ac-panel footer-accordion__panel">
                <div class="ac-panel-inner footer-accordion__panel-inner">
                  <?php foreach ($keys as $key) : ?>
                    <?php if (count($keys) > 1) : ?>
                      <p class="footer-accordion__group-title"><?php echo esc_html($menus[$key]['title']); ?></p>
                    <?php endif; ?>
                    <ul class="footer-accordion__list">
                      <?php foreach ($menus[$key]['links'] as $url => $text) : ?>
                        <li><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($text); ?></a></li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <nav class="site-footer__menus" aria-label="<?php esc_attr_e('Footer', 'foundry'); ?>">
      <?php foreach (array_values($menus) as $i => $menu) : ?>
        <div class="site-footer__menu-col" data-fade-up data-fade-up-delay="<?php echo esc_attr($i / 10); ?>">
          <p class="site-footer__menu-title"><?php echo esc_html($menu['title']); ?></p>
          <ul class="site-footer__menu">
            <?php foreach ($menu['links'] as $url => $text) : ?>
              <li><a class="site-footer__menu-link" href="<?php echo esc_url($url); ?>"><?php echo esc_html($text); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    </nav>

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
        <span class="site-footer__meta-text">© 2025 FDRY</span>
      </div>
    </div>
  </div>

  <div class="site-footer__bottom">
    <div class="site-footer__bottom-inner content-block content-block--footer">
      <p class="site-footer__copyright">COPYRIGHT &#169; <?php echo esc_html(wp_date('Y')); ?> <br> FDRY Digital Marketing Agency - WordPress, WooCommerce and Shopify Web Design Agency.</p>
      <p class="site-footer__copyright">FDRY is a trading name of Foundry Digital Limited</p>
    </div>
  </div>
</div>
