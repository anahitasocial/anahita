<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Actor Node is the base node type represeting actionable nodes, like person, group, event and etc.
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsDomainEntityActor extends ComBaseDomainEntityNode
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
		    'abstract_identifier' => 'com:actors.domain.entity.actor', //actor is an abstract entity, can not be stored in database
			'attributes' => array(
				'name'		=> array('required'=>true, 'format'=>'string','read'=>'public'),
			    'body'      => array('format'=>'string'),
				'status',
				'statusUpdateTime',
			),
			'behaviors'  => array(
                'subscribable',
				'modifiable',
				'followable',
			    'storable',
				'portraitable',
				'describable',
				'authorizer',
				'dictionariable',
				'privatable',
                'administrable',
			    'enableable'                
			)
		));
		
		parent::_initialize($config);
	}
				
	/**
	 * Update a status of an actor
	 * 
	 * @param string $status The status update
	 *  
	 * @return void
	 */	
	public function setStatus($status)
	{
		$this->set('status', $status);
		$this->statusUpdatedTime = AnDomainAttributeDate::getInstance();		
	}

	/**
	 * Return the portrait file for a size
	 * 
	 * @see LibBaseDomainBehaviorPortraitable
	 * 
	 * @return string
	 */
	public function getPortraitFile($size)
	{
		if ( strpos($this->filename,'/') ) {
			 $filename = str_replace('/', '/avatars/'.$size, $this->filename);			 
		} else {			
			$filename = $this->component.'/avatars/'.$size.$this->filename;				
		}

		return $filename;
	}
}