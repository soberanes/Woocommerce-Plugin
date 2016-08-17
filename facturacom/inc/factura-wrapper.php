<?php
require_once 'api-helper.php';
require_once 'commerce-helper.php';
require_once 'api-client.php';
require_once 'factura-config.php';

class FacturaWrapper{
    private static $error = array();
    private static $messages = array();

    /**
     * Getting wordpress shortcode
     *
     * @return String
     */
    static function form_shortcode(){
        // ApiHelper::deleteCookie('customer');
        // ApiHelper::deleteCookie('order');
        //
        $configEntity = FacturaWrapper::getConfigEntity();
        $form = '
            <div id="facturacion_wrapper">
                <div class="full-width">
                    <div id="primary" class="content-area">
                        <div id="content" class="site-content" role="main">
                            <div class="home_product">
                                <div class="steps-container">';
                                    $form.='<div class="f-welcome-container">';
                                        if(!empty($configEntity['title'])):
                                            $form.='<h1 class="f-page-title">'.$configEntity['title'].'</h1>';
                                        endif;
                                        if(!empty($configEntity['description'])):
                                            $form.='<p class="f-page-subtitle">'.$configEntity['description'].'</p>';
                                        endif;

                                    $form.='</div>';
                                    $form.='<!-- step one starts -->
                                    <div id="step-one" class="step-block">
                                        <div class="step-header" style="background:'.$configEntity['colorheader'].';">
                                            <h1 style="color:'.$configEntity['colorfont'].';">
                                                <span>Paso 1/4</span>
                                                Identificar pedido
                                            </h1>
                                        </div>
                                        <div class="step-content">
                                            <p class="step-instruction">Ingresa tu RFC, n&uacute;mero de pedido y correo electr&uacute;nico para buscar tu pedido.</p>
                                            <form name="f-step-one-form" id="f-step-one-form" action="<?php echo get_permalink(); ?>" method="post">
                                                <input type="hidden" name="csrf" value="" />
                                                <label for="f-rfc" >RFC*</label>
                                                <input type="text" class="input-upper f-input" id="f-rfc" name="rfc" value="" placeholder="12 o 13 dígitos" />
                                                <label for="f-num-order" >N&uacute;m de pedido*</label>
                                                <input type="text" class="f-input" id="f-num-order" name="order" value="" placeholder="Sin signo  #"  />
                                                <label for="f-email" >Correo electr&oacute;nico*</label>
                                                <input type="email" class="f-input" id="f-email" name="email" value="" placeholder="El correo registrado en el pedido"  />
                                                <div class="buttons-right">
                                                    <input type="submit" class="f-submit" id="step-one-button-next" name="f-submit" value="siguiente" />
                                                </div>
                                                <div class="error_msj"></div>
                                                <div class="clearfix"></div>
                                            </form>
                                        </div>
                                        <div class="loader_content">
                                            <div class="loader">Cargando...</div>
                                        </div>
                                        <div class="remodal" data-remodal-id="respuesta-paso-uno">
                                            <button data-remodal-action="close" class="remodal-close"></button>
                                            <h1 id="message-response-one"> </h1>
                                            <br>
                                            <button data-remodal-action="confirm" class="remodal-confirm">Aceptar</button>
                                        </div>
                                    </div>
                                    <!-- step one ends -->
                                    <!-- step two starts -->
                                    <div id="step-two" class="step-block">
                                        <div class="step-header" style="background:'.$configEntity['colorheader'].';">
                                            <h1 style="color:'.$configEntity['colorfont'].';">
                                            <span>Paso 2/4</span>
                                            Datos de facturaci&oacute;n
                                          </h1>
                                        </div>
                                        <div class="step-content">
                                          <p class="step-instruction"></p>
                                          <form name="f-step-two-form" id="f-step-two-form" action="<?php echo get_permalink(); ?>" method="post">
                                            <input type="hidden" name="csrf" value="" />
                                            <input type="hidden" id="apimethod" name="apimethod" value="create" />
                                            <input type="hidden" id="uid" name="uid" value="" />
                                            <h3>Datos de contacto</h3>
                                            <div class="input-group">
                                              <label for="general-nombre">Nombre</label>
                                              <input type="text" class="input-cap f-input f-top" id="general-nombre" name="general-nombre" value="" placeholder="" readonly />
                                            </div>
                                            <div class="input-group">
                                              <label for="general-apellidos">Apellidos</label>
                                              <input type="text" class="input-cap f-input f-top" id="general-apellidos" name="general-apellidos" value="" placeholder="" readonly />
                                            </div>
                                            <div class="input-group">
                                              <label for="general-email">Correo electr&oacute;nico</label>
                                              <input type="email" class="f-input f-top" id="general-email" name="general-email" value="" placeholder="Email para envío de CFDI" readonly />
                                            </div>
                                            <div class="input-group">
                                              <label for="fiscal-telefono">Tel&eacute;fono</label>
                                              <input type="text" class="input-cap f-input f-no-top f-right f-bottom" id="fiscal-telefono" name="fiscal-telefono" value="" placeholder="10 digitos" readonly />
                                            </div>
                                            <h3>Datos fiscales</h3>
                                            <div class="input-group">
                                              <label for="fiscal-nombre">Razón Social</label>
                                              <input type="text" class="input-cap f-input f-top" id="fiscal-nombre" name="fiscal-nombre" value="" placeholder="" readonly />
                                            </div>
                                            <div class="input-group">
                                              <label for="fiscal-rfc">RFC</label>
                                              <input type="text" class="input-upper f-input f-top" id="fiscal-rfc" name="fiscal-rfc" value="" placeholder="12 o 13 dígitos" readonly />
                                            </div>
                                            <div class="input-group">
                                              <label for="fiscal-calle">Calle</label>
                                              <input type="text" class="input-cap f-input f-no-top" id="fiscal-calle" name="fiscal-calle" value="" placeholder="" readonly />
                                            </div>
                                            <div class="input-group float-left">
                                              <label for="fiscal-exterior">N&uacute;mero exterior</label>
                                              <input type="text" class="input-cap f-input f-right f-no-top" id="fiscal-exterior" name="fiscal-exterior" value="" placeholder="" readonly />
                                            </div>
                                            <div class="input-group float-left" style="margin-left: 7px;">
                                              <label for="fiscal-interior">N&uacute;mero interior</label>
                                              <input type="text" class="input-cap f-input" id="fiscal-interior" name="fiscal-interior" value="" placeholder="" readonly />
                                            </div>
                                            <div class="input-group">
                                              <label for="fiscal-colonia">Colonia</label>
                                              <input type="text" class="input-cap f-input f-right" id="fiscal-colonia" name="fiscal-colonia" value="" placeholder="" readonly />
                                            </div>
                                            <div class="input-group">
                                              <label for="fiscal-municipio">Delegaci&oacute;n o Municipio</label>
                                              <input type="text" class="input-cap f-input f-no-top f-right" id="fiscal-municipio" name="fiscal-municipio" value="" placeholder="" readonly />
                                            </div>
                                            <div class="input-group">
                                              <label for="fiscal-estado">Estado</label>
                                              <input type="text" class="input-cap f-input" id="fiscal-estado" name="fiscal-estado" value="" placeholder="" readonly />
                                            </div>
                                            <div class="input-group">
                                              <label for="fiscal-pais">Pa&iacute;s</label>
                                              <input type="text" class="input-cap f-input f-right" id="fiscal-pais" name="fiscal-pais" value="México" placeholder="" readonly />
                                            </div>
                                            <div class="input-group">
                                              <label for="fiscal-cp">C&oacute;digo Postal</label>
                                              <input type="text" class="input-cap f-input f-no-top f-bottom" id="fiscal-cp" name="fiscal-cp" value="" placeholder="" readonly />
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="buttons-right">
                                              <input type="button" class="f-submit f-back" id="step-two-button-back" name="f-back" value="Volver" data-f="2" />
                                              <input type="button" class="f-submit f-edit" id="step-two-button-edit" name="f-edit" value="Editar" data-b="1" />
                                              <input type="submit" class="f-submit" id="step-two-button-next" name="f-submit" value="siguiente" />
                                            </div>
                                            <div class="f-loading">Cargando...</div>
                                            <div class="error_msj"></div>
                                            <div class="clearfix"></div>
                                          </form>
                                        </div>
                                        <div class="loader_content">
                                          <div class="loader">Cargando...</div>
                                        </div>
                                        <div class="remodal" data-remodal-id="respuesta-paso-dos">
                                            <button data-remodal-action="close" class="remodal-close"></button>
                                            <h1 id="message-response-dos"> </h1>
                                            <br>
                                            <button data-remodal-action="cancel" class="remodal-confirm">Aceptar</button>
                                        </div>
                                      </div>
                                    <!-- step two ends -->
                                    <!-- step three starts -->
                                    <div id="step-three" class="step-block step-invoice">
                                        <div class="step-header" style="background:'.$configEntity['colorheader'].';">
                                            <h1 style="color:'.$configEntity['colorfont'].';">
                                            <span>Paso 3/4</span>
                                            Verificar datos de pedido
                                          </h1>
                                        </div>
                                        <div class="step-content">
                                          <h3 class="invoice-title"> <span id="invoice-id">3526321</span></h3>
                                          <h3 class="invoice-title"> <span id="invoice-date">30/06/2015</span></h3>
                                          <div class="invoice-sections">

                                            <div class="invoice-emisor">
                                              <h3 class="invoice-header">Emisor</h3>
                                              <span id="emisor-nombre" class="ref-data"></span>
                                              <span id="emisor-rfc" class="ref-data"></span>
                                              <span id="emisor-direccion" class="ref-data"></span>
                                              <span id="emisor-direccion-zone" class="ref-data"></span>
                                              <span id="emisor-direccion-zone-city" class="ref-data"></span>
                                              <span id="emisor-telefono" class="ref-data"></span>
                                              <span id="emisor-email" class="ref-data"></span>
                                            </div>

                                            <div class="invoice-receptor">
                                              <h3 class="invoice-header">Receptor</h3>
                                              <span id="receptor-nombre" class="ref-data"></span>
                                              <span id="receptor-rfc" class="ref-data"></span>
                                              <span id="receptor-direccion" class="ref-data"></span>
                                              <span id="receptor-direccion-zone" class="ref-data"></span>
                                              <span id="receptor-direccion-zone-city" class="ref-data"></span>
                                              <span id="receptor-email" class="ref-data"></span>
                                            </div>

                                          <div class="invoice-details">
                                            <h3 class="invoice-header">Detalle del pedido</h3>
                                            <table id="table-details">
                                              <thead>
                                                <tr>
                                                  <td>Producto</td>
                                                  <td>Cantidad</td>
                                                  <td>Precio unitario</td>
                                                  <td>Total</td>
                                                </tr>
                                              </thead>
                                              <tbody id="datails-body">

                                              </tbody>
                                            </table>
                                          </div>

                                          <div class="invoice-payment">
                                            <h3 class="invoice-header">M&eacute;todo de pago</h3>
                                            <p id="invoice-pmethod">
                                              Selecciona el m&eacute;todo de pago
                                              <form id="payment-method-form">
                                                <div class="input-group">
                                                  <label for="select-payment">* M&eacute;todo</label>
                                                  <select id="select-payment" class="input-cap f-input f-select">
                                                      <option value="01">Efectivo</option>
                                                      <option value="02">Cheque nominativo</option>
                                                      <option value="03">Transferencia electrónica de fondos</option>
                                                      <option value="04">Tarjeta de crédito</option>
                                                      <option value="05">Monedero electrónico</option>
                                                      <option value="06">Dinero electrónico</option>
                                                      <option value="08">Vales de despensa</option>
                                                      <option value="28">Tarjeta de débito</option>
                                                      <option value="29">Tarjeta de servicio</option>
                                                      <option value="99">Otros</option>
                                                  </select>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div id="num-cta-box" class="input-group">
                                                  <label for="f-num-cta" style="width: 285px;">&Uacute;ltimos 4 dígitos de tu cuenta o tarjeta</label>
                                                  <input type="text" class="input-cap f-input f-no-top f-bottom f-digits" id="f-num-cta" name="f-num-cta" value="" placeholder="####" min="4" max="4"/>
                                                </div>
                                                <div class="clearfix"></div>
                                              </form>
                                            </p>
                                          </div>

                                          <div class="invoice-totals">
                                            <table>
                                              <tr>
                                                  <td>Subtotal</td>
                                                  <td><span id="invoice-subtotal"></span></td>
                                              </tr>
                                              <tr id="td-discount">
                                                  <td>Descuento</td>
                                                  <td><span id="invoice-discount"></span></td>
                                              </tr>
                                              <tr>
                                                  <td>IVA</td>
                                                  <td><span id="invoice-iva"></span></td>
                                              </tr>
                                              <tr>
                                                  <td>Total</td>
                                                  <td><span id="invoice-total"></span></td>
                                              </tr>
                                            </table>
                                          </div>
                                            <div class="clearfix"></div>
                                            <p class="f-page-subtitle">
                                                Antes de generar la factura, por favor confirme que los datos estén correctamente. <em>Ya que una vez emitida o generada la factura no podrá realizar cambios a la misma</em>. Agradecemos su preferencia.
                                            </p>
                                            <div class="clearfix"></div>
                                            <div class="buttons-right">
                                              <input type="button" class="f-submit f-back" id="step-three-button-back" name="f-back" value="Volver" data-f="3" />
                                              <input type="button" class="btn-success" style="background: #67BA2F !important;border: #67BA2F !important;padding: 13px 20px;margin: 0;margin-left: 15px;font-weight: bold;" id="step-three-button-next" name="f-submit" value="Generar factura" />
                                            </div>
                                            <div class="clearfix"></div>
                                          </div>
                                        </div>
                                        <div class="loader_content">
                                          <div class="loader">Cargando...</div>
                                        </div>
                                      </div>
                                    <!-- step three ends -->
                                    <!-- step four starts -->
                                    <div id="step-four" class="step-block step-invoice">
                                        <div class="step-header">
                                          <h1>
                                            <span>Paso 4/4</span>
                                            Resultado de facturaci&oacute;n
                                          </h1>
                                        </div>
                                        <div class="step-content">
                                          <div class="buttons_container">
                                            <h1 id="result-msg-title">La factura ha sido creada y enviada con &eacute;xito.</h1>
                                            <div class="clearfix"></div>
                                            <h4 id="result-email-msg"></h4>
                                            <h4 id="result-msg"></h4>
                                            <a href="#" id="btn-success-email" class="btn-success invoice-button invoice-pdf" target="_blank">Enviar por correo electr&oacute;nico</a>
                                            <a href="#" id="btn-success-pdf" class="btn-success invoice-button invoice-pdf" target="_blank">Descargar PDF</a>
                                            <a href="#" id="btn-success-xml" class="btn-success invoice-button invoice-xml" target="_blank">Descargar XML</a>
                                          </div>
                                          <div id="out-message">
                                            <h3>Ya puedes cerrar &eacute;sta p&aacute;gina o <a href="'.get_site_url().'">seguir navegando</a>.</h3>
                                          </div>
                                        </div>
                                      </div>
                                      <div id="out-message">

                                      </div>
                                    </div>
                                    <!-- step four ends -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>';

        return $form;

    }

