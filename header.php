<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$container = get_theme_mod( 'understrap_container_type' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="theme-color" content="#5472C6"/>
	
	<?php wp_head(); ?>
	<?php if (is_front_page()) {
	?>
	<script>
	$(window).on('load', function(){
    	$('#loading-animation').fadeOut(500);
	})
	</script>
	<?php
	}
	?>

	<meta name="google-site-verification" content="bO-ylznjkqW94tOj0xpQaYvc2P5QTf40geNG1Hhx_fs" />


	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?> - <?php bloginfo( 'description' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-11543866131">
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-11543866131');
</script>	
	
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5D5B7P');</script>
<!-- End Google Tag Manager -->

	
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/mainstyle/typeformstyle.css">
<link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />	
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/mainstyle/mainstyle.css">


<!-- Start cookieyes banner --> <script id="cookieyes" type="text/javascript" src="https://cdn-cookieyes.com/client_data/025fa60311aef15b7fe2e817/script.js"></script> <!-- End cookieyes banner -->

<script type="text/javascript" src="https://secure.office-insightdetails.com/js/788650.js" ></script>

<!-- Hotjar Tracking Code for FDRY Wholesite -->
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:5335370,hjsv:6};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>



<!-- Apollo -->
<script>function initApollo(){var n=Math.random().toString(36).substring(7),o=document.createElement("script");
o.src="https://assets.apollo.io/micro/website-tracker/tracker.iife.js?nocache="+n,o.async=!0,o.defer=!0,
o.onload=function(){window.trackingFunctions.onLoad({appId:"68d3da97c88cd90021279306"})},
document.head.appendChild(o)}initApollo();</script>


<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1558785528625318');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1558785528625318&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->


<link rel="stylesheet" href="https://jason5-dev.agis.ai/admin/styles.css">

	
</head>

<body data-rsssl=1 data-rsssl=1 data-rsssl=1 data-rsssl=1 data-rsssl=1 data-rsssl=1 <?php body_class(); ?>>

<noscript><img alt="" src="https://secure.office-insightdetails.com/788650.png" style="display:none;" /></noscript>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5D5B7P"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<style>
	.work-grid .work-box .hovereffect .overlay .work-title{width: 95%; word-wrap: break-word;overflow-wrap: break-word;}
	.work-grid .work-box .hovereffect:hover .overlay .work-title{width: 95%; word-wrap: break-word;overflow-wrap: break-word;}
	.brand .box-black .svg-brand img {max-width: initial!important;}
</style>



<?php if (is_front_page()) {
	?>

	<?php
}
?>
	<!--Header 2023-->
		
		<header>
		<div id="header2023">
			<div class="container-fluid">
				<div class="row">
					<div class="col-4 columnone">
						<div class="hamburgermenuopen">
					        <div class="hamburger" id="hamburger-1">
					          <span class="line"></span>
					          <span class="line secondline"></span>
					          <span class="line"></span>
					        </div>
					    </div>
					</div>
					<div class="col-4 columntwo">
						<center>
							<a class="custom-logo-link" rel="home" itemprop="url" href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<img src="<?php echo get_template_directory_uri(); ?>/img/svg/logofdry.svg" />
							</a>
						</center>
					</div>
					<div class="col-4 columnthree right">
						<div class="pic blackbutton">
							<a href="<?php echo site_url('/brief-1/');  ?>">
						  		<div class="button"><span>SEND A BRIEF</span></div>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="contenthamburgermenu">
			<div class="internalcontentmenu desktopmenu">
				<div class="container">
					<div class="row">
						<div class="col-6">
							<ul>
								<li><a href="<?php echo site_url( '/work/' ) ?>" class="nav-link">WORK</a></li>
								<li class="mainitem"><a href="<?php echo site_url( '/service/' ) ?>"  aria-haspopup="true" aria-expanded="false">SERVICES</a></li>
								<li><a href="<?php echo site_url( '/about/' ) ?>" class="nav-link">ABOUT</a></li>
								<li><a class="nav-link " href="<?php echo site_url( '/insights/' ) ?>">INSIGHTS</a></li>
								<li><a href="<?php echo site_url( '/contact/' ) ?>" class="nav-link">CONTACT</a></li>
								<div class="pic blackbutton">
								<a href="<?php echo site_url('/brief-1/');  ?>">
								  		<div class="button"><span>SEND A BRIEF</span></div>
									</a>
								</div>
							</ul>
						</div>
						<div class="col-6 allsubitems">
							<div class="subitems">
								<div class="row">
									<div class="col-6">
										<div class="itemsubmenu">
											<h3><a href="<?php echo get_the_permalink(6801); ?>">CREATE</a></h3>
											<li><a href="<?php echo get_the_permalink(6828); ?>">Brand & Creative</a></li>
											<li><a href="<?php echo get_the_permalink(6833) ?>">UX & UI</a></li>
											<li><a href="<?php echo get_the_permalink(6877); ?>">Web Design</a></li>
											<li><a href="<?php echo get_the_permalink(6892); ?>">Ecommerce</a></li>
										</div>
									</div>
									
									<div class="col-6">
										<div class="itemsubmenu">
											<h3><a href="<?php echo get_the_permalink(6821); ?>">GROW</a></h3>
											<li><a href="<?php echo get_the_permalink(6929); ?>">SEO Services & AI Search</a></li>
											<li><a href="<?php echo get_the_permalink(6949); ?>">Paid Media Ads</a></li>
											<li><a href="<?php echo get_the_permalink(7634); ?>">Social Media Marketing</a></li>
											<li><a href="<?php echo get_the_permalink(6940); ?>">Email Marketing Campaigns</a></li>
										</div>
									</div>

<div class="col-6">
</div>
								
	<div class="col-6">
										<a class="circleblack" href="<?php echo get_the_permalink(1355); ?>">SEND A BRIEF</a>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>	

			<div class="internalcontentmenu mobilmenu">
				<div class="container">
					<ul>
								<li><a href="<?php echo site_url( '/work/' ) ?>" class="nav-link">WORK</a></li>
								<li class="openhamburgermenu"><a href="<?php echo site_url( '/service/' ) ?>"  aria-haspopup="true" aria-expanded="false">SERVICES</a></li>

									<div class="mainservicemobil">
										<div class="itemsubmenu">
												<h3><a href="<?php echo get_the_permalink(6801); ?>">CREATE</a></h3>
												<li><a href="<?php echo get_the_permalink(6828); ?>">Brand & Creative</a></li>
												<li><a href="<?php echo get_the_permalink(6833) ?>">UX & UI</a></li>
												<li><a href="<?php echo get_the_permalink(6877); ?>">Web Design</a></li>
												<li><a href="<?php echo get_the_permalink(6892); ?>">Ecommerce</a></li>
										</div>
										




										<div class="itemsubmenu">
												<h3><a href="<?php echo get_the_permalink(6821); ?>">GROW</a></h3>
												<li><a href="<?php echo get_the_permalink(6929); ?>">SEO Services Search Marketing</a></li>
												<li><a href="<?php echo get_the_permalink(6949); ?>">Paid Advertising</a></li>
												<li><a href="<?php echo get_the_permalink(7634); ?>">Social Media Marketing</a></li>
												<li><a href="<?php echo get_the_permalink(6940); ?>">Email Marketing</a></li>
										</div>
									</div>


								<li><a href="<?php echo site_url( '/about/' ) ?>" class="nav-link">ABOUT</a></li>
								<li><a class="nav-link " href="<?php echo site_url( '/insights/' ) ?>">INSIGHTS</a></li>
								<li><a href="<?php echo site_url( '/contact/' ) ?>" class="nav-link">CONTACT</a></li>
								<div class="pic blackbutton">
								<a href="<?php echo site_url('/brief-1/');  ?>">
								  		<div class="button"><span>SEND A BRIEF</span></div>
									</a>
								</div>
					</ul>
				</div>
			</div>

		</div>
		</header>

		<script src="<?php echo get_template_directory_uri(); ?>/mainjs/header.js"></script>

	<!--Header 2023-->

	


	<div class="site" id="page">

<div class="line-gradient"></div>