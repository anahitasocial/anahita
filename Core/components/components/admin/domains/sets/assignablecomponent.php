<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Components
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * List of assignable components
 *
 * @category   Anahita
 * @package    Com_Components
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComComponentsDomainSetAssignableComponent extends KObject implements KServiceInstantiatable
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
			$query = $container->get('repos://site/components.component')
							->getQuery();
			
			$registry = $container->get('application.registry');
			$cached   = $registry->offsetExists('assignable-components'); 
			if ( $cached ) {
				$query->component($registry->offsetGet('assignable-components'));
			}
			
			//check the cache
			$container->get('repos://site/components.component')->getCommandChain()->disable();
			$components = $container->get('repos://site/components.component')
								->fetch($query, AnDomain::FETCH_ENTITY_LIST);
			
			$container->get('repos://site/components.component')->getCommandChain()->enable();
			
			if ( !$cached ) {
				$assignables = array();
				$names = array();
				foreach($components as $component) 
				{
					if ( $component->isAssignable() ) 
					{
						$names[] = $component->component;
						$assignables[] = $component;
					}
				}
				$components = $assignables;	
				$registry['assignable-components'] = $names;
			}
			
			$instance = $container->get('anahita:domain.entityset', array('data'=>$components,'repository'=>'repos://site/components.component'));			
			$container->set($config->service_identifier, $instance);			
		}
		
		return $container->get($config->service_identifier);				
	}
}

?>