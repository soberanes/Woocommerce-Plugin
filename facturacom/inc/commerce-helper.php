<?php

class CommerceHelper{

    static function getOrderById($orderId){
        // $order = new WC_Order($orderId);
        $order = wc_get_order($orderId);
        $order_post = get_post( $orderId );

        $order_data = array(
          'id'                        => $order->id,
          'order_number'              => $order->get_order_number(),
          'created_at'                => $order_post->post_date_gmt,
          'updated_at'                => $order_post->post_modified_gmt,
          'completed_at'              => $order->completed_date,
          'status'                    => $order->get_status(),
          'currency'                  => $order->order_currency,
          'total'                     => wc_format_decimal( $order->get_total(), 2 ),
          'total_line_items_quantity' => $order->get_item_count(),
          'total_tax'                 => wc_format_decimal( $order->get_total_tax(), 2 ),
          'total_shipping'            => wc_format_decimal( $order->get_total_shipping(), 2 ),
          'cart_tax'                  => wc_format_decimal( $order->get_cart_tax(), 2 ),
          'shipping_tax'              => wc_format_decimal( $order->get_shipping_tax(), 2 ),
          'total_discount'            => wc_format_decimal( $order->get_total_discount(), 2 ),
          'cart_discount'             => wc_format_decimal( $order->get_cart_discount(), 2 ),
          'order_discount'            => wc_format_decimal( $order->get_order_discount(), 2 ),
          'shipping_methods'          => $order->get_shipping_method(),
          'payment_details' => array(
            'method_id'    => $order->payment_method,
            'method_title' => $order->payment_method_title,
            'paid'         => isset( $order->paid_date ),
          ),
          'billing_email' => $order->billing_email,
          'billing_address' => array(
            'first_name' => $order->billing_first_name,
            'last_name'  => $order->billing_last_name,
            'company'    => $order->billing_company,
            'address_1'  => $order->billing_address_1,
            'address_2'  => $order->billing_address_2,
            'city'       => $order->billing_city,
            'state'      => $order->billing_state,
            'postcode'   => $order->billing_postcode,
            'country'    => $order->billing_country,
            'email'      => $order->billing_email,
            'phone'      => $order->billing_phone,
          ),
          'shipping_address' => array(
            'first_name' => $order->shipping_first_name,
            'last_name'  => $order->shipping_last_name,
            'company'    => $order->shipping_company,
            'address_1'  => $order->shipping_address_1,
            'address_2'  => $order->shipping_address_2,
            'city'       => $order->shipping_city,
            'state'      => $order->shipping_state,
            'postcode'   => $order->shipping_postcode,
            'country'    => $order->shipping_country,
          ),
          'note'                      => $order->customer_note,
          'customer_ip'               => $order->customer_ip_address,
          'customer_user_agent'       => $order->customer_user_agent,
          'customer_id'               => $order->customer_user,
          'view_order_url'            => $order->get_view_order_url(),
          'line_items'                => array(),
          'shipping_lines'            => array(),
          'tax_lines'                 => array(),
          'fee_lines'                 => array(),
          'coupon_lines'              => array(),
        );
        // add line items
        foreach( $order->get_items() as $item_id => $item ) {
          $product = $order->get_product_from_item( $item );

          $order_data['line_items'][] = array(
            'id'         => $item_id,
            'subtotal'   => wc_format_decimal( $order->get_line_subtotal( $item ), 2 ),
            'total'      => wc_format_decimal( $order->get_line_total( $item ), 2 ),
            'total_tax'  => wc_format_decimal( $order->get_line_tax( $item ), 2 ),
            'price'      => wc_format_decimal( $order->get_item_total( $item ), 2 ),
            'quantity'   => (int) $item['qty'],
            'tax_class'  => ( ! empty( $item['tax_class'] ) ) ? $item['tax_class'] : null,
            'name'       => $item['name'],
            'product_id' => ( isset( $product->variation_id ) ) ? $product->variation_id : $product->id,
            'sku'        => is_object( $product ) ? $product->get_sku() : null,
          );
        }

        // add shipping as a product
        foreach($order->get_items('shipping') as $shipping_key => $shipping_item){
            if($shipping_item['method_id'] != 'free_shipping'){
                $order_data['line_items'][] = array(
                    'id'         => $shipping_key,
                    'subtotal'   => wc_format_decimal($shipping_item['cost'], 2),
                    'total'      => wc_format_decimal($shipping_item['cost'], 2),
                    'total_tax'  => null,
                    'price'      => wc_format_decimal($shipping_item['cost'], 2),
                    'quantity'   => 1,
                    'tax_class'  => null,
                    'name'       => $shipping_item['name'],
                    'product_id' =>$shipping_key,
                    'sku'        => $shipping_item['method_id'],
                );
            }
        }

        return (object)$order_data;
    }

}
