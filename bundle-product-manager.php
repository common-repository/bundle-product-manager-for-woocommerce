<?php
/**
 * Bundle Product Manager
 *
 * @package           iwpdev/bundle-product-manager
 * @author            iwpdev
 * @license           GPL-2.0-or-later
 * @wordpress-plugin
 *
 * Plugin Name: Bundle Product Manager for WooCommerce.
 * Plugin URI: https://i-wp-dev.com
 * Description: Our WordPress WooCommerce plugin provides unique functionality by allowing you to easily add multiple
 * additional products to your main product before checkout. This is a convenient solution for customers who want to
 * collect complex sets of products and easily customize their orders. Improve the customer experience and increase
 * sales with our plugin.
 *
 * Version: 1.0.9
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Alex Lavyhin
 * Author URI: https://profiles.wordpress.org/alexlavigin/
 * License: GPL2
 *
 * Text Domain: bundle-product-manager
 * Domain Path: /languages
 */

use BundleProductManager\Admin\Notification\BPM_Notification;
use BundleProductManager\BPM_Main;

if ( ! defined( 'ABSPATH' ) ) {
	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

/**
 * Plugin version.
 */
const BPM_VERSION = '1.0.8';

/**
 * Plugin path.
 */
const BPM_PATH = __DIR__;

/**
 * Plugin main file
 */
const BPM_FILE = __FILE__;

/**
 * Class autoload.
 */
require_once BPM_PATH . '/vendor/autoload.php';

/**
 * Min ver php.
 */
const BPM_PHP_REQUIRED_VERSION = '7.4';

/**
 * Plugin url.
 */
define( 'BPMF_URL', untrailingslashit( plugin_dir_url( BPM_FILE ) ) );

/**
 * Access to the is_plugin_active function
 */
require_once ABSPATH . 'wp-admin/includes/plugin.php';

if ( ! function_exists( 'bpmf_is_php_version' ) ) {

	/**
	 * Check php version.
	 *
	 * @return bool
	 */
	function bpmf_is_php_version(): bool {
		if ( version_compare( PHP_VERSION, BPM_PHP_REQUIRED_VERSION, '<' ) ) {
			return false;
		}

		return true;
	}
}

if ( ! bpmf_is_php_version() ) {

	add_action(
		'admin_notices',
		[
			BPM_Notification::class,
			'bpm_php_version_nope',
		]
	);

	if ( is_plugin_active( plugin_basename( BPM_FILE ) ) ) {
		deactivate_plugins( plugin_basename( BPM_FILE ) );
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

	return;
}

if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	add_action(
		'admin_notices',
		[
			BPM_Notification::class,
			'woocommerce_no_active',
		]
	);
	if ( is_plugin_active( plugin_basename( BPM_FILE ) ) ) {
		deactivate_plugins( plugin_basename( BPM_FILE ) );
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

	return;
}

load_plugin_textdomain( 'bundle-product-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

new BPM_Main();
