<?php

/*
 * Title   : Conekta Payment Extension for WooCommerce
 * Author  : Cristina Randall
 * Url     : https://www.conekta.io/es/docs/plugins/woocommerce
 */

/**
 * Build the line items hash
 * @param array $items
 */
function build_line_items($items)
{
    $line_items = array();
    foreach ($items as $item) {
        $productmeta = new WC_Product($item['product_id']);
        $sku         = $productmeta->get_sku();
        $unit_price  = (floatval($item['line_subtotal']) * 1000) / floatval($item['qty']);
        $line_items  = array_merge($line_items, array(array(
         'name'        => $item['name'],
         'unit_price'  => intval(round(floatval($unit_price) / 10), 2),
         'description' => $item['name'],
         'quantity'    => intval($item['qty']),
         'sku'         => $sku,
         'type'        => 'physical',
         'tags'        => ['WooCommerce']
         ))
        );
    }
    
    return $line_items;
}

function build_tax_lines($taxes)
{
    $tax_lines = array();

    foreach ($taxes as $tax) {
        $tax_amount = floatval($tax['tax_amount']) * 1000;
        $tax_lines  = array_merge($tax_lines, array(
            array(
                "description" => $tax['label'],
                "amount"      => intval(round(floatval($tax_amount) / 10), 2)
            )
        ));
    }

    return $tax_lines;
}

function build_shipping_lines($data)
{
    $shipping_lines = array(
        array(
            "description" => (empty($data['shipping_method']) ? "default" : $data['shipping_method']),
            "amount"      => $data['shipping_cost'],
            "carrier" => (empty($data['shipping_carrier']) ? "default" : $data['shipping_carrier']),
            "method" => (empty($data['shipping_method']) ? "default" : $data['shipping_method'])
        )
    );

    return $shipping_lines;
}

function build_discount_lines($data)
{
    if(!empty($data["card"]["coupon_code"])) {
        $discount_lines = array(
            array(
                "description" => $data["card"]["coupon_code"],
                "kind"        => "coupon",
                "amount"      => $data["card"]["total_discount"]
            )
        );
    }

    return $discount_lines;
}

function build_shipping_contact($data)
{
    $shipping_contact = array(
        "email" => (empty($data["card"]["shipping_email"]) ? $data["card"]["email"] : $data["card"]["shipping_email"]),
        "phone" => (empty($data["card"]["shipping_phone"]) ? $data["card"]["phone"] : $data["card"]["shipping_phone"]),
        "receiver" => $data["card"]["name"],
        "address" => array(
            "street1" => $data["card"]["shipping_address_line1"],
            "street2" => $data["card"]["shipping_address_line2"],
            "city"    => $data["card"]["shipping_address_city"],
            "state"   => $data["card"]["shipping_address_state"],
            "country" => $data["card"]["shipping_address_country"],
            "zip"     => $data["card"]["shipping_address_zip"]
        ),
        "metadata" => array(
            "soft_validations" => true
        )
    );

    return $shipping_contact;
}

function build_customer_info($data)
{
    $customer_info = array(
        "name"  => $data["card"]["name"],
        "phone" => $data["card"]["phone"],
        "email" => $data["card"]["email"],
        "metadata" => array(
            "soft_validations" => true
        )
    );

    return $customer_info;
}

