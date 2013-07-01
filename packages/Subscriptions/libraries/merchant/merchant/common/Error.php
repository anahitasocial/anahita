<?php
/**
 * Description of Error
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Merchant_Error {
  private $errors = array();

  public function add($field, $message) {
     $this->errors[$field] = $message;
  }

  public function errors() {
    return $this->errors;
  }
}
?>
