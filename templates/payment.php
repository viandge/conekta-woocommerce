<?php
/*
 * Title   : Conekta Payment extension for WooCommerce
 * Author  : Cristina Randall
 * Url     : https://www.conekta.io/es/docs/plugins/woocommerce
 */
?>
<div class="clear"></div>
<span style="width: 100%; float: left; color: red;" class='payment-errors required'></span>
<div class="form-row form-row-wide">
  <label for="conekta-card-number"><?php echo $this->lang_options["card_number"]; ?><span class="required">*</span></label>
  <input id="conekta-card-number" class="input-text wc-credit-card-form-card-number" type="text" placeholder="•••• •••• •••• ••••" data-conekta="card[number]" />
</div>

<div class="form-row form-row-wide">
  <label for="conekta-card-name"> <?php echo $this->lang_options["card_name"]; ?><span class="required">*</span></label>
  <input id="conekta-card-name" type="text" data-conekta="card[name]" class="input-text" />
</div>
<div class="clear"></div>
<div class="form-row form-row-wide">
  <label for="conekta-card-expiration"><?php echo $this->lang_options["expiration"]; ?> (MM/YY) <span class="required">*</span></label>
  <input id="conekta-card-expiration" data-conekta="card[expiration]" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="MM / YY" />
</div>

<div class="clear"></div>
<p class="form-row form-row-first">
    <label for="conekta-card-cvc">CVC <span class="required">*</span></label>
    <input id="conekta-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="text" maxlength="4" data-conekta="card[cvc]" value=""  style="border-radius:6px"/>
</p>

<?php if ($this->enablemeses): ?>
<p class="form-row form-row-last">
  <label><?php echo $this->lang_options["payment_type"] ?><span class="required">*</span></label>
  <select id="monthly_installments" name="monthly_installments" autocomplete="off">
    <option selected="selected" value="1"><?php echo $this->lang_options["single_payment"] ?></option>
    <?php foreach($this->lang_options["monthly_installments"] AS $months => $description): ?>
      <option value="<?php echo $months; ?>"><?php echo $description; ?></option>
    <?php endforeach; ?>
  </select>
</p>

<?php endif; ?>
<div class="clear"></div>