function build_details($data, $line_items)
{
    $details = array();
    $details = array(
        "email" => $data['card']['email'],
        "name" => $data['card']['name'],
        "phone" => $data['card']['phone'],
        "line_items"  => $line_items,
        "billing_address"  => array(
            "company_name"=> $data['card']['billing_company'],
            "street1" => $data['card']['address_line1'],
            "street2" => $data['card']['address_line2'],
            "zip" => $data['card']['address_zip'],
            "city" => $data['card']['address_city'],
            "phone" => $data['card']['phone'],
            "email" => $data['card']['email'],
            "country" => $data['card']['address_country'],
            "state" => $data['card']['address_state']
            ),
        "shipment"  => array(
            "carrier"=> (empty($data['shipping_carrier']) ? "default" : $data['shipping_carrier']),
            "service"=> (empty($data['shipping_method']) ? "default" : $data['shipping_method']),
            "price" => $data['shipping_cost'],
            "address"=> array(
                "street1" => $data['card']['shipping_address_line1'],
                "street2" => $data['card']['shipping_address_line2'],
                "zip" => $data['card']['shipping_address_zip'],
                "city" => $data['card']['shipping_address_city'],
                "phone" => $data['card']['shipping_phone'],
                "country" => $data['card']['shipping_address_country'],
                "state" => $data['card']['shipping_address_state']
                )
            )
        );
    
        // manually compose custom fields given from getRequestData
    $custom_fields = array();

    if ($data['card']['total_discount'] != null) {
        $custom_fields = array_merge($custom_fields, array(
            "total_discount" => $data['card']['total_discount']
            ));
    }

    if ($data['card']['is_paying_customer'] != null) {
        $custom_fields = array_merge($custom_fields, array(
            "is_paying_customer" => $data['card']['is_paying_customer']
            ));
    }

    if ($data['card']['coupon_code'] != null) {
        $custom_fields = array_merge($custom_fields, array(
            "coupon_code" => $data['card']['coupon_code']
            ));
    }

        // merge custom_fields into $details if not empty
    if (!empty($custom_fields)) {
        $details = array_merge($details, array(
            "custom_fields" => $custom_fields
            ));
    }

    return $details;
    
}

/**
* Bundle and format the order information
* @param WC_Order $order
* Send as much information about the order as possible to Conekta
*/
function getRequestData($order)
{
    if ($order AND $order != null)
    {
            // custom fields 
        $custom_fields = array(
            "total_discount" => (float) $order->get_total_discount() * 100
            );

        $order_coupons = $order->get_used_coupons();

        if (count($order_coupons) > 0) {
            $custom_fields = array_merge($custom_fields, array(
                "coupon_code" => $order_coupons[0]
                ));
        }

        return array(
           "amount"      => (float) $order->get_total() * 100,
           "token"       => $_POST['conekta_token'],
           "shipping_method"       => $order->get_shipping_method(),
           "shipping_carrier"       => $order->get_shipping_method(),
                 "shipping_cost" => (float)$order->get_total_shipping() * 100,  //get_shipping_cost
                 "monthly_installments" => (int) $_POST['monthly_installments'],
                 "currency"    => strtolower(get_woocommerce_currency()),
                 "description" => sprintf("Charge for %s", $order->billing_email),
                 "card"        => array_merge(array(
                    "name"            => sprintf("%s %s", $order->billing_first_name, $order->billing_last_name),
                    "address_line1"   => $order->billing_address_1,
                    "address_line2"   => $order->billing_address_2,
                    "billing_company"   => $order->billing_company,
                    "phone"   => $order->billing_phone,
                    "email"   => $order->billing_email,
                    "address_city"     => $order->billing_city,
                    "address_zip"     => $order->billing_postcode,
                    "address_state"   => $order->billing_state,
                    "address_country" => $order->billing_country,
                    "shipping_address_line1"   => $order->shipping_address_1,
                    "shipping_address_line2"   => $order->shipping_address_2,
                    "shipping_phone"   => $order->shipping_phone,
                    "shipping_email"   => $order->shipping_email,
                    "shipping_address_city"     => $order->shipping_city,
                    "shipping_address_zip"     => $order->shipping_postcode,
                    "shipping_address_state"   => $order->shipping_state,
                    "shipping_address_country" => $order->shipping_country
                    ), $custom_fields)
                 );
    }
    return false;
}

/**
* Is the user a paying customer?
*
* @access public
* @return bool
*/
function is_paying_customer($user_id) {
   $paying_customer = get_user_meta( $user_id, 'paying_customer', true );  
   if( $paying_customer != '' && absint( $paying_customer ) > 0) {
       return true;
   }

   return false;
}
