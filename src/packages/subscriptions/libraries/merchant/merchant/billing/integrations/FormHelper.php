<?php 
/**
 * Description of Merchant_Billing_FormHelper
 *
 * @author Andreas Kollaros
 */

class Merchant_Billing_FormHelper {

  protected function hidden_field_tag($field, $value) {
    return '<input type="hidden" name="'.$field.'" id="'.$field.'" value="'.$value.'"/>'."\n";
  }

  protected function text_field_tag($field, $value) { 
    return '<input type="text" name="'.$field.'" id="'.$field.'" value="'.$value.'"/>'."\n";
  }

  protected function select_tag() {

  }
}

?>
