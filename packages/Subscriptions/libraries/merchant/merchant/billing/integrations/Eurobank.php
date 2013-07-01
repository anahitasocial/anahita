<?php
/**
 * Description of Eurobank
 *
 * @author Andreas Kollaros
 */
class Merchant_Billing_Integration_Eurobank extends Merchant_Billing_Helper{
  
  
  public function __construct($order, $account, $options=array()){
    parent::__construct($order, $account, $options);
    $this->mapping( 'billing_address', array(
        'country' => 'country'
      ) );

    $this->mapping('customer', array(
        'first_name' => 'first_name',
        'last_name' => 'last_name'
      ));
    $this->mapping('currency', 'currency_code');
  }
    
}
?>
