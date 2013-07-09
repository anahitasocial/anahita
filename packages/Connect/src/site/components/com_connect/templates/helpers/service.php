<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Connect Helper Class
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
 class ComConnectTemplateHelperService extends KTemplateHelperAbstract
 {
	/**
	 * Render logins, If $return_array is set to true, then it returns an array instead of a string
	 * 
	 * @param boolean $return_array Boolean value to whether return an array of string or one string. By default is false
	 * @return string 
	 */
 	public function renderLogins($return_array=false)
 	{ 
        $services = ComConnectHelperApi::getServices();
 		
 		$html = array();
 		
 		foreach($services as $name => $service) 
 		{
 			$html[] = $this->login($name);
 		}
 		
 		return $return_array ? $html : implode('&nbsp;', $html);
 	}
 	
	/**
	 * Returns the logo for a service 
	 *
	 * @param string $service
	 * @param array  $config
	 * @return LibBaseTemplateHelperHtmlElement
 	 */
 	public function login($service, $config = array())
 	{
 		$config = new KConfig($config);
 		
 		$config->append(array(
 			'html' 			=> $this->icon($service),
 			'url'			=> $this->url($service),
 			'attributes'	=> array()
 		));
 		
 		$html = $this->getService('com:base.template.helper.html');
 		
 		return $html->link($config->html, null, $config->attributes)
 		    ->dataSubmitUrl($config->url)
 		    ->class('btn btn-large btn-'.$service)
 		    ->dataTrigger('Submit')->title($service);
 	}

 	 /**
	 * Returns the url login for a service
	 *
	 * @param  string $service
	 * @return string 
 	 */
 	public function url($service)
 	{
 		$url = 'index.php?option=com_connect&view=login&server='.$service;
 		
 		if ( KRequest::get('get.return', 'cmd') ) {
 			$url .= '&return='.KRequest::get('get.return', 'raw') ;
 		}
 			
 		return JRoute::_($url);
 	}
 	
 	/**
	 * Returns the icons for a service
	 *
	 * @param  string $service
	 * @return LibBaseTemplateHelperHtmlElement 
 	 */
 	public function icon($service)
 	{
        return '<i class="icon-'.$service.'"></i>';
 	} 	
 	
 }