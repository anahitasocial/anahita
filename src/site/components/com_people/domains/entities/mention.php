<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_People
 * @subpackage Domain_Entity
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Person tag   
 * 
 * @category   Anahita
 * @package    Com_People
 * @subpackage Domain_Entity
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
final class ComPeopleDomainEntityMention extends ComBaseDomainEntityEdge
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
				'mention' => array('parent'=>'com:people.domain.entity.person'),
				'mentionable' => array('parent'=>'com:base.domain.entity.node')			
			),
        	'aliases' => array(
                'mention' => 'nodeA',
        		'mentionable' => 'nodeB'
            )
        ));
        
        parent::_initialize($config);           
    }
}