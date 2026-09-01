<?php
/**
 * Partial template for Contact page
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="container">
	<img class="img-fluid" src="<?php echo get_stylesheet_directory_uri() ?>/img/images/step2.png">
</div>
<div class="container contact-page">
	<?php echo do_shortcode( '[contact-form-7 id="1353" title="Brief-2"]' );?>
</div>