<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Search
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Search Scopes
 *
 * @category   Anahita
 * @package    Com_Search
 * @subpackage Domain_Entityset
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSearchDomainEntitysetScope extends KObjectArray implements KServiceInstantiatable
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
			 
			if ( !$registry->offsetExists('scopes') )
			{
				$components =  $container->get('repos://site/components.component')->fetchSet();
				$dispatcher = $container->get('koowa:event.dispatcher'); 
				$components->registerEventDispatcher($dispatcher);
				$event = new KEvent(array('scope'=>array()));				
				$dispatcher->dispatchEvent('onBeforeSearch', $event);	
				$scopes = new self();
				foreach($event->scope as $scope) 
				{
					$scope  = KConfig::unbox($scope);
					if ( is_array($scope) ) {
						$scope = $container->get('com://site/search.domain.entity.scope', $scope);
					}
					$scopes[$scope->getKey()] = $scope;
				}
				$registry->offsetSet('scopes', $scopes);
			}
						
			$container->set($config->service_identifier, $registry->offsetGet('scopes'));
		}
	
		return $container->get($config->service_identifier);
	}	

	/**
	 *
	 * @var int
	 */
	protected $_total;	
	
	/**
	 * Return total 
	 * 
	 * @return int
	 */
	public function getTotal()
	{
		return $this->_total;
	}
	
	/**
	 * Set the total of the scope
	 * 
	 * @param int $total
	 * 
	 * @return void
	 */
	public function setTotal($total)
	{
		$this->_total = $total;
	}
	
	/**
	 * Return a scope using a key or not if not found
	 * 
	 * @param string $scope
	 * 
	 * @return ComSearchDomainScope
	 */
	public function find($scope)
	{
		if ( strpos($scope,'.') === false ) {
			$scope = $scope.'.'.KInflector::singularize($scope);
		}
				
		if ( isset($this[$scope]) ) {
			return $this[$scope];
		}
		return null;
	}
}