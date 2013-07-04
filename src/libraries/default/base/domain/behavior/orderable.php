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
 * Orderable Behavior 
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Domain_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseDomainBehaviorOrderable extends AnDomainBehaviorAbstract
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
				'ordering'=>array('default'=>0)
			),
			'aliases' => array(
				'order' => 'ordering'
			)
		));
		
		parent::_initialize($config);
	}
	
	/**
	 * Before Update
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _beforeEntityUpdate(KCommandContext $context)
	{
		if ( $this->getModifiedData()->ordering ) {
			
			$store    = $this->getRepository()->getStore();
			$query	  = $this->getScopedQuery($context->entity);
			$change   = $this->getModifiedData()->ordering;
		
			if( $change->new - $change->old < 0 ) 
			{
				$query->update('@col(ordering) = @col(ordering) + 1');
				$query->where('ordering', '>=',  $change->new)
					  ->where('ordering', '<',   $change->old);
			} 
			else 
			{
				$query->update('@col(ordering) = @col(ordering) - 1');
				$query->where('ordering', '>',   $change->old)
					  ->where('ordering', '<=',  $change->new);
			}
						
			$store->execute($query);
		}		
	}
	
	/**
	 * Reorders all the entities
	 * 
	 * @return void
	 */
	public function reorder()
	{
		$store    = $this->getRepository()->getStore();
		$query 	  = $this->getScopedQuery($this->_mixer);
		$store->execute('SET @order = 0');
		$query->update('@col(ordering) = (@order := @order + 1)')->order('ordering', 'ASC');
		$store->execute($query);
	}
	
	/**
	 * Set the order before inserting
	 * 
	 * @param  KCommandContext $context
	 * @return void
	 */
	protected function _beforeEntityInsert(KCommandContext $context)
	{
		$max = $this->getScopedQuery($context->entity)
		        ->fetchValue('MAX(@col(ordering))');
		$this->ordering = $max + 1;
	}
	
	/**
	 * Reorder After Update
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _afterEntityUpdate(KCommandContext $context)
	{
		if ( $this->getModifiedData()->ordering )
			$this->reorder();
	}
	
	/**
	 * Reorder After Delete
	 * 
	 * @param KCommandContext $context
	 * @return void
	 */
	protected function _afterEntityDelete(KCommandContext $context)
	{
		$this->reorder();
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

?>