( function () {
    'use strict';
    if ( ! window.wc || ! window.wc.wcBlocksRegistry || ! window.wc.wcSettings || ! window.wp ) { return; }
    const settings = window.wc.wcSettings.getSetting( 'ligo_qr_woo_data', {} );
    const el = window.wp.element.createElement;
    const decode = window.wp.htmlEntities.decodeEntities;
    const label = decode( settings.title || 'QR Ligo' );
    const Content = () => el( 'div', { className: 'qrligo-blocks-info' },
        settings.description ? el( 'p', null, decode( settings.description ) ) : null,
        settings.merchant_name ? el( 'p', null, el( 'strong', null, 'Titular: ' ), settings.merchant_name ) : null,
        settings.qr_image_url ? el( 'img', {
            src: settings.qr_image_url, alt: 'QR Ligo',
            style: { width: '100%', maxWidth: '230px', height: 'auto', display: 'block', marginTop: '8px' }
        } ) : null,
        settings.instructions ? el( 'p', null, settings.instructions ) : null
    );
    window.wc.wcBlocksRegistry.registerPaymentMethod( {
        name: 'ligo_qr_woo',
        label: label,
        ariaLabel: label,
        content: el( Content ),
        edit: el( Content ),
        canMakePayment: () => true,
        supports: { features: settings.supports || [ 'products' ] }
    } );
} )();
