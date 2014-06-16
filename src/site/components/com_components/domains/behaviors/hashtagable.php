<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Components
 * @subpackage Domain_Behavior
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Hashtagable behavior
 *
 * @category   Anahita
 * @package    Com_Components
 * @subpackage Domain_Behavior
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComComponentsDomainBehaviorHashtagable extends LibBaseDomainBehaviorEnableable
{
	/**
	 * Hashtag Scope
	 * 
	 * @var array
	 */
	protected $_hashtag_scope = array();
	
	/**
	 * Scope t
	 * 
	 * @var string
	 */
	protected $_scope_type;
	
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
		
		$this->_hashtag_scope = $config->class;		
		$this->_scope_type   = $config->type;
		
	}
	
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
			'type'  => null,
			'class' => null
		));
	
		parent::_initialize($config);
	}
	
	/**
	 * Cacthes the before hashtag
	 *
	 * @param KEvent $event
	 *
	 * @return void
	 */
	public function onBeforeHashtag(KEvent $event)
	{
		$event->scope->append($this->_mixer->getHashtagScope());
	}	
		
	/**
	 * Return the medium hashtag scopes
	 * 
	 * @return array
	 */
	public function getHashtagScope()
	{
		$hashtagables = array();
		
		foreach($this->getEntityRepositories($this->_hashtag_scope) as $repository)			
			$hashtagables[] = array('repository'=>$repository,'type'=>$this->_scope_type);
		
		return $hashtagables;
	}
}