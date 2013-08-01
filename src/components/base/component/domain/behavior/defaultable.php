<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Defultable Behavior. 
 * 
 * Allows to set an entity as the default entity within a set of entities
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainBehaviorDefaultable extends AnDomainBehaviorAbstract
{

    /**
     * A property whose value can be used as scope
     * 
     * @var array
     */
    protected $_scopes;
    
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
        
        $this->_scopes = $config['scopes'];
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
		    'scopes'      => array(),
			'attributes'  => array(
				'isDefault'=>array(
					'default' => false
				))
		));
		
		parent::_initialize($config);
	}
	
	/**
	 * Set the order before inserting
	 *
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _beforeEntityInsert(KCommandContext $context)
	{
	    //if the entity default is set to 
	    //true then, set the previous default entity to false
	    if ( $this->_mixer->isDefault === true )
	    {
	        $query = $this->getScopedQuery($context->entity);
		    $this->getRepository()->update(array('isDefault'=>false), $query);
	    }
	}
	
	/**
	 * Reorder After Update
	 *
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _beforeEntityUpdate(KCommandContext $context)
	{
	    //if default has changed
	    if ( $this->_mixer->getModifiedData()->isDefault )
	    {
	        $is_default = $this->_mixer->isDefault === true;
	        $query	    = $this->getScopedQuery($context->entity);
	        //if it's true, then reset all existing to false
	        if ( $is_default ) {
	            $this->getRepository()->update(array('isDefault'=>false), $query);
	        }
	        else {
	             $query->id($this->id,'<>')->limit(1);
	             $this->getRepository()->update(array('isDefault'=>true), $query);
	        }
	    }
	}
	
	/**
	 * Return the query after applying the scope
	 *
	 * @param AnDomainEntityAbstract $entity The  entity
	 *
	 * @return AnDomainQuery
	 */
	public function getScopedQuery($entity)
	{
	    $query = $this->getRepository()->getQuery();
	     
	    foreach($this->_scopes as $key => $value)
	    {
	        if ( is_numeric($key) ) {
	            $key   = $value;
	            $value = $entity->$key;
	        }
	        $query->where($key,'=',$value);
	    }
	    return $query;
	}
}