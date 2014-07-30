<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Components
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Scope Object
 *
 * @category   Anahita
 * @package    Com_Components
 * @subpackage Domain_Entity
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComComponentsDomainEntityScope extends KObject 
{
	/**
	 * The entity type
	 *
	 * @var string
	 */
	public $node_type;
	
	/**
	 * Scope type. Can be posts, actors or others
	 * 
	 * @var string
	 */
	public $type;
	
	/**
	 * The entity identifier
	 *
	 * @var KIdentifier
	 */
	public $identifier;
	
	/**
	 * A flag whether to scope is commetnable
	 *
	 * @var boolean
	 */
	public $commentable;
	
	/**
	 * A flag whether to scope is ownable
	 *
	 * @var boolean
	 */
	public $ownable;
	
	/**
	 * A flag whether to scope is hashtagable
	 *
	 * @var boolean
	 */
	public $hashtagable;
	
	/**
	 * Returns how many result count there are per scope
	 * 
	 * @var int
	 */
	public $result_count;
		
	/**
	 * Constructor.
	 * 
	 * If a repository is passed, the scope can guess some of the values
	 *
	 * @param KConfig $config An optional KConfig object with configuration options.
	 *
	 * @return void
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
		$this->identifier = $config->identifier;
		
		$this->node_type  = $config->node_type;
		
		$this->commentable = $config->commentable;
		
		$this->type = $config->type;
		
		$this->ownable = $config->ownable;
		
		$this->hashtagable = $config->hashtagable;
		
		JFactory::getLanguage()->load('com_'.$this->identifier->package);
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
		if($config->repository) 
		{ 
			$config->append(array(
				'identifier' => $config->repository->getDescription()->getInheritanceColumnValue()->getIdentifier(),
				'node_type' => (string) $config->repository->getDescription()->getInheritanceColumnValue(),
				'commentable' => $config->repository->isCommentable(),
				'ownable' => $config->repository->isOwnable(),
				'hashtagable' => $config->repository->isHashtagable()
			));
		}
			
		parent::_initialize($config);
	}
	
	/**
	 * wakes up
	 */
	public function __wakeup()
	{
		JFactory::getLanguage()->load('com_'.$this->identifier->package);
	}
	
	/**
	 * The package and name portion of the identifier concatinated together using a dot
	 * 
	 * @return string
	 */
	public function getKey()
	{
		return $this->identifier->package.'.'.$this->identifier->name;
	}
}