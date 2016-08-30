<?php
/**
* @version		$Id: import.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
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

/**
 * Load the loader class
 */
if(!class_exists('JLoader'))
{
    require_once( JPATH_LIBRARIES.DS.'loader.php');
}

/**
 * Joomla! library imports
 */

//Base classes
JLoader::import( 'joomla.base.object');

//Factory class and methods
JLoader::import( 'joomla.factory');

//Error
JLoader::import( 'joomla.error.error');
JLoader::import( 'joomla.error.exception');

//Utilities
JLoader::import( 'joomla.utilities.arrayhelper');

//Filters
JLoader::import( 'joomla.filter.filterinput');
