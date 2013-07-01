<?php
define('RESET_SEQ',"\033[0m");
define('COLOR_SEQ',"\033[");
define('BOLD_SEQ', "\033[1m");
/**
 * Description of Logger
 *
 * @package Aktive Merchant
 * @author  Andreas Kollaros
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class Merchant_Logger {

  public static $start_time;

  public static $path = null;
  public static $filename = 'development.log';

  static public function start_logging() {
    self::$start_time = microtime(true);
    self::log (COLOR_SEQ ."1;32m"
            ."Started at : [".date('H:i:s d-m-Y', time())."]"
            .RESET_SEQ);
  }

  static public function log($string) {
    if ( null === self::$path ) self::$path = dirname(__FILE__) . '/../../../log/';

    if ( !is_writable(self::$path . self::$filename)) return;

    $fp = fopen(self::$path . self::$filename, 'a');
    fwrite($fp, $string."\n");
    fclose($fp);
  }

  static public function error_log($string) {
    self::log( COLOR_SEQ."1;31m".$string.RESET_SEQ);
  }

  static public function end_logging() {
    $buffer = COLOR_SEQ."1;32mParse time: ("
            .number_format( (microtime(true) - self::$start_time) * 1000,'4')
            ."ms)".RESET_SEQ;
    self::log($buffer);
  }

  static public function save_response($string) {
    if ( null === self::$path ) self::$path = dirname(__FILE__) . '/../../../log/';

    if ( ! is_writable(self::$path . 'response.xml') OR ! file_exists(self::$path . 'response.xml'))
    {
        return;
    }

    $fp = fopen(self::$path . 'response.xml', 'w');
    fwrite($fp, $string);
    fclose($fp);
  }
  static public function save_request($string) {
    if ( null === self::$path ) self::$path = dirname(__FILE__) . '/../../../log/';

    if ( ! is_writable(self::$path . 'request.xml') OR ! file_exists(self::$path . 'request.xml'))
    {
        return;
    }

    $fp = fopen(self::$path . 'request.xml', 'w');
    fwrite($fp, $string);
    fclose($fp);
  }

  static public function print_ar($array) {
    echo '<pre>'."\n";
    print_r($array);
    echo '</pre>'."\n";
  }
}
?>
