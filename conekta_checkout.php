<?php
/*
Plugin Name: Conekta Payment Gateway
Plugin URI: https://wordpress.org/plugins/conekta-woocommerce/
Description: Payment Gateway through Conekta.io for Woocommerce for both credit and debit cards as well as cash payments in OXXO.
Version: 0.1.1
Author: Conekta.io
Author URI: https://www.conekta.io
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/*
 * Title   : Conekta Payment Extension for WooCommerce
 * Author  : Conekta.io
 * Url     : https://www.conekta.io/es/docs/plugins/woocommerce
 */

function conekta_checkout_init_your_gateway()
{
    if (class_exists('WC_Payment_Gateway'))
    {
        include_once('conekta_gateway_helper.php');
        include_once('conekta_card_gateway.php');
        include_once('conekta_cash_gateway.php');

    }
}

add_action('plugins_loaded', 'conekta_checkout_init_your_gateway', 0);
