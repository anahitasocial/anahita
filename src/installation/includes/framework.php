<?php
/**
* @version		$Id: framework.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @subpackage	Installation
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/*
 * Joomla! system checks
 */

error_reporting( E_ALL );
@set_magic_quotes_runtime( 0 );
@ini_set('zend.ze1_compatibility_mode', '0');

/*
 *
 */
if (file_exists( JPATH_CONFIGURATION . DS . 'configuration.php' ) && (filesize( JPATH_CONFIGURATION . DS . 'configuration.php' ) > 10) && !file_exists( JPATH_INSTALLATION . DS . 'index.php' )) {
	header( 'Location: ../index.php' );
	exit();
}

/*
 * Joomla! system startup
 */

// System includes
require_once( JPATH_LIBRARIES		.DS.'joomla'.DS.'import.php');

// Installation file includes
define( 'JPATH_INCLUDES', dirname(__FILE__) );

/*
 * Joomla! framework loading
 */

// Include object abstract class
require_once(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'utilities'.DS.'compat'.DS.'compat.php');

// Joomla! library imports
jimport( 'joomla.database.table' );
jimport( 'joomla.user.user');
jimport( 'joomla.environment.uri' );
jimport( 'joomla.user.user');
jimport( 'joomla.html.parameter' );
jimport( 'joomla.utilities.utility' );
jimport( 'joomla.language.language');
jimport( 'joomla.utilities.string' );
// Anahita
jimport( 'anahita.anahita');

?>
