<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

/** Checkout Blocks adapter; delegates processing to the existing WC gateway. */
class QR_LIGO_Blocks_Integration extends AbstractPaymentMethodType {
    protected $name = 'ligo_qr_woo';
    // The parent class already declares protected $settings.

    public function initialize() {
        $this->settings = get_option( 'woocommerce_ligo_qr_woo_settings', array() );
    }

    public function is_active() {
        return 'yes' === ( $this->settings['enabled'] ?? 'no' );
    }

    public function get_payment_method_script_handles() {
        $handle = 'qrligo-checkout-blocks';
        wp_register_script(
            $handle,
            QRLIGO_URL . 'assets/js/checkout-blocks.js',
            array( 'wc-blocks-registry', 'wc-settings', 'wp-element', 'wp-html-entities' ),
            QRLIGO_VERSION,
            true
        );
        return array( $handle );
    }

    public function get_payment_method_data() {
        return array(
            'title'         => __( 'QR Ligo', 'metodo-de-pago-qr-de-ligo' ),
            'description'   => isset( $this->settings['description'] ) ? wp_strip_all_tags( $this->settings['description'] ) : '',
            'instructions'  => isset( $this->settings['instructions'] ) ? wp_strip_all_tags( $this->settings['instructions'] ) : '',
            'merchant_name' => isset( $this->settings['merchant_name'] ) ? sanitize_text_field( $this->settings['merchant_name'] ) : '',
            'qr_image_url'  => isset( $this->settings['qr_image_url'] ) ? esc_url_raw( $this->settings['qr_image_url'] ) : '',
            'supports'      => array( 'products' ),
        );
    }
}
