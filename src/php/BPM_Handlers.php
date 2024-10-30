<?php
/**
 * Adding products that may be included in the package
 *
 * @package iwpdev/bundle-product-manager
 */

namespace BundleProductManager;

use WP_Query;

/**
 * BundleProductManager class file.
 */
class BPM_Handlers {

	/**
	 * Search product action and nonce name.
	 */
	public const SEARCH_PRODUCT_ACTION_NAME = 'bpm_search_product';

	/**
	 * Save product nonce code.
	 */
	public const SAVE_PRODUCT_NONCE = 'bpm_save_product';

	/**
	 * Delete product action and nonce name.
	 */
	public const DELETE_PRODUCT_NONCE = 'bpm_delete_product';

	/**
	 * BundleProductManager construct.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init hooks and actions.
	 *
	 * @return void
	 */
	private function init(): void {

		add_action( 'wp_ajax_' . self::SEARCH_PRODUCT_ACTION_NAME, [ $this, 'bpm_search_product_ajax_handler' ] );
		add_action(
			'wp_ajax_nopriv_' . self::SEARCH_PRODUCT_ACTION_NAME,
			[
				$this,
				'bpm_search_product_ajax_handler',
			]
		);

		add_action( 'wp_ajax_' . self::DELETE_PRODUCT_NONCE, [ $this, 'bpm_delete_product' ] );
		add_action(
			'wp_ajax_nopriv_' . self::DELETE_PRODUCT_NONCE,
			[
				$this,
				'bpm_delete_product',
			]
		);

		add_action( 'woocommerce_process_product_meta', [ $this, 'bpm_save_product_bundle' ] );
	}

	/**
	 * Search product ajax handler.
	 *
	 * @return void
	 */
	public function bpm_search_product_ajax_handler(): void {
		$nonce = ! empty( $_POST['nonce'] ) ? filter_var( wp_unslash( $_POST['nonce'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;

		if ( ! wp_verify_nonce( $nonce, self::SEARCH_PRODUCT_ACTION_NAME ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce code', 'bundle-product-manager' ) ] );
		}

		$string     = ! empty( $_POST['searchString'] ) ? filter_var( wp_unslash( $_POST['searchString'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		$product_id = ! empty( $_POST['productID'] ) ? filter_var( wp_unslash( $_POST['productID'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;

		if ( empty( $string ) ) {
			wp_send_json_error( [ 'message' => __( 'You sent an empty string', 'bundle-product-manager' ) ] );
		}

		$query_search = new WP_Query(
			[
				'post_type'    => 'product',
				'post_status'  => 'publish',
				's'            => $string,
				'post__not_in' => [ $product_id ],
			]
		);

		$products_list = [];

		if ( $query_search->have_posts() ) {
			while ( $query_search->have_posts() ) {
				$query_search->the_post();

				$product = wc_get_product( get_the_ID() );

				$products_list[] = [
					'id'    => $product->get_id(),
					'title' => $product->get_name(),
					'price' => $product->get_price_html(),
				];
			}

			wp_reset_postdata();
		} else {
			wp_send_json_error( [ 'message' => __( 'No results were found for your request', 'bundle-product-manager' ) ] );
		}

		wp_send_json_success( [ 'productsList' => $products_list ] );
	}

	/**
	 * Save product bundle.
	 *
	 * @param int $product_id Woocommerce product id.
	 *
	 * @return void
	 */
	public function bpm_save_product_bundle( int $product_id ): void {
		$nonce              = ! empty( $_POST[ self::SAVE_PRODUCT_NONCE ] ) ? filter_var( wp_unslash( $_POST[ self::SAVE_PRODUCT_NONCE ] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		$product_bundles_id = ! empty( $_POST['product_bundle_ids'] ) ? filter_var( wp_unslash( $_POST['product_bundle_ids'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;

		if ( ! wp_verify_nonce( $nonce, self::SAVE_PRODUCT_NONCE ) && empty( $product_bundles_id ) ) {
			return;
		}

		update_post_meta( $product_id, 'bpm_product_bundles_id', explode( ',', $product_bundles_id ) );
	}

	/**
	 * Delete ajax handler.
	 *
	 * @return void
	 */
	public function bpm_delete_product(): void {
		$nonce = ! empty( $_POST['nonce'] ) ? filter_var( wp_unslash( $_POST['nonce'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;

		if ( ! wp_verify_nonce( $nonce, self::DELETE_PRODUCT_NONCE ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce code', 'bundle-product-manager' ) ] );
		}

		$ids        = ! empty( $_POST['ids'] ) ? filter_var( wp_unslash( $_POST['ids'] ), FILTER_SANITIZE_FULL_SPECIAL_CHARS ) : null;
		$product_id = ! empty( $_POST['productID'] ) ? filter_var( wp_unslash( $_POST['productID'] ), FILTER_SANITIZE_NUMBER_INT ) : null;

		if ( empty( $product_id ) ) {
			wp_send_json_error( [ 'message' => __( 'Empty product id', 'bundle-product-manager' ) ] );
		}

		if ( empty( $ids ) ) {
			delete_post_meta( $product_id, 'bpm_product_bundles_id' );

			wp_send_json_success( [ 'message' => __( 'All product delete', 'bundle-product-manager' ) ] );
		}

		update_post_meta( $product_id, 'bpm_product_bundles_id', explode( ',', $ids ) );

		wp_send_json_success( [ 'message' => __( 'Products have been removed', 'bundle-product-manager' ) ] );
	}
}
