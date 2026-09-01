<?php

/**
 * Button partial
 *
 * @author Andrea Musso
 *
 * @package Foundry
 *
 * @param array $args {
 *     @type string       $variant Color modifier: primary, white, yellow. Default 'primary'.
 *     @type string       $label   Button text. Default 'SERVICES'.
 *     @type string|array $url     Anchor href or ACF link array. Default '#'.
 *     @type string       $target  Optional link target (e.g. _blank).
 * }
 */

if ( ! isset( $args ) || ! is_array( $args ) ) {
	$args = array();
}

// get_template_part() may expose values via $args and/or extract() into local scope.
$variant = $args['variant'] ?? ( isset( $variant ) ? $variant : 'primary' );
$label   = $args['label'] ?? ( isset( $label ) ? $label : 'SERVICES' );
$url     = $args['url'] ?? ( isset( $url ) ? $url : '#' );
$target  = $args['target'] ?? ( isset( $target ) ? $target : '' );
$allowed = array( 'primary', 'white', 'yellow' );

if ( is_array( $url ) ) {
	if ( $target === '' && ! empty( $url['target'] ) ) {
		$target = $url['target'];
	}

	if ( ! $label && ! empty( $url['title'] ) ) {
		$label = $url['title'];
	}

	$url = $url['url'] ?? '#';
}

$label  = is_string( $label ) ? $label : '';
$url    = is_string( $url ) ? $url : '#';
$target = is_string( $target ) ? $target : '';

if ( ! in_array( $variant, $allowed, true ) ) {
	$variant = 'primary';
}
?>

<div class="btn btn--<?php echo esc_attr( $variant ); ?>">
	<a
		href="<?php echo esc_url( $url ); ?>"
		<?php if ( $target !== '' ) : ?>
			target="<?php echo esc_attr( $target ); ?>"
			<?php if ( $target === '_blank' ) : ?>
				rel="noopener noreferrer"
			<?php endif; ?>
		<?php endif; ?>
	>
		<?php echo esc_html( $label ); ?>
		<?php get_template_part( 'svg-template/svg-arrow' ); ?>
	</a>
</div>
