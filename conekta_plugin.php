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

class WC_Conekta_Plugin extends WC_Payment_Gateway
{
	public $version  = "0.3.0";
	public $description = "Payment Gateway through Conekta.io for Woocommerce for both credit and debit cards as well as cash payments in OXXO and monthly installments for Mexican credit cards.";
	public $plugin_name = "Conekta Payment Gateway for Woocommerce";
	public $plugin_URI = "https://wordpress.org/plugins/conekta-woocommerce/";
	public $author = "Conekta.io";
	public $author_URI = "https://www.conekta.io";

	protected $lang;
	protected $lang_messages;

	public function set_locale_options()
	{
		if (function_exists("get_locale") && get_locale() !== "") {
			$current_lang = explode("_", get_locale());
			$this->lang = $current_lang[0];
			$this->lang_messages = require_once("lang/" . $this->lang . ".php");
			Conekta::setLocale($this->lang);
		}

		return $this;
	}

	public function get_lang_options(){
		return $this->lang_messages;
	}
}