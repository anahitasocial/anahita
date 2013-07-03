<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Domain
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Story Aggregation keys
 *   
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Domain
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesDomainAggregations extends KObjectArray implements KServiceInstantiatable
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
        if (!$container->has($config->service_identifier))
        {
            $registry   = $container->get('application.registry', array('key'=>$config->service_identifier));
        
            if ( !$registry->offsetExists('aggregations') )
            {
                $components   =	 $container->get('repos://site/components.component')
                                    ->fetchSet();
                
                $dispatcher = $container->get('koowa:event.dispatcher');
                $components->registerEventDispatcher($dispatcher);
                $aggregations = new KConfig();
                $event = new KEvent(array('aggregations'=>$aggregations));
                $dispatcher->dispatchEvent('onStoryAggregation', $event);
                $registry->offsetSet('aggregations', $aggregations);
            }
        
            $container->set($config->service_identifier, $registry->offsetGet('aggregations'));
        }
        
        return $container->get($config->service_identifier);        
    }    
}