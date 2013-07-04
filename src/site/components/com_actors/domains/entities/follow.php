<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Social Edge represents an edge between two nodes that will be used to the act of nodeA following 
 * activities of nodeB. This edge maybe used to find relevant stories for nodeA from the nodes it is
 * following. 
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsDomainEntityFollow extends ComBaseDomainEntityEdge
{
	/**
    * Initializes the default configuration for the object
    *
    * Called from {@link __construct()} as a first step of object instantiation.
    *
    * @param KConfig $config An optional KConfig object with configuration options.
    *
    * @return void
    */
    protected function _initialize(KConfig $config)
    {
		$config->append(array(
			'relationships' => array(
				'follower' 	 => array('parent'=>'com:actors.domain.entity.actor'),
				'leader' 	 => array('parent'=>'com:actors.domain.entity.actor')			
			),
			'aliases' => array(
				'follower' => 'nodeA',
				'leader'   => 'nodeB'								
			)
		));
			
		parent::_initialize($config);
	}	
}