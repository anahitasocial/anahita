<?php
/**
 * Description of gateways.php
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */
if ( false === spl_autoload_register('gateways_autoload') ){
  throw new Exception('Unable to register gateways_autoload as an autoloading method');
}

function gateways_autoload($class_name) {
  $path = dirname(__FILE__) . "/";
  $filename = explode('_',$class_name);
  $class_filename = array_pop($filename);
  if ( file_exists( $path . 'gateways/' . $class_filename . ".php" ) ) {
    require_once( $path . 'gateways/' . $class_filename . ".php");
  }
}
?>