    /**
     * Saving configuration in .conf file
     *
     * @return Boolean
     */
    static function saveSettings($settings){

        return FacturaConfig::saveConf($settings);

    }

    /**
     * Getting settings entity
     *
     * @return Array
     */
    static function getConfigEntity(){
        return FacturaConfig::configEntity();
    }

    /**
     * Get Factura.com invoices via API
     *
     * @return Object
     */
    static function getInvoices(){

        $configEntity = self::getConfigEntity();
        $url     = $configEntity['apiurl'] . 'invoices';
        $request = 'GET';

        return WrapperApi::callCurl($url, $request);
    }

    /**
     * Send invoice to customer via email
     *
     * @param Int $uid
     * @return Object
     */
    static function sendInvoiceEmail($uid){
        if(!isset($uid)){
            return array(
                'Error' => 'No se ha recibido el id de la factura.',
            );
        }

        $configEntity = self::getConfigEntity();

        $url     = $configEntity['apiurl'] . 'invoice/' . $uid . '/email';
        $request = 'GET';

        return WrapperApi::callCurl($url, $request);
    }

    /**
     * Cancel invoice in Factura.com system
     *
     * @param Int $uid
     * @return Object
     */
    static function cancelInvoice($uid){
        if(!isset($uid)){
            return array(
                'Error' => 'No se ha recibido el id de la factura.',
            );
        }

        $configEntity = self::getConfigEntity();

        $url     = $configEntity['apiurl'] . 'invoice/' . $uid . '/cancel';
        $request = 'GET';

        return WrapperApi::callCurl($url, $request);
    }

