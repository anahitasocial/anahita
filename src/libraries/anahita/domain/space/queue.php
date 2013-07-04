<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Space
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Entity queue. Tracks a queue of entities plus groups entities using their repositories
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Space
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainSpaceQueue extends KObjectQueue 
{
	/**
	 * Array of entities segmented per repository
	 * 
	 * @var AnArrayObject
	 */
	protected $_repository_entities;
	
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */
	public function __construct(KConfig $config = null)
	{
		parent::__construct($config);

		$this->_repository_entities = new AnObjectArray();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see KObjectQueue::enqueue()
	 */
	public function enqueue( KObjectHandlable $object, $priority)
	{				
		$this->getRepositoryEntities($object->getRepository())->insert($object);
		
		return parent::enqueue($object, $priority);
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see KObjectQueue::dequeue()
	 */
	public function dequeue( KObjectHandlable $object)
    {
    	$this->getRepositoryEntities($object->getRepository())->extract($object);
    	
    	return parent::dequeue($object);
    }
    
    /**
     * Returns a collection of entityset of a repository
     * 
     * @param AnDomainRepositoryAbstract $repository The repositry whose entities must return
     * 
     * @return AnDomainEntityset
     */
    public function getRepositoryEntities($repository)
    {
    	if ( !isset($this->_repository_entities[$repository]) ) 
    	{
    		$this->_repository_entities[$repository] = 
    			$this->getService('anahita:domain.entityset', array('repository'=>$this));
    	}
    	    	
    	return $this->_repository_entities[$repository];
    }
}