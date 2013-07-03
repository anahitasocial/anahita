<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Components
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Searchable behavior
 *
 * @category   Anahita
 * @package    Com_Components
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComComponentsDomainBehaviorSearchable extends LibBaseDomainBehaviorEnableable
{
	/**
	 * Search Scope
	 * 
	 * @var array
	 */
	protected $_search_scope = array();
	
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
		
		$this->_search_scope = $config->class;		
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
	 * Cacthes the before search
	 *
	 * @param KEvent $event
	 *
	 * @return void
	 */
	public function onBeforeSearch(KEvent $event)
	{
		$event->scope->append($this->_mixer->getSearchScope());
	}	
		
	/**
	 * Return the medium search scopes
	 * 
	 * @return array
	 */
	public function getSearchScope()
	{
		$searchables = array();
		foreach($this->getEntityRepositories($this->_search_scope) as $repository) {			
			$searchables[] = array('repository'=>$repository,'type'=>$this->_scope_type);
		}
		return $searchables;
	}
}