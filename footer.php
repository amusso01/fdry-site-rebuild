<?php

/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package understrap
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$container = get_theme_mod('understrap_container_type');
?>


<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<style>
    :root{
    --slide-w: 250px;    /* ancho de .slide */
    --slide-gap: 60px;   /* margen total lateral por slide (30px left + 30px right) */
    --unit: calc(var(--slide-w) + var(--slide-gap)); /* ancho real ocupado por cada slide */
    --visible: 6;        /* cuántos logos quieres ver */
    }

    @keyframes scroll {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(calc(-1 * var(--unit) * var(--visible)));
    }
    }

    .sliderbody .slider {
    height: 56px;
    margin: auto;
    overflow: hidden;
    position: relative;
    width: 100%;
    max-width: 100%;
    margin: 44px 0;
    }
    .sliderbody .slider .slide-track {
    animation: scroll 40s linear infinite;
    display: flex;
    width: calc(var(--unit) * (var(--visible) * 2));
    }

    .sliderbody .slider .slide {
    height: 56px;
    width: var(--slide-w);
    box-sizing: border-box;
    flex: 0 0 auto;
    }

    .sliderbody .slider .slide img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    max-width: 200px;
    }

    .tech-banner{background-color: #F7F5F5;}
    .tech-banner h2{color: #191919; font-size: 60px; font-style: normal; font-weight: 500; line-height: normal; letter-spacing: -3px;}
    .tech-banner p{color: #686868; font-size: 16px; font-style: normal; font-weight: 500; line-height: normal;}

    .stickymenu{padding: 12px 32px; border-radius: 40px; border: 1px solid #DDD; background: rgba(248, 248, 248, 0.40); backdrop-filter: blur(15px); transition: all 0.3s ease; width: 95%; margin: 0 auto; z-index: 999; position: relative;}
    .navmain.scrolled .stickymenu{border-radius: 40px; border: 1px solid #DDD; background: rgba(248, 248, 248, 0.40); backdrop-filter: blur(15px); top: 20px;}


    .btnfdry {
      border-radius: 28px;
      background: #4951F2;
      padding: 16px 28px;
      color: #FFF;
      text-align: center;
      font-size: 20px;
      font-style: normal;
      font-weight: 600;
      line-height: normal;
      transition: all 0.3s ease-in-out;
      box-shadow: 0 4px 10px rgba(73, 81, 242, 0.2);
    }

    .btnfdry:hover {
      background-color: #343AAD;
      color: #FFFFFF;
      box-shadow: 0 6px 14px rgba(73, 81, 242, 0.35);
    }
    .contentmenu {
      border-radius: 52px;
      background: rgba(33, 33, 33, 0.70);
      position: absolute;
      width: 96%;
      top: -15px;
      left: 0;
      right: 0;
      height: 0;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      gap: 1.5rem;
      margin: 0 auto;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      opacity: 0;
      transform: translateY(-20px);
    }

    /* Estado visible (activo) */
    .contentmenu.active {
      height: 98vh;
      opacity: 1;
      transform: translateY(0);
    }
    .navmain{margin: 20px 0;}
    .navmain.scrolled .contentmenu {top: 6px!important;}

    .hamburgerbtn{cursor: pointer;}

    .videohome{max-width: 95%; margin: 0 auto;}
    .videohome video{height: 732px; width: 100%; border-radius: 20px; object-fit: cover;}

    /*Temp*/
    /*header{display: none;}
    footer, #wrapper-footer, .footerbanner{display: none;}*/

    /*Fdry footer*/
    .fdryfooter{background-color: #191919; padding-top: 94px;}
    .fdryfooter .container{max-width: 95%; margin: 0 auto;}
    .fdryfooter svg{margin-top: 123px;}
    .last-section{background-color: #212121; padding: 22px 0;}
    .last-section p{color: #A4A4A4; font-size: 13px; font-style: normal; font-weight: 500; line-height: normal; margin: 0;}

    .socialfdry{padding: 36px 0;}
    .socialfdry a:hover{color: #fff;}

    .allsocial{margin-bottom: 0;}
    .allsocial a, .allsocial span{color: #A4A4A4; font-size: 16px; font-style: normal; font-weight: 500; line-height: normal;}
    .mainfooter h2{margin-bottom: 26px; color: #F7F5F5; font-size: 80px; font-style: normal; font-weight: 500; line-height: 84px;}
    .mainfooter h3{margin-bottom: 26px; color: #F7F5F5; font-size: 24px; font-style: normal; font-weight: 400; line-height: 36px;}
    .mainfooter p{margin-bottom: 10px; color: #F7F5F5; font-size: 16px; font-style: normal; font-weight: 500; line-height: 22px;}
    .mainfooter li a{color: #F7F5F5; font-size: 16px; font-style: normal; font-weight: 500; line-height: normal;}
    .mainfooter li{margin-bottom: 10px;}
    .infocol a{color: #F7F5F5; font-size: 16px; font-style: normal; font-weight: 500; line-height: normal;}
    .last-section .container{max-width: 95%; margin: 0 auto;}

    .btnwhite{max-width: 250px; width: 100%;border-radius: 6px; background: #FFF; color: #212121; text-align: center; font-size: 20px; font-style: normal; font-weight: 600; line-height: normal; padding: 16px 10px; border: 2px solid transparent;}
    .btnwhite:hover{border-radius: 6px; background: transparent; color: #fff; text-align: center; font-size: 20px; font-style: normal; font-weight: 600; line-height: normal; padding: 16px 10px; border: 2px solid #fff;}
    .btntransparent{max-width: 250px; width: 100%; border-radius: 6px; background: transparent; color: #fff; text-align: center; font-size: 20px; font-style: normal; font-weight: 600; line-height: normal; padding: 16px 10px; border: 2px solid #fff;}
    .btntransparent:hover{border-radius: 6px; background: #FFF; color: #212121; text-align: center; font-size: 20px; font-style: normal; font-weight: 600; line-height: normal; padding: 16px 10px; border: 2px solid #fff;}

    .mobilefootermenu{display: none;}

    .Accordions{display: block; max-width: 100%; margin: auto;}
    .Accordion_item {width: 100%; height: auto; margin: 5px 0; border-bottom: 1px solid #363636;}
    .Accordion_item .title_tab {width: 100%; background-color: transparent; color: #fff; padding: 20px 0; cursor: pointer; transition: background-color 0.3s  ease-in; border-radius: 0;}
    .Accordion_item .title_tab .title {margin: 0; color: #F7F5F5; font-size: 20px; font-style: normal; font-weight: 500; line-height: normal; position: relative;}
    .inner_content {width: 100%; height: auto; display: none; overflow: hidden;}
    .inner_content p, .inner_content a{color: #F7F5F5; font-size: 16px; font-style: normal; font-weight: 500; line-height: normal;}
    .mobilefootermenu .inner_content  h3 {color: #F7F5F5; font-size: 24px; font-style: normal; font-weight: 700; line-height: 36px; margin: 20px 0 5px; font-size: 24px !important;}
    .mobilefootermenu .inner_content  h3:first-child{margin-top: 0!important;}

    .Accordion_item .title_tab .title {position: relative;}
    .Accordion_item .title_tab .title .icon {position: absolute; right: 1%; top: calc(50% - 8px); width: 20px; height: 20px; background-color: transparent; transition: all 0.3s ease-in-out;}
    .Accordion_item .title_tab .title .icon::before, .Accordion_item .title_tab .title .icon::after {content: ''; position: absolute; background-color: #fcfcfc; transition: all 0.3s ease-in-out;}
    .Accordion_item .title_tab .title .icon::before {top: 0; left: 50%; width: 2px; height: 100%; transform: translateX(-50%);}
    .Accordion_item .title_tab .title .icon::after { top: 50%; left: 0; width: 100%; height: 2px; transform: translateY(-50%);}
    .Accordion_item .title_tab.active .title .icon::before {opacity: 0; }

    .logofdry{display: flex; justify-content: center; align-items: center;}

    .graphMobile{display: none;}

    .mainfooter.menufooter li a{color: #A4A4A4;}
    .mainfooter.menufooter h3{font-size: 16px; margin-bottom: 10px!important;}
    .mainfooter.menufooter ul li a:hover{color: #fff;}
    .mainfooter.menufooter{padding: 68px 0 0;}
    .infocol a:hover{color: #ccc;}

    /*Responsive*/
    @media(max-width:  900px){
      .graphMobile{display: block;}
      .graphDesktop{display: none;}
    }
    @media (max-width: 800px) {
      .navmain .container-fluid {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-direction: row-reverse !important; /* invierte el orden */
      }
      .navmain .container-fluid > div:nth-child(2),
      .logo {
        flex: 1 !important;
        display: flex !important;
        justify-content: flex-start !important;
        text-align: left !important;
      }
      .navmain .container-fluid img,
      .navmain .container-fluid .logo {
        position: static !important;
        left: auto !important;
        transform: none !important;
        margin: 0 !important;
      }
      .navmain .container-fluid > div:first-child {
        flex: 1 !important;
        display: flex !important;
        justify-content: flex-end !important;
      }
      .navmain .container-fluid > div:last-child {
        display: none !important;
      }
      .contentmenu{width: 98%; top: -15px;}
    }


    @media (max-width: 700px) {
        .videohome #iframe {
            display: block;
        }
    }
    @media(max-width: 767px){
      .mainfooter h2{font-size: 46px; line-height: normal;}
      .mainfooter h3{font-size: 17px; line-height: normal;}
      .infodesktop{display: none;}
      .mainfooter {padding: 20px 0 36px;}
      .fdryfooter{padding: 0;}
      .fdryfooter svg {margin-top: 0px; height: auto;}
      .last-section .container{align-items: start;}
      .last-section p br{display: none;}

      .mobilefootermenu{display: block;}

      .sectiongrid{padding-left: 15px; padding-right: 15px;}

      .mainfooter.menufooter{display: none;}
    }
</style>

<?php get_template_part('loop-templates/tech', 'banner'); ?>

<?php get_template_part('sidebar-templates/sidebar', 'footerfull'); ?>

	<div class="wrapper" id="wrapper-footer" style="display: block!important;">
      <div class="container-fluid brief-footer footer__brief-bg">
        <div class="container">
          <div class="row">
            <div class="col-lg-9 col-md-6">
              <div class="canvatext">
                <span>Let’s talk</span>
                <h2 class="h2title">Send us<br>your brief</h2>
                <p>and calculate your budget and timescale</p>
              </div>
            </div>
            <div class="col-lg-3 col-md-3 offset-md-3 left">
              <a href="<?php echo site_url('/brief-1/'); ?>" id="box" class="btn brief">
                <p>START</p>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
<!-- New Footer FDRY 2025  -->
    <div class="fdryfooter">
      <div class="container">
        <div class="mainfooter mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
          
          <!-- Columna izquierda -->
          <div>
            <h2 class="mb-4">Come & say hello</h2>
            <h3>123 Buckingham Palace Rd, London SW1W 9SH</h3>
            <div class="flex flex-row gap-4">
              <a href="/brief-1/" class="btnwhite flex-1 text-center">Book an appointment</a>
              <a href="javascript:void(0)" id="fdryklaviyo-open-btn" class="btntransparent fdryklaviyo-open-button flex-1 text-center">Join our newsletter</a>
            </div>
          </div>

          <!-- Columna derecha -->
          <div class="infodesktop grid place-items-end text-right">
            <div class="infocol">
              <p><a href="mailto:studio@fdry.com">studio@fdry.com</a></p>
              <p><a href="tel:+4402081234669">+44 (0) 20 81234669</a></p>
              <p>123 BPR, London, SW1W 9SH</p>
            </div>
          </div>

          <div class="mobilefootermenu">
            <div class="">
              
              <div class="Accordions">
                <div class="Accordion_item">
                  <div class="title_tab">
                    <h3 class="title">
                      Contact
                      <span class="icon"></span>
                    </h3>
                  </div>
                  <div class="inner_content">
                    <p><a href="mailto:studio@fdry.com">studio@fdry.com</a></p>
                    <p><a href="tel:+4402081234669">+44 (0) 20 81234669</a></p>
                    <p>123 BPR, London, SW1W 9SH</p>
                  </div>
                </div>
                
                <div class="Accordion_item">
                  <div class="title_tab">
                    <h3 class="title">Services<span class="icon"></span></h3>
                  </div>
                  <div class="inner_content">
                    <h3>Create</h3>
                    <ul>
                      <li><a href="/service/brand-creative/">Brand & Create</a></li>
                      <li><a href="/service/ux-ui/">UX & UI</a></li>
                      <li><a href="/service/web-design-agency/">Web Design</a></li>
                      <li><a href="/service/ecommerce/">Ecommerce</a></li>
                      <li><a href="/shopify-agency/">Shopify Agency</a></li>
                      <li><a href="/woocommerce-agency/">WooCommerce Agency</a></li>
                      <li><a href="/wordpress-agency/">Wordpress Agency</a></li>
                      <li><a href="/adobe-commerce-agency/">Adobe Commerce Agency</a></li>
                    </ul>

                    <h3>Grow</h3>
                    <ul>
               		<li><a href="/service/seo-agency/">SEO Marketing</a></li>
            	    <li><a href="/service/geo-marketing-agency/">GEO Marketing</a></li>
               		<li><a href="/service/ecommerce-seo-agency/">Ecommerce SEO</a></li>
               		<li><a href="/service/b2b-seo-agency-london/">B2B SEO</a></li>
                	<li><a href="/service/ai-seo-agency-london/">AI SEO</a></li>
               		<li><a href="/servic/technical-seo-agency/">Technical SEO</a></li>
                	  <li><a href="/service/paid-advertising/">Paid Media Ads</a></li>
                      <li><a href="/service/social-media-marketing/">Social Media Marketing</a></li>
                      <li><a href="/service/email-marketing/">Email Marketing Campaigns</a></li>
                    </ul>

                    <h3>Sectors</h3>
                    <ul>
                      <li><a href="/sectors/retail-ecommerce/">Retail and Ecommerce</a></li>
                      <li><a href="/sectors/healthcare-wellness/">Healthcare and Wellness</a></li>
                      <li><a href="/sectors/financial-services/">Financial Services</a></li>
                      <li><a href="/sectors/manufacturing-industrials/">Manufacturing and Industrials</a></li>
                      <li><a href="/sectors/professional-services/">Professional Services</a></li>
                    </ul>
                  </div>
                </div>

                <div class="Accordion_item">
                  <div class="title_tab">
                    <h3 class="title">Company<span class="icon"></span></h3>
                  </div>
                  <div class="inner_content">
                    <ul>
                      <li><a href="/about/">About</a></li>
                      <li><a href="/careers/">Careers</a></li>
                      <li><a href="/service/">Services</a></li>
                      <li><a href="/work/">Work</a></li>
                      <li><a href="/insights/">Insights</a></li>
                      <li><a href="/contact/">Contact</a></li>
                    </ul>

                  </div>
                </div>
                
              </div>
            </div>
          </div>
        </div>

        <div class="mainfooter menufooter text-left mx-auto grid grid-cols-4 md:grid-cols-4 gap-2">
          <div>
            <h3>Create</h3>
            <ul>
              <li><a href="/service/brand-creative/">Brand & Create</a></li>
              <li><a href="/service/ux-ui/">UX & UI</a></li>
              <li><a href="/service/web-design-agency/">Web Design</a></li>
              <li><a href="/service/ecommerce/">Ecommerce</a></li>
              <li><a href="/shopify-agency/">Shopify Agency</a></li>
              <li><a href="/woocommerce-agency/">WooCommerce Agency</a></li>
              <li><a href="/wordpress-agency/">Wordpress Agency</a></li>
              <li><a href="/adobe-commerce-agency/">Adobe Commerce Agency</a></li>
            </ul>
          </div>

          <div>
            <h3>Grow</h3>
            <ul>
               <li><a href="/service/seo-agency/">SEO Marketing</a></li>
               <li><a href="/service/geo-marketing-agency/">GEO Marketing</a></li>
               <li><a href="/service/ecommerce-seo-agency/">Ecommerce SEO</a></li>
               <li><a href="/service/b2b-seo-agency-london/">B2B SEO</a></li>
                <li><a href="/service/ai-seo-agency-london/">AI SEO</a></li>
               <li><a href="/servic/technical-seo-agency/">Technical SEO</a></li>
              <li><a href="/service/paid-advertising/">Paid Media Ads</a></li>
              <li><a href="/service/social-media-marketing/">Social Media Marketing</a></li>
              <li><a href="/service/email-marketing/">Email Marketing Campaigns</a></li>
            </ul>
          </div>

          <div>
            <h3>Sectors</h3>
            <ul>
              <li><a href="/sectors/retail-ecommerce/">Retail and Ecommerce</a></li>
              <li><a href="/sectors/healthcare-wellness/">Healthcare and Wellness</a></li>
              <li><a href="/sectors/financial-services/">Financial Services</a></li>
              <li><a href="/sectors/manufacturing-industrials/">Manufacturing and Industrials</a></li>
              <li><a href="/sectors/professional-services/">Professional Services</a></li>
            </ul>
          </div>

          <div>
            <h3>Company</h3>
            <ul>
              <li><a href="/about/">About</a></li>
              <li><a href="/careers/">Careers</a></li>
              <li><a href="/service/">Services</a></li>
              <li><a href="/work/">Work</a></li>
              <li><a href="/insights/">Insights</a></li>
              <li><a href="/contact/">Contact</a></li>
            </ul>
          </div>

        </div>

        <svg xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 1260 320"
             preserveAspectRatio="xMidYMid meet"
             width="100%"
             fill="none"
             style="height: auto;">
          <path d="M232.571 70.2705H83.6914V140.521H214.486V210.318H83.6914V320H0V293.712H29.0186V283.288H0V0.0195312H232.571V70.2705ZM519.393 22.2256C542.103 34.4628 560.607 52.5923 574.065 75.707C587.944 101.088 595.515 130.095 594.674 160.461C595.515 189.921 588.365 219.381 574.065 244.762C560.187 269.236 539.579 288.272 515.607 300.509C488.271 314.106 458.831 319.997 428.972 319.997H296.074L372.616 218.021V247.934H425.607C451.262 247.934 471.449 240.229 487.01 224.819C502.57 209.41 510.141 187.655 510.141 160.461C510.141 133.267 502.571 111.511 487.01 96.1016C482.804 91.5693 477.757 87.9441 472.71 84.7715L519.393 22.2256ZM1156.12 187.652L1146.45 205.329V319.996H1062.76V203.969L1018.18 124.2L1156.12 187.652ZM770.053 71.6299H733.884V165.448H780.566C797.809 165.448 810.847 161.369 819.679 153.211C828.51 144.6 833.558 131.909 832.717 118.766C833.558 105.622 828.51 92.9318 819.679 83.8672C810.847 75.7091 797.809 71.6299 780.566 71.6299H779.726V0.472656H785.613C809.585 0.0194275 833.137 5.00458 855.427 14.9756C874.352 23.5869 889.912 38.0907 901.268 56.2197C912.202 74.8021 917.67 96.5574 917.249 118.766C917.67 140.067 912.203 160.916 902.109 179.045C891.595 196.721 876.876 210.771 859.212 219.383L923.558 319.547H833.558L779.726 234.792H733.884V319.547H650.192V0.0195312H770.053V71.6299ZM428.972 0.0244141C457.149 -0.428816 484.907 5.46309 510.562 17.2471L463.879 79.793C451.683 74.8074 438.645 72.541 425.607 72.541H372.617V201.258L288.925 313.206V0.0244141H428.972ZM1108.6 126.924L1178.83 0.0195312H1260L1161.17 178.592L1010.19 108.795L949.625 0.0195312H1038.36L1108.6 126.924Z" fill="#212121"/>
        </svg>


        <div class="socialfdry">
          <div class="mx-auto flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
            
              <!-- socials -->
              <ul class="allsocial flex flex-wrap gap-6">
                <li><a href="https://www.instagram.com/FDRY_digital/" target="_blank">Instagram</a></li>
                <li><a href="https://www.linkedin.com/company/fdry" target="_blank">LinkedIn</a></li>
              </ul>

              <!-- Política y copyright -->
              <div class="allsocial flex items-center gap-6">
                <a href="/terms-and-conditions/">Terms</a>
                <a href="/privacy-policy/">Privacy Policy</a>
                <span>© 2025 FDRY</span>
              </div>

            </div>
          </div>

      </div>
    </div>

    <div class="last-section">
          <div class="container mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            
            <div>
              <p>COPYRIGHT &#169; <script>document.write(new Date().getFullYear())</script> <br> FDRY Digital Marketing Agency - WordPress, WooCommerce and Shopify Web Design Agency.</p>
            </div>
            

            <!-- copyright -->
            <div class="flex items-center gap-6">
              <p>FDRY is a trading name of Foundry Digital Limited</p>
            </div>

          </div>
    </div>

    <!-- Start footer pop up -->
    <script>
        $(document).ready(function() {

            var $openBtn = $('#fdryklaviyo-open-btn');

            $openBtn.on('click', function() {
                
                window._klOnsite = window._klOnsite || [];
                window._klOnsite.push(['openForm', 'RdsGcP']);

                console.log("Disparando el formulario de Klaviyo: RdsGcP");
            });

        });
    </script>
    <!-- End footer pop up -->
    <!-- End Footer FDRY 2025 -->

<?php wp_footer(); ?>
<script type="text/javascript">
      var $titleTab = $('.title_tab');

      $titleTab.on('click', function(e) {
        e.preventDefault();
        if ($(this).hasClass('active')) {
          $(this).removeClass('active');
          $(this).next().stop().slideUp(500);
          $(this).next().find('p').removeClass('show');
        } else {
          $(this).addClass('active');
          $(this).next().stop().slideDown(500);
          $(this).parent().siblings().children('.title_tab').removeClass('active');
          $(this).parent().siblings().children('.inner_content').slideUp(500);
          $(this).parent().siblings().children('.inner_content').find('p').removeClass('show');
          $(this).next().find('p').addClass('show');
        }
      });
</script>
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
	AOS.init();
</script>

<script src="<?php echo get_template_directory_uri(); ?>/mainjs/footer.js"></script>


<br><br>

<div class="container">
	<div style="text-align:center; font-size:11px; color:#666; width:70%; margin: 0 auto;">
<br>
                <h2><b>Ecommerce web design agency | Web designers & developers in London</b></h2>
<span id="dots2"></span><span id="more2" style="display:none">

<p style="text-align:left; font-size:12px; color:#666">At FDRY, our London-based Fulham, Victoria and Chelsea team of specialist web designers and web developers deliver world-class e-commerce web design services that help ambitious British brands dominate their niches.</p>
<br><br>

<p style="text-align:left; font-size:12px; color:#666">We combine stunning, brand-unique visual design with bulletproof technical development to create bespoke online stores on WooCommerce, Shopify, WordPress, Adobe Commerce (Magento), and BigCommerce. </p>
<br><br>

<p style="text-align:left; font-size:12px; color:#666">Our web designers craft pixel-perfect, mobile-first experiences that showcase your products beautifully and guide users seamlessly to checkout, while our senior web developers build custom functionality, flawless integrations with payment gateways, CRM systems, and inventory tools, and performance that consistently achieves sub-2-second load times and 98–100 Core Web Vitals scores.</p>
<br><br>

<p style="text-align:left; font-size:12px; color:#666">From high-volume D2C brands to complex B2B marketplaces, FDRY’s e-commerce web designers and web developers have driven 300 %+ revenue increases for clients across the UK. We handle everything—strategy, design, development, launch, and ongoing conversion rate optimisation, so you get a future-proof store that grows with your business.</p>
<br><br>



<p style="text-align:left; font-size:12px; color:#666">Ready to scale your online store without the growing pains? As a specialist ecommerce development agency based in Chelsea and Victoria, London, we’re the ecommerce replatforming partner and ecommerce optimization agency that small-to-enterprise brands trust when revenue (not just traffic) is the goal.
 Contact us today for a zero-obligation custom ecommerce development quote and discover why London’s fastest-growing brands choose us as their long-term ecommerce replatforming partner. Your checkout page deserves better – let’s make it happen.</p>
<br><br>

<p style="text-align:left; font-size:12px; color:#666">Need a digital marketing agency that actually moves the needle instead of just sending pretty reports? We’re a Chelsea/Victoria-based full-service marketing agency obsessed with predictable, scalable growth for B2B and [Industry, e.g., SaaS] companies — from enterprise-grade SEO that dominates page one to hyper-targeted PPC campaigns that lower CAC and fill pipelines fast.
No fluffy “awareness” packages here. Our digital marketing retainer services are built around your revenue goals, with transparent full-service marketing agency pricing and fixed-fee digital marketing agency cost quotes you’ll never need to second-guess. Whether you’re an enterprise hunting for a battle-tested SEO company for enterprise, a SaaS founder needing a marketing consultant who speaks pipeline (not vanity metrics), or a B2B brand ready to outspend and outsmart competitors, we become the embedded growth partner you wish you hired years ago.
Book a no-pressure PPC agency appointment or request your custom digital marketing agency cost quote today. Let’s turn your ad spend and organic traffic into revenue you can take to the bank.</p>
<br><br>

<p style="text-align:left; font-size:12px; color:#666">Ready for an e-commerce website that finally delivers the sales you deserve? Partner with FDRY’s expert web designers and web developers today.</p>
</span>
<br><br>
<button onclick="myFunction()" id="myBtnread">Read more</button>

<br>

<script>
function myFunction() {
  var dots2 = document.getElementById("dots2");
  var more2Text = document.getElementById("more2");
  var btnText = document.getElementById("myBtnread");

  if (dots2.style.display === "none") {
    dots2.style.display = "inline";
    btnText.innerHTML = "Read more"; 
    more2Text.style.display = "none";
  } else {
    dots2.style.display = "none";
    btnText.innerHTML = "Read less"; 
    more2Text.style.display = "inline";
  }
}
</script>

	</div>
</div>

<br><br>







</body>

</html>