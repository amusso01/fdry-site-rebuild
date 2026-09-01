<?php

/**
 * Main Site Header Template
 * 
 * @author   Andrea Musso
 * 
 * @package  Foundry
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'FDRY_USING_NEW_HEADER' ) ) {
	define( 'FDRY_USING_NEW_HEADER', true );
}

?>


<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="theme-color" content="#5472C6" />

  <?php wp_head(); ?>
  <?php if (is_front_page()) {
  ?>
    <script>
      $(window).on('load', function() {
        $('#loading-animation').fadeOut(500);
      })
    </script>
  <?php
  }
  ?>

  <meta name="google-site-verification" content="bO-ylznjkqW94tOj0xpQaYvc2P5QTf40geNG1Hhx_fs" />


  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-title" content="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>">
  <link rel="profile" href="http://gmpg.org/xfn/11">
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=AW-11543866131">
  </script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'AW-11543866131');
  </script>

  <!-- Google Tag Manager -->
  <script>
    (function(w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({
        'gtm.start': new Date().getTime(),
        event: 'gtm.js'
      });
      var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s),
        dl = l != 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src =
        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-5D5B7P');
  </script>
  <!-- End Google Tag Manager -->


  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/mainstyle/typeformstyle.css">
  <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/mainstyle/mainstyle.css">


  <!-- Start cookieyes banner -->
  <script id="cookieyes" type="text/javascript" src="https://cdn-cookieyes.com/client_data/025fa60311aef15b7fe2e817/script.js"></script> <!-- End cookieyes banner -->

  <script type="text/javascript" src="https://secure.office-insightdetails.com/js/788650.js"></script>

  <!-- Hotjar Tracking Code for FDRY Wholesite -->
  <script>
    (function(h, o, t, j, a, r) {
      h.hj = h.hj || function() {
        (h.hj.q = h.hj.q || []).push(arguments)
      };
      h._hjSettings = {
        hjid: 5335370,
        hjsv: 6
      };
      a = o.getElementsByTagName('head')[0];
      r = o.createElement('script');
      r.async = 1;
      r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
      a.appendChild(r);
    })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
  </script>



  <!-- Apollo -->
  <script>
    function initApollo() {
      var n = Math.random().toString(36).substring(7),
        o = document.createElement("script");
      o.src = "https://assets.apollo.io/micro/website-tracker/tracker.iife.js?nocache=" + n, o.async = !0, o.defer = !0,
        o.onload = function() {
          window.trackingFunctions.onLoad({
            appId: "68d3da97c88cd90021279306"
          })
        },
        document.head.appendChild(o)
    }
    initApollo();
  </script>


  <!-- Meta Pixel Code -->
  <script>
    ! function(f, b, e, v, n, t, s) {
      if (f.fbq) return;
      n = f.fbq = function() {
        n.callMethod ?
          n.callMethod.apply(n, arguments) : n.queue.push(arguments)
      };
      if (!f._fbq) f._fbq = n;
      n.push = n;
      n.loaded = !0;
      n.version = '2.0';
      n.queue = [];
      t = b.createElement(e);
      t.async = !0;
      t.src = v;
      s = b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
      'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1558785528625318');
    fbq('track', 'PageView');
  </script>
  <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id=1558785528625318&ev=PageView&noscript=1" /></noscript>
  <!-- End Meta Pixel Code -->


  <link rel="stylesheet" href="https://jason5-dev.agis.ai/admin/styles.css">


</head>


<body <?php body_class(); ?>>

  <noscript><img alt="" src="https://secure.office-insightdetails.com/788650.png" style="display:none;" /></noscript>

  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5D5B7P"
      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->

  <div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'foundry'); ?></a>
    <header class="site-header">
      <div class="site-header__inner content-block">
        <div class="site-header__brand">
          <?php get_template_part('components/header/logo'); ?>
          <?php get_template_part('components/header/hamburger'); ?>
          <?php get_template_part('components/navigation/primary'); ?>
        </div>
        <div class="site-header__cta">
          <?php
          $nav_button_label  = get_field('nav_button_label', 'option');
          $nav_button_link   = get_field('nav_button_url', 'option');
          $nav_button_url    = '#';
          $nav_button_target = '';

          if (is_array($nav_button_link)) {
            $nav_button_url    = $nav_button_link['url'] ?? '#';
            $nav_button_target = $nav_button_link['target'] ?? '';

            if (! $nav_button_label && ! empty($nav_button_link['title'])) {
              $nav_button_label = $nav_button_link['title'];
            }
          } elseif (is_string($nav_button_link) && $nav_button_link !== '') {
            $nav_button_url = $nav_button_link;
          }

          $nav_button_url    = is_string($nav_button_url) ? $nav_button_url : '#';
          $nav_button_target = is_string($nav_button_target) ? $nav_button_target : '';
          $nav_button_label  = is_string($nav_button_label) ? $nav_button_label : '';

          if ($nav_button_label !== '') {
            get_template_part(
              'components/partials/button',
              null,
              array(
                'variant' => 'yellow',
                'label'   => $nav_button_label,
                'url'     => $nav_button_url,
                'target'  => $nav_button_target,
              )
            );
          }
          ?>
        </div>
      </div>
      <?php get_template_part('components/navigation/secondary'); ?>
    </header><!-- .site-header -->

    <div id="content" class="site-content">