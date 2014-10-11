<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Search
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Searchable behavior. The searchable behavior allows for other conrollers to integrate into the 
 * setup the scope and owner for the search layout 
 * 
 * 
 * @category   Anahita
 * @package    Com_Search
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSearchControllerBehaviorSearchable extends KControllerBehaviorAbstract
{	
	/**
	 * Sets the search owner context and scope automatically
	 * 
	 * @return void
	 */
	protected function _afterControllerGet()
	{
		if($this->_mixer->isIdentifiable() && $this->isDispatched()) 
		{			
			$item = $this->_mixer->getItem();
			$scope = $this->_mixer->getIdentifier()->package.'.'.$this->_mixer->getIdentifier()->name;
			$scope = $this->getService('com://site/components.domain.entityset.scope')->find($scope);
			
			if($scope)
			    $this->getService()->set('com://site/search.scope', $scope);
			
			if($item && $item->persisted() && $item->inherits('ComActorsDomainEntityActor'))
			{
			    $this->getService()->set('com://site/search.owner', $item);
			    $this->getService()->set('com://site/components.scope', null);
			}
			elseif($this->getRepository()->isOwnable() && $this->actor) 
			{
			    $this->getService()->set('com://site/search.owner', $this->actor);
			}			
		}
	}
}