    /**
     * Get invoice by customer's RFC and order num
     *
     * @param String $rfc
     * @return Object
     */
    static function getInvoiceByOrder($rfc, $orderId){
        if(!isset($rfc)){
            return null;
        }

        if(!isset($orderId)){
            return null;
        }

        $configEntity = self::getConfigEntity();

        $url = $configEntity['apiurl'] . 'invoices?rfc=' . $rfc . '&num_order=' . $orderId;
        $request = 'GET';

        $invoideData = WrapperApi::callCurl($url, $request)->data;
        if(count($invoideData) == 1){
            return $invoideData[0]->UID;
        }else{
            return null;
        }

    }

    /**
     * Get customer data from Factura.com system
     *
     * @param String $rfc
     * @return Object
     */
    static function getCustomer($rfc){
        if(!isset($rfc)){
            return array(
                'Error' => 'No se ha recibido el rfc del cliente.',
            );
        }

        $configEntity = self::getConfigEntity();

        $url = $configEntity['apiurl'] . 'clients/' . $rfc;
        $request = 'GET';

        return WrapperApi::callCurl($url, $request);
    }

    /**
     * Get order data from Woocommerce system
     *
     * @param Int $orderId
     * @return Array
     */
    static function getOrder($orderId){
        if(!isset($orderId)){
            return array(
                'Error' => 'No se ha recibido el id del pedido.',
            );
        }

        $order = CommerceHelper::getOrderById($orderId);

        return $order;
    }

