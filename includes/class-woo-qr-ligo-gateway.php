<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class QR_LIGO_Gateway extends WC_Payment_Gateway {
	
	public $instructions = '';
	public $merchant_name = '';
	public $qr_image_url = '';
	public $enable_thankyou = true;

	public function __construct() {
		$this->id                 = 'ligo_qr_woo';
		$this->icon               = QRLIGO_URL . 'assets/img/logo-ligo.png';
		$this->has_fields         = false;
		$this->method_title       = __( 'QR Ligo', 'metodo-de-pago-qr-de-ligo' );
		$this->method_description = __( 'Pago por QR Ligo. Muestra el QR y el nombre del titular. El pedido queda en espera hasta confirmar el pago.', 'metodo-de-pago-qr-de-ligo' );

		$this->supports = array( 'products' );

		$this->init_form_fields();
		$this->init_settings();

		$this->title            = 'QR Ligo';
		$this->description      = $this->get_option( 'description' );
		$this->instructions     = $this->get_option( 'instructions' );
		$this->merchant_name    = $this->get_option( 'merchant_name' );
		$this->qr_image_url     = $this->get_option( 'qr_image_url' );
		$this->enable_thankyou  = 'yes' === $this->get_option( 'enable_thankyou', 'yes' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 4 );
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Activar/Desactivar', 'metodo-de-pago-qr-de-ligo' ),
				'type'    => 'checkbox',
				'label'   => __( 'Activar QR Ligo', 'metodo-de-pago-qr-de-ligo' ),
				'default' => 'yes'
			),
			'description' => array(
				'title'       => __( 'Descripción', 'metodo-de-pago-qr-de-ligo' ),
				'type'        => 'textarea',
				'description' => __( 'Texto que verá el cliente al seleccionar este método en el checkout.', 'metodo-de-pago-qr-de-ligo' ),
				'default'     => __( 'Paga escaneando el QR de Ligo. El pedido se activará al confirmar tu pago.', 'metodo-de-pago-qr-de-ligo' ),
				'desc_tip'    => true,
			),
			'instructions' => array(
				'title'       => __( 'Instrucciones (emails y gracias)', 'metodo-de-pago-qr-de-ligo' ),
				'type'        => 'textarea',
				'description' => __( 'Se muestran en el correo de pedido y en la página de gracias.', 'metodo-de-pago-qr-de-ligo' ),
				'default'     => __( 'Escanea el QR con Ligo o app compatible. Incluye el número de pedido como referencia.', 'metodo-de-pago-qr-de-ligo' ),
				'desc_tip'    => true,
			),
			'merchant_name' => array(
				'title'       => __( 'Nombre del titular', 'metodo-de-pago-qr-de-ligo' ),
				'type'        => 'text',
				'description' => __( 'Nombre legal o comercial que se mostrará junto al QR.', 'metodo-de-pago-qr-de-ligo' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'qr_image_url' => array(
				'title'       => __( 'Imagen QR', 'metodo-de-pago-qr-de-ligo' ),
				'type'        => 'text',
				'description' => __( 'URL de la imagen del QR. Usa el botón "Subir/Seleccionar".', 'metodo-de-pago-qr-de-ligo' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'qr_image_button' => array(
				'title'       => __( 'Subir/Seleccionar QR', 'metodo-de-pago-qr-de-ligo' ),
				'type'        => 'title',
				'description' => '<button class="button ligo-qr-upload">' . esc_html__( 'Abrir biblioteca de medios', 'metodo-de-pago-qr-de-ligo' ) . '</button>',
			),
			'enable_thankyou' => array(
				'title'       => __( 'Mostrar en Gracias', 'metodo-de-pago-qr-de-ligo' ),
				'type'        => 'checkbox',
				'label'       => __( 'Mostrar el bloque de QR en la página de "Gracias por tu pedido"', 'metodo-de-pago-qr-de-ligo' ),
				'default'     => 'yes',
			),
		);
	}

    public function admin_options() {
        echo '<h2>' . esc_html( $this->get_method_title() ) . '</h2>';
        echo wp_kses_post( wpautop( esc_html( $this->get_method_description() ) ) );
        echo '<style>.form-table th {width: 260px;}</style>';
        echo '<table class="form-table">';
        $this->generate_settings_html( $this->form_fields );
        echo '</table>';
    }

    public function process_payment( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( $this->merchant_name ) {
            $order->update_meta_data( '_ligo_qr_merchant_name', sanitize_text_field( $this->merchant_name ) );
        }

        if ( $this->qr_image_url ) {
            $order->update_meta_data( '_ligo_qr_image_url', esc_url_raw( $this->qr_image_url ) );
        }

        /* translators: 1: nombre del método de pago (por ejemplo, "QR Ligo"). */
        $note = sprintf(
            __( 'Esperando pago por %1$s.', 'metodo-de-pago-qr-de-ligo' ),
            'QR Ligo'
        );

        $order->update_status(
            'on-hold',
            esc_html( $note )
        );

        wc_reduce_stock_levels( $order_id );

        if ( isset( WC()->cart ) ) {
            WC()->cart->empty_cart();
        }

        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order ),
        );
    }

    public function thankyou_page( $order_id ) {
        if ( ! $this->enable_thankyou ) { return; }
        $order = wc_get_order( $order_id );
        if ( ! $order ) { return; }

        $merchant = $order->get_meta( '_ligo_qr_merchant_name', true );
        $qr_url   = $order->get_meta( '_ligo_qr_image_url', true );

        if ( empty( $merchant ) ) { $merchant = $this->merchant_name; }
        if ( empty( $qr_url ) )   { $qr_url   = $this->qr_image_url; }

        echo '<section class="qrligo-thankyou" style="margin:1.25rem 0;padding:1rem;border:1px solid #e5e7eb;border-radius:10px;background:#f8fafc;">';
        echo '<h2 style="margin:0 0 .75rem 0;">' . esc_html__( 'Pago por QR Ligo', 'metodo-de-pago-qr-de-ligo' ) . '</h2>';

        if ( $merchant ) {
            echo '<p style="margin:.25rem 0;"><strong>' . esc_html__( 'titular:', 'metodo-de-pago-qr-de-ligo' ) . '</strong> ' . esc_html( $merchant ) . '</p>';
        }

        if ( $qr_url ) {
            echo '<div style="margin-top:.5rem;"><img src="' . esc_url( $qr_url ) . '" alt="' . esc_attr__( 'QR Ligo', 'metodo-de-pago-qr-de-ligo' ) . '" style="max-width:260px;height:auto;border-radius:6px;"/></div>';
        }

        if ( $this->instructions ) {
            echo '<p style="margin-top:.75rem;">' . wp_kses_post( wpautop( $this->instructions ) ) . '</p>';
        }

        echo '</section>';
    }

    public function email_instructions( $order, $sent_to_admin, $plain_text, $email ) {
        if ( ! $order || $order->get_payment_method() !== $this->id || $sent_to_admin ) {
            return;
        }

        $merchant = $order->get_meta( '_ligo_qr_merchant_name', true );
        $qr_url   = $order->get_meta( '_ligo_qr_image_url', true );

        if ( empty( $merchant ) ) { $merchant = $this->merchant_name; }
        if ( empty( $qr_url ) )   { $qr_url   = $this->qr_image_url; }

        if ( $plain_text ) {
            // Texto plano: sanitiza/escapa como texto.
            $merchant_safe = $merchant ? sanitize_text_field( $merchant ) : '';
            $qr_safe       = $qr_url   ? esc_url_raw( $qr_url )         : '';

            echo PHP_EOL . '--- ' . esc_html__( 'Pago por QR Ligo', 'metodo-de-pago-qr-de-ligo' ) . ' ---' . PHP_EOL;

            if ( $merchant_safe !== '' ) {
                echo esc_html__( 'titular:', 'metodo-de-pago-qr-de-ligo' ) . ' ' . esc_html( $merchant_safe ) . PHP_EOL;
            }
            if ( $qr_safe !== '' ) {
                echo esc_html__( 'QR:', 'metodo-de-pago-qr-de-ligo' ) . ' ' . esc_url( $qr_safe ) . PHP_EOL;
            }
            if ( $this->instructions ) {
                echo esc_html( wp_strip_all_tags( $this->instructions ) ) . PHP_EOL;
            }

            echo PHP_EOL;
        } else {
            // HTML: usa escapes adecuados por contexto.
            echo '<h2>' . esc_html__( 'Pago por QR Ligo', 'metodo-de-pago-qr-de-ligo' ) . '</h2>';

            if ( $merchant ) {
                echo '<p><strong>' . esc_html__( 'titular:', 'metodo-de-pago-qr-de-ligo' ) . '</strong> ' . esc_html( $merchant ) . '</p>';
            }
            if ( $qr_url ) {
                echo '<p><a href="' . esc_url( $qr_url ) . '">' . esc_html__( 'Ver QR', 'metodo-de-pago-qr-de-ligo' ) . '</a></p>';
            }
            if ( $this->instructions ) {
                echo wp_kses_post( wpautop( $this->instructions ) );
            }
        }
    }

}
