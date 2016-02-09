<?php
/**
* Plugin name: Factura punto com para woocommerce
* Plugin URI: http://factura.com
* Description: Conecta tu tienda de woocommerce para que tus clientes puedan facturar todos los pedidos.
* Version: 1.3
* Author: Factura.com
*/

include( plugin_dir_path( __FILE__ ) . 'inc/factura-wrapper.php');

define( 'FACTURACOM__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FACTURACOM__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FACTURACOM__APIURL', 'http://devfactura.in/api/v1/');

//init hooks
add_action( 'init', 'facturacom_scripts' );
add_action('admin_menu', 'facturacom_menu');
add_action( 'init', 'styles_factura_enqueuer' );
add_shortcode('facturacom_form', 'form_shortcode');

/**
 * Register new status
 * Tutorial: http://www.sellwithwp.com/woocommerce-custom-order-status-2/
**/
function register_invoiced_order_status() {
    register_post_status( 'wc-invoiced', array(
        'label'                     => 'Pedido Facturado',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Pedidos Facturados <span class="count">(%s)</span>', 'Pedidos Facturados <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'register_invoiced_order_status' );

// Add to list of WC Order statuses
function add_invoiced_to_order_statuses( $order_statuses ) {

    $new_order_statuses = array();

    // add new order status after processing
    foreach ( $order_statuses as $key => $status ) {

        $new_order_statuses[ $key ] = $status;

        if ( 'wc-completed' === $key ) {
            $new_order_statuses['wc-invoiced'] = 'Pedido Facturado';
        }
    }

    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'add_invoiced_to_order_statuses' );

function facturacom_menu(){
  add_menu_page( 'Factura.com', 'Factura.com', 'manage_woocommerce', 'facturacom-dashboard', 'facturacom_dashboard', plugin_dir_url( __FILE__ ).'/assets/factura-icon.png' );
}

function facturacom_scripts() {
   wp_register_script( "facturacom_script", FACTURACOM__PLUGIN_URL . 'assets/facturacom.js', array('jquery') );
   wp_localize_script( 'facturacom_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
   wp_enqueue_script( 'jquery' );
   wp_enqueue_script( 'facturacom_script' );
}

//Creating Admin dashboard
function facturacom_dashboard(){
?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">

        <div id="icon-themes" class="icon32"></div>
        <h2>Factura.com para Woocommerce</h2>
        <?php //settings_errors(); ?>

        <?php
            $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'invoice_report';
        ?>

        <h2 class="nav-tab-wrapper">
            <a href="?page=facturacom-dashboard&tab=invoice_report" class="nav-tab <?php echo $active_tab == 'invoice_report' ? 'nav-tab-active' : ''; ?>">Historial Facturas</a>
            <a href="?page=facturacom-dashboard&tab=factura_settings" class="nav-tab <?php echo $active_tab == 'factura_settings' ? 'nav-tab-active' : ''; ?>">Configuración</a>
        </h2>


            <?php

            if( $active_tab == 'invoice_report' ) {
                facturacom_history();
            } else {
                facturacom_settings();
            }

           ?>



    </div><!-- /.wrap -->
<?php
} // end sandbox_theme_display

function facturacom_history(){
    $invoices = FacturaWrapper::getInvoices();
    $index    = 0;
    $settings = FacturaWrapper::getConfigEntity();
    ?>
    <h1><strong><?php echo count($invoices->data) ?></strong> facturas en sistema.</h1>
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <th>Folio</th>
            <th>Fecha de timbrado</th>
            <th>RFC receptor</th>
            <th>Pedido</th>
            <th>Estado</th>
            <th>PDF</th>
            <th>XML</th>
            <th>Opciones</th>
        </thead>
        <tbody>
            <?php foreach ($invoices->data as $invoice): ?>
            <tr>
                <td><?php echo $invoice->Folio ?></td>
                <td><?php echo $invoice->FechaTimbrado ?></td>
                <td><?php echo $invoice->Receptor ?></td>
                <?php $wpuser = get_user_by('id','1'); ?>
                <td>
                    <a href="<?php echo get_site_url(); ?>/wp-admin/post.php?post=<?php echo $invoice->NumOrder ?>&action=edit"><?php echo $invoice->NumOrder ?></a> de
                    <a href="<?php echo get_site_url(); ?>/wp-admin/user-edit.php?user_id=<?php echo $invoice->ReferenceClient ?>"><?php echo $wpuser->data->user_nicename ?></a>
                </td>
                <td><?php echo $invoice->Status ?></td>
                <td><a href="http://devfactura.in/api/publica/invoice/<?php echo $invoice->UID ?>/pdf">PDF</a></td>
                <td><a href="http://devfactura.in/api/publica/invoice/<?php echo $invoice->UID ?>/xml">XML</a></td>
                <td>
                    <a href="#" class="button button-primary send_invoice" data-uid="<?php echo $invoice->UID ?>">
                        Enviar por correo
                    </a>
                    <a href="#" class="button button-secundary cancel_invoice" data-uid="<?php echo $invoice->UID ?>">
                        Cancelar
                    </a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php
}

function facturacom_settings(){
    $settings = FacturaWrapper::getConfigEntity();
    ?>
    <form id="facturacom_settings" method="post">
        <table class="form-table">
    		<tbody>
                <tr valign="top">
        			<th scope="row" class="titledesc">
        				<label for="apikey">Api Key*</label>
        			</th>
        			<td class="forminp">
        				<fieldset>
        					<input type="text" id="apikey" name="apikey" value="<?php echo $settings['apikey'] ?>" style="width:710px">
        				</fieldset>
        			</td>
        		</tr>
                <tr valign="top">
        			<th scope="row" class="titledesc">
        				<label for="apisecret">Api Secret*</label>
        			</th>
        			<td class="forminp">
        				<fieldset>
        					<input type="text" id="apisecret" name="apisecret" value="<?php echo $settings['apisecret'] ?>" style="width:710px">
        				</fieldset>
        			</td>
        		</tr>
                <tr valign="top">
        			<th scope="row" class="titledesc">
        				<label for="serie">Serie de facturación*</label>
        			</th>
        			<td class="forminp">
        				<fieldset>
        					<input type="text" id="serie" name="serie" value="<?php echo $settings['serie'] ?>" style="width:710px">
        				</fieldset>
        			</td>
        		</tr>
                <tr valign="top">
        			<th scope="row" class="titledesc">
        				<label for="dayoff">Días después de mes permitido facturar*</label>
        			</th>
        			<td class="forminp">
        				<fieldset>
        					<input type="number" id="dayoff" name="dayoff" value="<?php echo $settings['dayoff'] ?>" min="1" max="25" style="width: 100px">
        				</fieldset>
        			</td>
        		</tr>
                <tr>
                    <th colspan="2">
                        <hr>
                    </th>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="front-title">Título de la sección en el área de clientes</label>
                    </th>
                    <td class="forminp">
                        <fieldset>
                            <input type="text" name="front-title" value="<?php echo $settings['title'] ?>" style="width: 710px">
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="front-desc">Texto descriptivo de la sección en el área de clientes (se puede incrustar HTML)</label>
                    </th>
                    <td class="forminp">
                        <fieldset>
                            <textarea name="front-desc" rows="8" cols="70" style="width: 710px"><?php echo $settings['description'] ?></textarea>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="front-title">Color de fondo en encabezado</label>
                    </th>
                    <td class="forminp">
                        <fieldset>
                            <input type="color" name="front-title" value="<?php echo $settings['colorheader'] ?>" style="width: 710px">
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label for="front-title">Color de letra en encabezado</label>
                    </th>
                    <td class="forminp">
                        <fieldset>
                            <input type="color" name="front-title" value="<?php echo $settings['colorfont'] ?>" style="width: 710px">
                        </fieldset>
                    </td>
                </tr>
        	</tbody>
        </table>
        <p id="submit_message" class="input_success">
            La configuración se ha guardado correctamente.
        </p>
        <p class="submit">
            <input type="button" id="send_settings" class="button button-primary" value="Guardar configuración" style="width: 180px;text-align:center;">
            <span id="setting_loading" style="display:none;">Guardando configuración</span>
        </p>
    </form>
    <?php
}

function form_shortcode(){
    echo FacturaWrapper::form_shortcode();
}

function styles_factura_enqueuer(){
    // Register the style like this for a plugin:
    wp_register_style( 'factura-com-style', plugins_url( 'assets/facturacom.css', __FILE__ ), array(), '20120208', 'all' );
    // For either a plugin or a theme, you can then enqueue the style:
    wp_enqueue_style( 'factura-com-style' );
}

add_action( 'wp_ajax_send_invoice', 'send_invoice' );
function send_invoice(){
    $invoiceUid = $_POST['uid'];

    $sentMessage = FacturaWrapper::sendInvoiceEmail($invoiceUid);

    echo json_encode($sentMessage, JSON_PRETTY_PRINT);
    wp_die();
}

add_action( 'wp_ajax_cancel_invoice', 'cancel_invoice' );
function cancel_invoice(){
    $invoiceUid = $_POST['uid'];

    $cancelMessage = FacturaWrapper::cancelInvoice($invoiceUid);

    echo json_encode($cancelMessage, JSON_PRETTY_PRINT);
    wp_die();
}

add_action( 'wp_ajax_save_config', 'save_config_callback' );
function save_config_callback() {
	global $wpdb;

    $settings = array(
        'apikey'      => $_POST['apikey'],
        'apisecret'   => $_POST['apisecret'],
        'serie'       => $_POST['serie'],
        'dayoff'      => $_POST['dayoff'],
        'apiurl'      => FACTURACOM__APIURL,
        'title'       => $_POST['title'],
        'description' => $_POST['description'],
        'colorheader' => $_POST['colorheader'],
        'colorfont'   => $_POST['colorfont'],
    );

    $saved = FacturaWrapper::saveSettings($settings);

    $response = array(
        'success' => $saved,
    );

    echo json_encode($response, JSON_PRETTY_PRINT);
	wp_die();
}

add_action('wp_ajax_step_one', 'step_one_callback');
add_action('wp_ajax_nopriv_step_one', 'step_one_callback');
function step_one_callback(){
    global $wpdb;
    //FacturaWrapper::deleteCookies('all');
    $customerRfc = trim($_POST['rfc']);
    $customerData = FacturaWrapper::getCustomer($customerRfc);
    $orderData    = FacturaWrapper::getOrder(trim($_POST['order']));

    $orderEmail = trim($_POST['email']);
    $validate = FacturaWrapper::validateOrder($orderData, $orderEmail, $customerRfc);

    if($validate['Error'] == true){
        $response = array(
            'success' => false,
            'message' => $validate['Message'],
            'metadata' => (isset($validate['Meta'])) ? $validate['Meta'] : null,
        );
    }else{
        FacturaWrapper::saveCookies('customer', $customerData);
        FacturaWrapper::saveCookies('order', $orderData);

        if($customerData->status == "error"){
            $customerResponse = $customerData;
        }else{
            $customerResponse = FacturaWrapper::getCookies('customer')->Data;
        }

        $response = array(
            'success' => true,
            'customer' => $customerResponse,
        );
    }

    echo json_encode($response, JSON_PRETTY_PRINT);
	wp_die();
}

add_action("wp_ajax_create_client", "create_client_callback");
add_action("wp_ajax_nopriv_create_client", "create_client_callback");
function create_client_callback(){
  if($_REQUEST["csrf"] == null){

    if( $_REQUEST["g_nombre"] == null || $_REQUEST["g_apellidos"] == null ||
    $_REQUEST["g_email"] == null || $_REQUEST["f_calle"] == null || $_REQUEST["f_colonia"] == null ||
    $_REQUEST["f_cp"] == null || $_REQUEST["f_estado"] == null || $_REQUEST["f_exterior"] == null ||
    $_REQUEST["f_municipio"] == null || $_REQUEST["f_nombre"] == null ||
    $_REQUEST["f_rfc"] == null || $_REQUEST["f_telefono"] == null ){

      $response = array(
        'success' => false,
        'message' => 'No se han recibido todos los campos. Por favor revise la información del cliente.'
      );
      echo json_encode($response, JSON_PRETTY_PRINT);
      wp_die();
    }

    $customerNewData = FacturaWrapper::create_client($_REQUEST, $_REQUEST["order"]);
    FacturaWrapper::saveCookies('customer', $customerNewData->Data);

    $response = array(
      'success' => true,
      'customer' => FacturaWrapper::getCookies('customer'),
      'order' => FacturaWrapper::getCookies('order'),
    );

  }else{
    $response = array(
        'success' => false,
        'message' => "La operación no se ha podido realizar",
    );
  }

  echo json_encode($response, JSON_PRETTY_PRINT);
  die;
}

add_action("wp_ajax_generate_invoice", "generate_invoice_callback");
add_action("wp_ajax_nopriv_generate_invoice", "generate_invoice_callback");
function generate_invoice_callback(){

  if($_REQUEST["payment_m"] == null){
    $response = array(
      'success' => false,
      'message' => 'No se recibieron algunos datos.'
    );
    echo json_encode($response, JSON_PRETTY_PRINT);
    die;
  }

  if($_REQUEST["payment_m"] == 4 || $_REQUEST["payment_m"] == 5){
    if($_REQUEST["num_cta_m"] == ""){
      $response = array(
        'success' => false,
        'message' => 'Si selecciona Pago con tarjeta o Transferencia electrónica, necesita especificar los últimos 4 dígitos de su cuenta o tarjeta.',
      );
      echo json_encode($response, JSON_PRETTY_PRINT);
      die;
    }
  }

  $payment_data = array(
    "method"      => $_REQUEST["payment_m"],
    "method_text" => $_REQUEST["payment_t"],
    "account"     => $_REQUEST["num_cta_m"]
  );

  $invoice = FacturaWrapper::generateInvoice($payment_data);

  $response = array(
      'success' => true,
      'invoice' => $invoice
  );

  //delete sessions
  unset($_SESSION['customer']);
  unset($_SESSION['order']);

  echo json_encode($response, JSON_PRETTY_PRINT);
  die;
}
