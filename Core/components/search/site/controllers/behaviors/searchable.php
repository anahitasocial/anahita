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
 * search module for setting up a scope and owner 
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
	 * Constructor.
	 *
	 * @param KConfig $config An optional KConfig object with configuration options.
	 *
	 * @return void
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);		
	}	
	
	/**
	 * Sets the search owner context and scope automatically
	 * 
	 * @return void
	 */
	protected function _afterControllerGet()
	{
		if ( $this->_mixer->isIdentifiable() && !$this->isDispatched() ) 
		{			
			$scope = $this->_mixer->getIdentifier()->package.'.'.$this->_mixer->getIdentifier()->name;
			$scope = $this->getService('com://site/search.domain.entityset.scope')->find($scope);
			if ( $scope ) {
			    $this->getService()->set('mod://site/search.scope', $scope);
			}
			
			$item = $this->_mixer->getItem();
			if ( $item && $item->persisted() &&
			        $item->inherits('ComActorsDomainEntityActor') )
			{
			    $this->getService()->set('mod://site/search.owner', $item);
			    $this->getService()->set('mod://site/search.scope', null);
			}
			else if ( $this->getRepository()->isOwnable() && $this->actor ) {
			    $this->getService()->set('mod://site/search.owner', $this->actor);
			}			
		}
	}
}