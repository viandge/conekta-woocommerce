function formatFields(){
	jQuery('#conekta-card-number').payment('formatCardNumber');
	jQuery('#conekta-card-expiration').payment('formatCardExpiry');
	jQuery('#conekta-card-cvc').payment('formatCardCVC');
}

function formToObject(form){
	var values = {};
	jQuery.each(form.serializeArray(), function(i, field) {
	    values[field.name] = field.value;
	});
	return values;
}

jQuery(document).ready(function($) {
	Conekta.setPublishableKey(wc_conekta_params.public_key);

	var $form = $('form.checkout,form#order_review');

	formatFields();
	$('body').on('updated_checkout', function () {
		formatFields();
	});

	var conektaErrorResponseHandler = function(response) {
		$form.find('.payment-errors').text(response.message_to_purchaser);
		$form.unblock();
	};

	var conektaSuccessResponseHandler = function(response) {
		$form.append($('<input type="hidden" name="conekta_token" />').val(response.id));
		$form.submit();
	};

	$('body').on('click', 'form#order_review input:submit', function(){
		if($('input[name=payment_method]:checked').val() != 'conektacard'){
			return true;
		}

		return false;
	});

	$('body').on('click', 'form.checkout input:submit', function(){
		$('.woocommerce_error, .woocommerce-error, .woocommerce-message, .woocommerce_message').remove();
		$('form.checkout').find('[name="conekta_token"]').remove();
	});

	$('form.checkout').bind('checkout_place_order', function (e) {

		if ($('input[name="payment_method"]:checked').val() != 'conektacard') {
			return true;
		}
		$form.find('.payment-errors').html('');
		$form.block({message: null, overlayCSS: {background: "#fff url(" + woocommerce_params.ajax_loader_url + ") no-repeat center", backgroundSize: "16px 16px", opacity: 0.6}});

		if ($form.find('[name="conekta_token"]').length){
			return true;
		}

		var formData = formToObject($form);
		var expiration = $("#conekta-card-expiration").val().replace(/ /g,'').split("/");
		var exp_month = expiration[0];

		switch (expiration[1].length){
			case 4:
				var exp_year = expiration[1];
				break;
			case 2:
				var exp_year = "20" + expiration[1];
				break;
			default:
				alert("CVC is invalid");
				return false;
				break;
		}

		var cardData = {
			"card": {
				"number": $("#conekta-card-number").val(),
				"name": $("#conekta-card-name").val(),
				"exp_year": exp_year,
				"exp_month": exp_month,
				"cvc": $("#conekta-card-cvc").val(),
				"address": {
					"street1": formData.billing_address_1,
					"street2": formData.billing_address_2,
					"city": formData.billing_city,
					"state": formData.billing_state,
					"zip": formData.billing_postcode,
					"country": formData.billing_country
				}
			}
		};

		Conekta.token.create(cardData, conektaSuccessResponseHandler, conektaErrorResponseHandler);

		return false;
	}); 
});