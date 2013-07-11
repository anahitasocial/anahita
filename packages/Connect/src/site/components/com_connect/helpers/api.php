<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * API Helper Class to retreive consumer and service object
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
 class ComConnectHelperApi extends KObject
 {
    /**
     * Return an array of services
     * 
     * @return array
     */
    static public function getServices()
    {
        $avail_services = array('facebook','twitter','linkedin');
        $services = array();
        foreach($avail_services as $service)
        {
            $service = self::getAPI($service);
            if ( $service->enabled() ) {
                $services[$service->getIdentifier()->name] = $service;
            }
        }
        return $services;
    }
    
 	/**
 	 * Gets a consumer
 	 * 
 	 * @param  string $api
 	 * @return ComConnectOauthConsumer
 	 */
 	static public function getConsumer($api)
 	{
        $api      = strtolower($api);
		$key  	  = get_config_value('com_connect.'.$api.'_key');
		$secret   = get_config_value('com_connect.'.$api.'_secret');
		$consumer = new ComConnectOauthConsumer(new KConfig(array(
			'key'	=> $key, 
			'secret'=> $secret, 
			'callback_url'=> JURI::base().'components/com_connect/callback.php'
		)));
		return $consumer; 		
 	}
 	
 	/**
 	 * Get a service api
 	 *
 	 * @param  string $service The service name.
 	 * 
 	 * @return ComConnectOauthApiAbstract
 	 */
 	static public function getAPI($service)
 	{
        $service    = strtolower($service);
        $identifier = 'com://site/connect.oauth.service.'.$service;
        if ( !KService::has($identifier) )
        {
            $config   = array();
            $config['consumer'] = self::getConsumer($service);
            $config['enabled']  = self::enabled($service);
            KService::set($identifier, KService::get($identifier, $config));            
        }
		return 	KService::get($identifier);
 	}
        
    /**
     * Returns if a service is enabled
     *
     * @param string $service Service name
     * 
     * @return bool 
     */
    static public function enabled($service)
    {
        $service = strtolower($service);
        return get_config_value('connect.'.$service.'_enable', true) && 
         ComConnectHelperApi::getConsumer($service)->isValid();
    }    
 }