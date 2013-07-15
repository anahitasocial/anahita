<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Search
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Scope Object
 *
 * @category   Anahita
 * @package    Com_Search
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSearchDomainEntityScope extends KObject 
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
		
		$this->type		   = $config->type;
		
		$this->ownable 	   = $config->ownable;
		
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
		if ( $config->repository ) 
		{ 
			$config->append(array(
				'identifier'  	  => $config->repository->getDescription()->getInheritanceColumnValue()->getIdentifier(),
				'node_type'		  => (string)$config->repository->getDescription()->getInheritanceColumnValue(),
				'commentable'	  => $config->repository->isCommentable(),
				'ownable'	      => $config->repository->isOwnable()
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