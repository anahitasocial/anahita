<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Topics
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Topic Entity
 *
 * @category   Anahita
 * @package    Com_Topics
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComTopicsDomainEntityTopic extends ComMediumDomainEntityMedium 
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
			'resources'		=> array('topics_topics'),
			'attributes' 	=> array(
			    'name'		=> array('required'=>AnDomain::VALUE_NOT_EMPTY),
			    'body'		=> array('required'=>AnDomain::VALUE_NOT_EMPTY),
				'isSticky'  => array('column'=>'sticky', 'type'=>'boolean', 'default'=>false,'required'=>true)
			),
			'behaviors' => array(
				'hittable'
			),
		));
		
		return parent::_initialize($config);		
	}
}