<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Cachable Behavior 
 *
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainBehaviorCachable extends AnDomainBehaviorAbstract
{           
    /**
     * Global Query Cache.
     * 
     * @var ArrayObject
     */
    static protected $_cache;
    
    /**
     * Turn off/on cache
     * 
     * @var boolean
     */
    protected $_enable;
    
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
        
        if ( !self::$_cache ) {
            self::$_cache = new ArrayObject();
        }
        
        $this->_enable = $config->enable;     
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
            'enable'     => true,
            'priority'   => KCommand::PRIORITY_LOWEST
        ));
    
        parent::_initialize($config);
    }
    
    /**
     * Check the object cache see if the data has already been retrieved
     * 
     * This cache is only persisted throughout a request 
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    protected function _beforeRepositoryFetch(KCommandContext $context)
    {
        if ( $this->_enable )
        {
            $key = $this->_getCacheKey($context->query);
            
            if ( self::$_cache->offsetExists($key) )
            {
                $context->data = self::$_cache->offsetGet($key);
                return false;
            }            
        }
    }
    
    /**
     * Stores the objects in the cache. This cache is persisted during the
     * request life cycle
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    protected function _afterRepositoryFetch(KCommandContext $context)
    {
        if ( $this->_enable )
        {
            $key = $this->_getCacheKey($context->query);
            self::$_cache->offsetSet($key, $context->data);            
        }
    }    
    
    /**
     * Clean and disable the cahce before insert
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    protected function _beforeEntityInsert(KCommandContext $context)
    {
        self::$_cache->exchangeArray(array());
    }    
    
    /**
     * Clean and disable the cahce before delete
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    protected function _beforeEntityDelete(KCommandContext $context)
    {
        self::$_cache->exchangeArray(array());
    }    

    /**
     * Clean and disable the cahce before update
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    protected function _beforeEntityUpdate(KCommandContext $context)
    {
        self::$_cache->exchangeArray(array());
    }      
    
    /**
     * Enables the cache
     * 
     * @return void
     */
    public function enableCache()
    {
        $this->_enable = true;
    }
    
    /**
     * Disable the cache
     *
     * @return void
     */
    public function disableCache()
    {
        $this->_enable = false;
    }    
    
    /**
     * Empty the cache
     * 
     * @param AnDomainQuery $query
     * 
     * @return void
     */
    public function emptyCache($query)
    {
    	self::$_cache->offsetSet($this->_getCacheKey($query), null);    	
    }
    
    /**
     * Returns a key to use for cache 
     * 
     * @param AnDomainQuery $query
     * 
     * @return string
     */
    protected function _getCacheKey($query)
    {
    	return (string)$query;
    }
    
    /**
     * Return the handle
     *
     * @return string
     */
    public function getHandle()
    {
         return KMixinAbstract::getHandle();   
    }
}
