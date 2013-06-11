<?php
/**
 * @version		$Id: parameter.php 14401 2010-01-26 14:10:00Z louis $
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

jimport( 'joomla.registry.registry' );
jimport( 'joomla.html.parameter' );

//Register the element class with the loader
JLoader::register('JElement', JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'parameter'.DS.'element.php');

/**
 * Parameter handler
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class RTParameter extends JParameter
{

    var $_defaults = array();

	/**
	 * modified
	 */
	function __construct($data, $path='')
	{
		parent::__construct($data);

        if ($path) {
			$this->loadSetupFile($path);
		}

		// Set base path
		$this->_elementPath[] = JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'html'.DS.'parameter'.DS.'element';

	}

	function get($key, $default = '', $group = '_default')
	{
		$value = $this->getValue($group.'.'.$key);

        if (empty($value) && ($value !== 0) && ($value !== '0')) {

            if (!$default) {
                if (key_exists($key,$this->_defaults)) {
                    $default = $this->_defaults[$key];
                } else {
                    $params =& $this->_xml['_default']->_children;
                    foreach ($params as $param) {
                        $att = $param->_attributes;
                        if (key_exists('default',$att))
                            $this->_defaults[$att['name']] = $att['default'];
                    }
                    if (key_exists($key,$this->_defaults))
                        $default = $this->_defaults[$key];
                }
            }
            return $default;
        } else {
            return $value;
        }
	}
	
}