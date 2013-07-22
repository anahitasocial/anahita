<?php
/**
 * Legacy Mode compatibility
 * @version		$Id: database.mysqli.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla.Legacy
 * @deprecated	As of version 1.5
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.database.database' );
jimport( 'joomla.database.database.mysqli' );
/**
 * @package		Joomla
 * @deprecated As of version 1.5
 */
class database extends JDatabase {
	function __construct ($host='localhost', $user, $pass, $db='', $table_prefix='', $offline = true) {
		parent::__construct( 'mysqli', $host, $user, $pass, $db, $table_prefix );
	}
}
?>