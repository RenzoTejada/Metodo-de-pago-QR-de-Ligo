=== Método de pago QR de Ligo ===
Contributors: renzotejada, huakotech, alexistejada
Requires at least: 6.8
Tested up to: 7.1.4
Requires PHP: 7.4
Stable tag: 1.6.4
WC requires at least: 6.8
WC tested up to: 11.2.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Método de pago offline para WooCommerce que muestra un QR del Ligo y el nombre del titular.

== Descripción ==
- Permite seleccionar "QR Ligo" en el checkout.
- Muestra nombre del comercio y QR (subido desde ajustes) al seleccionar el método.
- En el pago, el pedido queda en espera hasta confirmar manualmente.
- Muestra bloque con QR en la página de Gracias y en emails.
- Compatible con HPOS: usa la API de pedidos.
- Integración con checkout clásico y Checkout Blocks.
- Muestra el QR configurado en la selección del método dentro de Checkout Blocks.

== Configuración ==
1. Ve a WooCommerce → Ajustes → Pagos → QR Ligo.
2. Activa el método y completa:
   - Nombre del titular.
   - Imagen QR (usa el botón para subir/seleccionar).
   - Instrucciones para el cliente.
3. Guarda los cambios.

== Changelog ==
= 1.6.4 =
* Mejora la UX/UI del encabezado del bloque QR en la página de pedido recibido.
* Reubica la identidad de Ligo en una banda superior compacta y mejor alineada, sin separar visualmente el contenido principal.
* Mantiene la compatibilidad con Checkout Blocks, checkout clásico y HPOS.

= 1.6.3 =
* Identificación visual de Ligo en el encabezado del bloque de instrucciones de pago de la página de pedido recibido.
* Reutiliza el icono incluido con el plugin, sin dependencias externas adicionales.
* Mantiene las opciones, el QR configurado, Checkout Blocks, checkout clásico y HPOS.
* Readme actualizado con WordPress 7.1.4 y WooCommerce 11.2.1 como versiones de referencia; pruebas funcionales completas pendientes.

= 1.6.2 =
* Rediseño responsive y aislado del bloque de QR en la página de pedido recibido.
* Muestra total y número de pedido, pasos de pago y aviso de confirmación manual.
* Conserva las opciones, el checkout clásico, Blocks y HPOS.

= 1.6.0 =
* Integración de QR Ligo con WooCommerce Checkout Blocks.
* Se conservan el ID del método y los ajustes de la versión 1.5.
* Declaración de compatibilidad con HPOS y Cart/Checkout Blocks.
* Metadatos preparados para WordPress 7.1.4 y WooCommerce 11.2.1 (validación funcional pendiente).
= 1.5 =
* Agregando colaboradores y assets de banner plugin
= 1.4 =
* Agregando colaboradores
= 1.3 =
* Add deploy with git actions.
= 1.2 =
* Corrigiendo lo solicitado por el equipo de Soporte de Automatic.
= 1.1 =
* Actualizando plugin name.
= 1.0 =
* Versión inicial.
