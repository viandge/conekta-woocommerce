<?php

if (!class_exists('Conekta'))
{
	require_once("lib/conekta-php/lib/Conekta.php");
}

/*
* Title   : Conekta Payment extension for WooCommerce
* Author  : Conekta.io
* Url     : https://wordpress.org/plugins/conekta-woocommerce
*/

class WC_Conekta_Plugin extends WC_Payment_Gateway {
	const LANG = "EN";
	
	public $version  = "0.3.0";
	public $description = "Payment Gateway through Conekta.io for Woocommerce for both credit and debit cards as well as cash payments in OXXO and monthly installments for Mexican credit cards.";
	public $plugin_name = "Conekta Payment Gateway";
	public $plugin_URI = "https://wordpress.org/plugins/conekta-woocommerce/";
	public $author = "Conekta.io";
	public $author_URI = "https://www.conekta.io";
}