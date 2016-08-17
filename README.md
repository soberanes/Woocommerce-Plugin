# Woocommerce Plugin de Factura.com versión 1.6

Woocommerce es una herramienta muy sencilla e intuitiva que permite montar una tienda en línea con una amplia variedad de funcionalidades que junto con otros plugins complementan las operaciones básicas de ecommerce.
El plugin de Factura.com proporciona integración con la plataforma de Factura.com incluyendo las siguientes funciones:
- Reporte de Facturas enviadas y canceladas en el panel de administración.
- Enviar facturas por email a los clientes automáticamente y cancelar facturas desde
el panel de administración.
- Funcionalidad para que los clientes creen facturas directamente desde el área de
clientes.
- Reportes de historial de facturas y pedidos pendientes de facturar.

El plugin de Factura.com requiere Woocommerce versión 2.3 o superior para su correcto funcionamiento.

Si requieres más información acerca de la instalación y el uso de este módulo para Woocommerce, puedes contactarnos en https://factura.com/contacto o enviar un correo directamente a soporte@factura.com y con gusto te ayudaremos en lo que necesites.

CHANGELOG

Se corrigieron errores en el javascript, al momento de hacer llamadas a la API de Factura.com  y procesar las respuestas.
Se agregaron métodos de pago y en su caso correspondiente, ingresar los últimos 4 dígitos de la tarjeta o cuenta bancaria. Esta es una disposición directamente del SAT, por lo que la agregamos al módulo para cumplir con los lineamientos de facturación electrónica.
Se corrigió un error en el cálculo de totales, ya que anteriormente no tomaba en cuenta el IVA configurado en Woocommerce, tomando solo el precio del producto. Ahora el sistema detecta si hay IVA en los productos o en el envío, calculando el IVA correcto en la factura.
Se corrigieron algunos errores menores de diseño.
