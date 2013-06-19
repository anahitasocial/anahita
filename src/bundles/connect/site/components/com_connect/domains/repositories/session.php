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
 * Session object. After an actor has been authenticated, session store its authentication
 * token/value
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectDomainRepositorySession extends AnDomainRepositoryDefault
{
    /**
     * Modify session query to only bring the sessions that are available
     * 
     * @return void
     */
    protected function _beforeRepositoryFetch(KCommandContext $context)
    {
        $query    = $context->query;
        $services = array_keys(ComConnectHelperApi::getServices());
        $query->api($services);        
    }
}