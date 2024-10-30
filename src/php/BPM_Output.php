<?php
/**
 * Output front-end and admin.
 *
 * @package iwpdev/bundle-product-manager
 */

namespace BundleProductManager;

use WP_Query;

/**
 * BPM_Output class file.
 */
class BPM_Output {

	/**
	 * BPM_Output construct
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
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'bpm_add_product_bundle_tab' ] );
		add_action( 'woocommerce_product_data_panels', [ $this, 'bpm_add_product_bundle_tab_fields' ] );
		add_action( 'woocommerce_after_add_to_cart_form', [ $this, 'bpm_output_product_bundle' ], 30 );
	}

	/**
	 * Add product bundle tab.
	 *
	 * @param array $tabs Product tabs.
	 *
	 * @return array
	 */
	public function bpm_add_product_bundle_tab( array $tabs ): array {

		$tabs['product_bundle'] = [
			'label'  => __( 'Product bundle', 'bundle-product-manager' ),
			'target' => 'product_bundle_data_tab',
			'class'  => [ 'show_if_simple', 'show_if_variable' ],
		];

		return $tabs;
	}

	/**
	 * Add product bundle tab fields.
	 *
	 * @return void
	 */
	public function bpm_add_product_bundle_tab_fields(): void {
		global $post;

		$product_bundle = get_post_meta( $post->ID, 'bpm_product_bundles_id', true ) ?? [];
		?>
		<div id="product_bundle_data_tab" class="panel woocommerce_options_panel">
			<div class="options_group">
				<?php
				woocommerce_wp_text_input(
					[
						'id'          => 'product_bundle_data',
						'label'       => __( 'Select product', 'bundle-product-manager' ),
						'desc_tip'    => 'true',
						'description' => __( 'Type any keyword to search', 'bundle-product-manager' ),
					]
				);
				wp_nonce_field( BPM_Handlers::SAVE_PRODUCT_NONCE, BPM_Handlers::SAVE_PRODUCT_NONCE );
				?>
				<div class="preload">
					<img
							src="<?php echo esc_url( BPMF_URL . '/assets/img/spinner.gif' ); ?>"
							alt="Preloader">
				</div>
				<div class="result-search">

				</div>
				<div class="selected-products">
					<?php
					if ( ! empty( $product_bundle ) ) {
						$arg = [
							'post_type'   => 'product',
							'post_status' => 'publish',
							'post__in'    => $product_bundle,
						];

						$query_search = new WP_Query( $arg );
						while ( $query_search->have_posts() ) {
							$query_search->the_post();

							$product = wc_get_product( get_the_ID() );
							?>
							<div>
								<a
										href="#"
										class="add_product selected-product"
										data-product_id="<?php echo esc_attr( $product->get_id() ); ?>">
									<span class="title">
										<?php echo esc_html( $product->get_name() ); ?>
									</span>
									<div class="product-price">
										<?php echo wp_kses_post( $product->get_price_html() ); ?>
									</div>
									<span class="remove-button dashicons dashicons-trash"></span>
								</a>
							</div>
							<?php
						}
						wp_reset_postdata();
					}
					?>
				</div>
				<input
						type="hidden"
						name="product_bundle_ids"
						value="<?php echo esc_attr( ! empty( $product_bundle ) ? implode( ',', $product_bundle ) : '' ); ?>">
			</div>
		</div>
		<?php
	}

	/**
	 * Output product bundle.
	 *
	 * @return void
	 */
	public function bpm_output_product_bundle(): void {
		global $post;

		$product_bundle = get_post_meta( $post->ID, 'bpm_product_bundles_id', true );
		if ( ! empty( $product_bundle ) ) {

			$query_search = new WP_Query(
				[
					'post_type' => 'product',
					'post__in'  => $product_bundle,
				]
			);
			?>
			<div class="product-bundle-block">
				<?php
				while ( $query_search->have_posts() ) {
					$query_search->the_post();

					$product = wc_get_product( get_the_ID() );
					?>
					<div id="product-<?php echo esc_attr( $product->get_id() ); ?>"
						<?php post_class(); ?>>
						<div class="product-content">
							<?php if ( has_post_thumbnail( $product->get_id() ) ) { ?>
								<div class="product-thumb">
									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail( 'thumbnail' ); ?>
									</a>
								</div>
							<?php } ?>
							<div class="product-info">
								<div class="product-title" for="product-16536">
									<a href="<?php the_permalink(); ?>">
										<?php the_title(); ?>
									</a>
								</div>
								<?php echo wp_kses_post( $product->get_price_html() ); ?>
							</div>
						</div>
						<?php
						if ( $product->get_type() !== 'variable' && ! $product->is_sold_individually() ) {
							echo '<div class="add-to-cart-product-bundle" >';
							// @codingStandardsIgnoreLine
							self::bpm_get_cart_form_template( 'simple' );
							echo '</div>';
						}

						if ( 'variable' === $product->get_type() ) {
							echo '<div class="add-to-cart-product-bundle" >';
							echo '<a class="button product_type_variable" href="' . esc_url( get_the_permalink( $product->get_id() ) ) . '">' . esc_html( __( 'Choose a variation', 'bundle-product-manager' ) ) . '</a>';
							echo '</div>';
						}
						?>
					</div>
					<?php
				}
				wp_reset_postdata();
				?>
			</div>
			<?php
		}
	}

	/**
	 * Get cart form template.
	 *
	 * @param string $template_path Template path.
	 *
	 * @return void
	 */
	public function bpm_get_cart_form_template( string $template_path ): void {
		if ( locate_template( $template_path . '.php' ) !== '' ) {
			include get_stylesheet_directory() . '/bpm_template/simple.php';
		}

		include BPM_PATH . '/template/simple.php';
	}
}
