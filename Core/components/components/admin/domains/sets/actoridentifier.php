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
class ComComponentsDomainSetActorIdentifier extends KObject implements KServiceInstantiatable
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
			//check the cache
			$registry  = $container->get('application.registry');
			
			if ( !$registry->offsetExists('actor-identifiers') ) {
				$registry['actor-identifiers'] = self::_findActorIdentifiers($container);	
			}
			
			$identifiers = $registry['actor-identifiers'];
			
			$instance = $container->get('koowa:object.array', array('data'=>$identifiers));
			$container->set($config->service_identifier, $instance);
		}
		
		return $container->get($config->service_identifier);				
	}	
	
	/**
	 * Return an array of actor identifiers
	 *
	 * @return array
	 */
	static protected function _findActorIdentifiers(KServiceInterface $container)
	{		
		$components = $container->get('repos://admin/components.component')
						->getQuery()->enabled(true)
						->fetchSet();
		
		$components   = array_unique($container->get('repos://admin/components.component')
						->fetchSet()->component);
		
		$identifiers  = array();
		
		foreach($components as $component)
		{
			$path = JPATH_SITE.'/components/'.$component.'/domains/entities';
		
			if ( !file_exists($path) ) {
				continue;
			}
		
			//get all the files
			$files = (array)JFolder::files($path);
			//convert com_<Component> to ['com','<Name>']
			$parts      = explode('_', $component);
			$identifier = new KServiceIdentifier('com:'. substr($component, strpos($component, '_') + 1));
			$identifier->path = array('domain', 'entity');
		
			foreach($files as $file)
			{
				$identifier->name = substr($file, 0, strpos($file, '.'));
				try
				{
					if ( is($identifier->classname, 'ComActorsDomainEntityActor') ) {
						$identifiers[] = clone $identifier;
					}
				}
				catch(Exception $e) {  }
			}
		}	
		return $identifiers;	
	}
			
}

?>