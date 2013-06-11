<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Session entityset. Provides some nice API to get the correct session
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectDomainEntitysetSession extends AnDomainEntitysetDefault
{    
    /**
     * Return a session with a name
     * 
     * @param string $service The service name
     * 
     * @return ComConnectDomainEntitySession
     */
    public function getSessionApi($service)
    {   
        $session = $this->find(array('api'=>$service));
        if ( $session ) {
            return $session->api;
        }
    }
    
    /**
     * Forwards a non-property $key to the getSessionApi 
     * 
     * @return ComConnectDomainEntitySession
     */
    public function __get($key)
    {
        if ( !$this->_repository->getDescription()->getProperty($key) ) {
            return $this->getSessionApi($key); 
        }
        
        return parent::__get($key);
    }
}