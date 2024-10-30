<?php
/**
 * Main class plugin.
 *
 * @package iwpdev/bundle-product-manager
 */

namespace BundleProductManager;

/**
 * Main class file.
 */
class BPM_Main {

	/**
	 * Main construct.
	 */
	public function __construct() {
		$this->bpm_init();

		new BPM_Handlers();
		new BPM_Output();
	}

	/**
	 * Init hooks and actions.
	 *
	 * @return void
	 */
	private function bpm_init(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'bpm_add_style_and_script_admin_panel' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'bpm_add_style_and_script' ] );
	}

	/**
	 * Add style and script admin panel.
	 *
	 * @return void
	 */
	public function bpm_add_style_and_script_admin_panel(): void {
		$url = BPMF_URL;
		$min = '.min';

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$min = '';
		}

		wp_enqueue_script( 'bpm_admin_main', $url . '/assets/js/admin-main' . $min . '.js', [ 'jquery' ], BPM_VERSION, true );
		wp_enqueue_style( 'bpm_admin_main', $url . '/assets/css/admin-main' . $min . '.css', '', BPM_VERSION );

		wp_localize_script(
			'bpm_admin_main',
			'bpmAdminObject',
			[
				'ajaxUrl'             => admin_url( 'admin-ajax.php' ),
				'searchProductAction' => BPM_Handlers::SEARCH_PRODUCT_ACTION_NAME,
				'searchProductNonce'  => wp_create_nonce( BPM_Handlers::SEARCH_PRODUCT_ACTION_NAME ),
				'deleteProductAction' => BPM_Handlers::DELETE_PRODUCT_NONCE,
				'deleteProductNonce'  => wp_create_nonce( BPM_Handlers::DELETE_PRODUCT_NONCE ),
			]
		);
	}

	/**
	 * Add front-end style and scripts.
	 *
	 * @return void
	 */
	public function bpm_add_style_and_script(): void {

		if ( is_product() ) {
			$url = BPMF_URL;
			$min = '.min';

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$min = '';
			}

			wp_enqueue_script( 'bpm_main', $url . '/assets/js/main' . $min . '.js', [], BPM_VERSION, true );

			wp_enqueue_style( 'bpm_main', $url . '/assets/css/main' . $min . '.css', '', BPM_VERSION );
		}
	}
}
