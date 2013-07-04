<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Medium Nodes. Represents medium entities. most of the assets nodes in a social network is
 * a subclass of a medium node 
 * 
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMediumDomainEntityMedium extends ComBaseDomainEntityNode 
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
            'inheritance' => array('abstract'=>$this->getIdentifier()->classname === __CLASS__),		       
		    'relationships'  => array(
		          'author' => array('parent'=>'com:people.domain.entity.person', 'child_column'=>'created_by', 'required'=>true),      
            ),
		    'attributes'  => array(
                'name'=>array('read'=>'public')
             ),
			'behaviors'	  => array(                            
				'votable',
				'authorizer', 
				'privatable', 
				'ownable',
				'dictionariable', 
				'subscribable',
				'describable'
			)
		));
		
        $behaviors = $config->behaviors;
        
        $behaviors->append(array(
//            'taggable'    => array('parse_hashtag'=>true) ,
            'modifiable'  => array(
                'modifiable_properties' => array('name','body')
            ),
            'commentable' => 
                array('comment'=>array('length'=>5000,'format'=>'post'))
        ));
        
		parent::_initialize($config);
	}
}