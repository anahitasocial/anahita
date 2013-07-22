<?php
/**
* @version		$Id: radio.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla.Framework
* @subpackage	Parameter
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

require_once (JPATH_LIBRARIES.'/joomla/html/parameter/element/radio.php');
/**
 * Renders a radio element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JElementPatcher extends JElementRadio
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Patcher';

	function fetchElement($name, $value, &$node, $control_name)
	{
        if (!is_writable(JPATH_ADMINISTRATOR.'/includes'))
        {
            return 'Unable to write to <strong>administrator/includes</strong> directory.  Unable to patch files. Check your permissions.';
        }

        if (!is_writable(JPATH_ADMINISTRATOR.'/includes/application.php')){
           return 'Unable to write to <strong>administrator/includes/application.php</strong>.  Unable to patch files.  Check your permissions.';
        }

        return parent::fetchElement($name, $value, $node, $control_name);
	}
}
