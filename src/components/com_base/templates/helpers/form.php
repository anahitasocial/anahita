<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Form Helper
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseTemplateHelperForm extends LibBaseTemplateHelperForm
{
	/**
	 * Render a parameter
	 * 
	 * @param  JParameter $parameter
	 * @param  KConfig 	  $config
	 * @return string
	 */
	protected function _render($parameter, $config)
	{
		$params = $parameter->getParams($config->name, $config->group);
		foreach($params as $key => $param) {
			$params[$key] = array($param[0]=>$param[1]) ;
		}
		return $this->_template->renderHelper('ui.form', $params[0]);
		return $parameter->render($config->name, $config->group);
	}	
			
}