<?php

/** 
 * LICENSE: ##LICENSE##
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
	 * Actor components
	 * 
	 * @var ComActorsDomainEntitysetComponent
	 */   
	protected $_components;
	  
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
		    'inheritance' => array('abstract'=>$this->getIdentifier()->classname == __CLASS__),
	        'attributes' => to_hash(array(
                'name'		=> array('required'=>AnDomain::VALUE_NOT_EMPTY, 'format'=>'string','read'=>'public'),
                'body'      => array('format'=>'string'),
                'status',
                'statusUpdateTime',
	        )),
	        'behaviors'  => to_hash(array(	                
                'subscribable',
                'modifiable',
                'storable',
                'describable',
                'authorizer',
                'privatable',
                'administrable',
                'enableable',
                'dictionariable',
                'followable',
                'portraitable'   => array(
                        'sizes'  => array(
                                'small'  => '80xauto',
                                'medium' => '160xauto',
                                'large'  => '480xauto',
                                'square' => 56 ))	                
	        )		        
        )));

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
		$this->statusUpdateTime = AnDomainAttributeDate::getInstance();		
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
	
	/**
	 * (non-PHPdoc)
	 * @see AnDomainEntityAbstract::__get()
	 */
	public function __get($name)
	{
		if ( $name == 'components' ) 
		{
			if ( !isset($this->_components) ) {
				$this->_components = $this->getService('com://site/actors.domain.entityset.component', array(
        				'actor' 		=> $this        				
				));
			}
			return $this->_components;
		} 
		else if ( $name == 'uniqueAlias' ) {
			return $this->get('id');
		}
		else {
			return parent::__get($name);
		}
	}
}