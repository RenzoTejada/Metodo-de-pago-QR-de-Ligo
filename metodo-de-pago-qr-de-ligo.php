<?php
/**
 * Plugin Name:       Método de pago QR de Ligo
 * Description:       Método de pago offline para WooCommerce que muestra un QR del Ligo y el nombre del titular.
 * Author: Renzo Tejada
 * Author URI: https://renzotejada.com/
 * Version:           1.6.4
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       metodo-de-pago-qr-de-ligo
 * Domain Path: /languages
 * Tested up to:      7.1.4
 * WC tested up to: 11.2.1
 * WC requires at least: 6.8
 *
 * @package QR_LIGO
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'QRLIGO_VERSION', '1.6.4' );
define( 'QRLIGO_FILE', __FILE__ );
define( 'QRLIGO_DIR', plugin_dir_path( __FILE__ ) );
define( 'QRLIGO_URL', plugin_dir_url( __FILE__ ) );

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			__FILE__,
			true
		);
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'cart_checkout_blocks', __FILE__, true
        );
	}
});

add_filter( 'woocommerce_payment_gateways', function( $methods ) {
	require_once QRLIGO_DIR . 'includes/class-woo-qr-ligo-gateway.php';
	$methods[] = 'QR_LIGO_Gateway';
	return $methods;
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {

    // Salimos si no estamos en WooCommerce → Ajustes.
    if ( 'woocommerce_page_wc-settings' !== $hook ) {
        return;
    }

    // Doble verificación con la pantalla actual (evita usar $_GET).
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'woocommerce_page_wc-settings' !== $screen->id ) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script(
        'woo-qr-ligo-admin',
        QRLIGO_URL . 'assets/js/admin.js',
        array( 'jquery' ),
        QRLIGO_VERSION,
        true
    );
});
/** Register QR Ligo in the WooCommerce Checkout block. */
add_action( 'woocommerce_blocks_loaded', function () {
    if ( ! class_exists( '\Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
        return;
    }
    require_once QRLIGO_DIR . 'includes/class-woo-qr-ligo-blocks.php';
    add_action( 'woocommerce_blocks_payment_method_type_registration', function ( $registry ) {
        $registry->register( new QR_LIGO_Blocks_Integration() );
    } );
} );

/** Load styles only on the order-received page; do not affect the store theme. */
add_action( 'wp_enqueue_scripts', function () {
    if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
        wp_enqueue_style( 'qrligo-thankyou', QRLIGO_URL . 'assets/css/thankyou.css', array(), QRLIGO_VERSION );
    }
} );
