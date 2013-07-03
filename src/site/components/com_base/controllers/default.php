<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Service Controller
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
final class ComBaseControllerDefault extends ComBaseControllerService implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton
     *
     * @param KConfigInterface 	$config    An optional KConfig object with configuration options
     * @param KServiceInterface	$container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {        
        $strIdentifier = (string)$config->service_identifier;
        $registery     = $container->get('application.registry', array('key'=>$strIdentifier.'_default_class'));        
        if ( !$registery->offsetExists($strIdentifier) )
        {
            try {
                $identifier          = clone $config->service_identifier;
                $identifier->type    = 'repos';
                $identifier->path = array('domain','entity');
                $default  = array('prefix'=>$container->get($identifier)->getClone(), 'fallback'=>'ComBaseControllerDefault');
            }
            catch(Exception $e) {
                $default = 'Com'.ucfirst($config->service_identifier->package).'ControllerDefault';
                $default = array('default'=>array($default, 'ComBaseControllerResource'));
            }
            $default['identifier'] = $config->service_identifier;
            register_default($default);
            $classname = AnServiceClass::findDefaultClass($config->service_identifier);
            $config->service_identifier->classname = $classname;
            $registery->offsetSet($strIdentifier, $classname);
        }
        $classname = $registery->offsetGet($strIdentifier);        
        $instance  = new $classname($config);
        return $instance;       
    }
}