    /**
     * Validate order
     *
     * @param Object $order
     * @param String $email
     * @param String $rfc
     * @return Array
     */
    static function validateOrder($order, $email, $rfc){
        //validate order is set
        if(!isset($order)){
            return array(
                'Error' => true,
                'Message' => 'No se ha recibido el pedido.',
            );
        }

        //validate email given is set
        if(!isset($email)){
            return array(
                'Error' => true,
                'Message' => 'No se ha recibido el email del cliente.',
            );
        }

        //validate rfc given is set
        if(!isset($rfc)){
            return array(
                'Error' => true,
                'Message' => 'No se ha recibido el rfc del cliente.',
            );
        }

        //validate email given is of the order given
        if($order->billing_email != $email){
            return array(
                'Error' => true,
                'Message' => 'El email ingresado no corresponde al email del pedido',
            );
        }

        //validate invoiced status
        if($order->status == "invoiced"){
            return array(
                'Error' => true,
                'Message' => 'Este pedido se encuentra facturado.',
                'Meta' => array(
                    'code' => 101,
                    'uid' => self::getInvoiceByOrder($rfc, $order->id),
                )
            );
        }

        //validate order completed status
        if($order->status != 'completed'){
            return array(
                'Error' => true,
                'Message' => 'Este pedido no se encuentra completado.',
            );
        }

        //validate order and dayoff to invoice
        if(self::validateDayOff($order->completed_at) == false){
            return array(
                'Error' => true,
                'Message' => 'Este pedido se encuentra fuera del periodo para facturación y ya no se puede facturar.',
            );
        }

        return true;

    }

