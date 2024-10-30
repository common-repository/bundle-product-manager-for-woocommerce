<?php
/**
 * Woocommerce template simple cart.
 *
 * @package iwpdev/bundle-product-manager
 */

defined( 'ABSPATH' ) || exit;

global $product;

$args = [
	'quantity'   => 1,
	'class'      => 'button bpm_add_to_cart_btn product_type_simple add_to_cart_button ajax_add_to_cart',
	'attributes' => [
		'data-product_id'  => $product->get_id(),
		'data-product_sku' => $product->get_sku() ?? '',
		'rel'              => 'nofollow',
	],
];

//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo apply_filters(
	'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
	sprintf(
		'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
		esc_url( $product->add_to_cart_url() ),
		esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
		esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
		isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
		esc_html( $product->add_to_cart_text() )
	),
	$product,
	$args
);
