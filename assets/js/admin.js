(function($){
  $(document).on('click', '.ligo-qr-upload', function(e){
    e.preventDefault();
    var frame = wp.media({
      title: 'Seleccionar QR Ligo',
      button: { text: 'Usar esta imagen' },
      multiple: false
    });
    frame.on('select', function(){
      var attachment = frame.state().get('selection').first().toJSON();
      var $input = $('input[name="woocommerce_ligo_qr_woo_qr_image_url"]');
      if($input.length){
        $input.val(attachment.url).trigger('change');
      }
    });
    frame.open();
  });
})(jQuery);