    /**
     * Validate invoicing day off
     *
     * @param String $completed_date
     * @return Boolean
     */
    static function validateDayOff($completed_date){
        $order_month = date("m",strtotime($completed_date));
        $current_month = date("m");
        $current_day = date("d");

        $configEntity = self::getConfigEntity();

        if($order_month != $current_month){
            $order_day = date("d",strtotime($completed_date));

            if($order_month < $current_month){
                if($current_day > $configEntity['dayoff']){
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Create cookies by name
     *
     * @param Object $customer
     * @param Object $order
     * @return void
     */
    static function saveCookies($name, $value){

        if(isset($value)){
            ApiHelper::saveCookie($name, $value);
        }

    }

    /**
     * Get cookies variables by name
     *
     * @param String $name
     */
    static function getCookies($name){
        $cookie = ApiHelper::getCookie($name);
        return $cookie;
    }

    /**
     * Delete cookies variables by name
     *
     * @param String $name
     */
    static function deleteCookies($name){
        ApiHelper::deleteCookies($name);
    }

    /*
	 * Create invoice in factura.com system
	 *
	 * @param Array $data customer's data to save in factura.com system
	 * @return Array
	 *
	 */
    static function generateInvoice($payment_data){
        $configEntity = self::getConfigEntity();
        $url = $configEntity['apiurl'] . "invoice/create";
        $request = 'POST';

        $order = FacturaWrapper::getCookies('order');
        $customer = $_SESSION['customer'];

        $items = array();

        foreach($order->line_items as $item){
            $unidad = ($item["product_id"] == 31) ? "Servicio" : "Producto";
            // if(CommerceHelper::includeTax()){
            //     $product_price = ($item["subtotal"]/$item["quantity"]) / 1.16;
            // }else{
            //     $product_price = ($item["subtotal"]/$item["quantity"]);
            // }
            $product_price = ($item["price"]/$item["quantity"]) / 1.16;

            $product = array(
                "cantidad"  => $item["quantity"],
                "unidad"    => $unidad,
                "concept"   => $item["name"],
                "precio"    => $product_price,
                "subtotal"  => $item["subtotal"],
            );

            array_push($items, $product);
        }

        //payment method
        if($payment_data["account"] == ''){
          $num_cta = 'No Identificado';
        }else{
          $num_cta = $payment_data["account"];
        }

        $params = array(
          "rfc"           => $customer->RFC,
          "items"         => $items,
          "numerocuenta"  => $num_cta,
          "formapago"     => "Pago en una Sola Exhibición",
          "metodopago"    => $payment_data["method"],
          "currencie"     => $order->currency,
          "iva"           => 1,
          "num_order"     => $order->id,
          "seriefactura"  => $configEntity['serie'],
          "save"          => "true",
          "descuento"     => $order->total_discount / 1.16,
        );

        $invoiced = WrapperApi::callCurl($url, $request, $params);

		//change status
		if($invoiced->status == 'success'){
			self::changeOrderStatus('wc-invoiced', $order->id);
		}

		return $invoiced;
    }

    /*
	 * Create client in factura.com system
	 *
	 * @param Array $data customer's data to save in factura.com system
	 * @return Array
	 *
	 */
	public function create_client($data){
        $configEntity = self::getConfigEntity();

		if($data["api_method"] == "create"){
			$url = $configEntity['apiurl'] . 'clients/create';
		}else{
			$url = $configEntity['apiurl'] . "clients/".$data["uid"]."/update";
		}

        $request = 'POST';
        $params = array(
          "nombre"          => $data["g_nombre"],
          "apellidos"       => $data["g_apellidos"],
          "email"           => $data["g_email"],
          "telefono"        => $data["f_telefono"],
          "razons"          => $data["f_nombre"],
          "rfc"             => $data["f_rfc"],
          "calle"           => $data["f_calle"],
          "numero_exterior" => $data["f_exterior"],
          "numero_interior" => $data["f_interior"],
          "codpos"          => $data["f_cp"],
          "colonia"         => $data["f_colonia"],
          "estado"          => $data["f_estado"],
          "ciudad"          => $data["f_municipio"],
          "delegacion"      => $data["f_municipio"],
          "save"            => true,
        );

		return WrapperApi::callCurl($url, $request, $params);
	}

    /*
     * Change status order in woocommerce system
     *
     * @param String $new_status new status to woocommerce's order
     * @param integer $order_id order id
     * @param integer $invoice_id invoice id
     * @return Array
     */
    public function changeOrderStatus($new_status, $order_id){
        global $wpdb;

        //change to invoiced
        $order = new WC_Order($order_id);
        $order->update_status($new_status, '');
    }


}
