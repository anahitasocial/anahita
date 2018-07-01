<?php


 /**
  * API Helper Class to retreive consumer and service object.
  *
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */
 class ComConnectHelperApi extends KObject
 {
     /**
     * Return an array of services.
     *
     * @return array
     */
    public static function getServices()
    {
        $avail_services = array('facebook','twitter','linkedin');
        $services = array();
        foreach ($avail_services as $service) {
            $service = self::getAPI($service);
            if ($service->enabled()) {
                $services[$service->getIdentifier()->name] = $service;
            }
        }

        return $services;
    }

    /**
     * Gets a consumer.
     *
     * @param string $api
     *
     * @return ComConnectOauthConsumer
     */
    public static function getConsumer($api)
    {
        $api = strtolower($api);
        $key = get_config_value('com_connect.'.$api.'_key');
        $secret = get_config_value('com_connect.'.$api.'_secret');
        $consumer = new ComConnectOauthConsumer(new KConfig(array(
            'key' => $key,
            'secret' => $secret,
            'callback_url' => AnRequest::base(), 
        )));

        return $consumer;
    }

    /**
     * Get a service api.
     *
     * @param string $service The service name.
     *
     * @return ComConnectOauthApiAbstract
     */
    public static function getAPI($service)
    {
        $service = strtolower($service);
        $identifier = 'com:connect.oauth.service.'.$service;
        if (!KService::has($identifier)) {
            $config = array();
            $config['consumer'] = self::getConsumer($service);
            $config['enabled'] = self::enabled($service);
            KService::set($identifier, KService::get($identifier, $config));
        }

        return    KService::get($identifier);
    }

    /**
     * Returns if a service is enabled.
     *
     * @param string $service Service name
     *
     * @return bool
     */
    public static function enabled($service)
    {
        $service = strtolower($service);

        return get_config_value('connect.'.$service.'_enable', true) && self::getConsumer($service)->isValid();
    }
 }
