<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Cachable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnDomainBehaviorCachable extends AnDomainBehaviorAbstract
{
    /**
     * Global Query Cache.
     * 
     * @var ArrayObject
     */
    protected static $_cache;

    /**
     * Turn off/on cache.
     * 
     * @var bool
     */
    protected $_enable;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        if (! self::$_cache) {
            self::$_cache = new ArrayObject();
        }

        $this->_enable = $config->enable;
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'enable' => true,
            'priority' => AnCommand::PRIORITY_LOWEST,
        ));

        parent::_initialize($config);
    }

    /**
     * Check the object cache see if the data has already been retrieved.
     * 
     * This cache is only persisted throughout a request 
     *
     * @param AnCommandContext $context
     */
    protected function _beforeRepositoryFetch(AnCommandContext $context)
    {
        if ($this->_enable) {
            $key = $this->_getCacheKey($context->query);

            if (self::$_cache->offsetExists($key)) {
                $context->data = self::$_cache->offsetGet($key);

                return false;
            }
        }
    }

    /**
     * Stores the objects in the cache. This cache is persisted during the
     * request life cycle.
     *
     * @param AnCommandContext $context
     */
    protected function _afterRepositoryFetch(AnCommandContext $context)
    {
        if ($this->_enable) {
            $key = $this->_getCacheKey($context->query);
            self::$_cache->offsetSet($key, $context->data);
        }
    }

    /**
     * Clean and disable the cahce before insert.
     *
     * @param AnCommandContext $context
     */
    protected function _beforeEntityInsert(AnCommandContext $context)
    {
        self::$_cache->exchangeArray(array());
    }

    /**
     * Clean and disable the cahce before delete.
     *
     * @param AnCommandContext $context
     */
    protected function _beforeEntityDelete(AnCommandContext $context)
    {
        self::$_cache->exchangeArray(array());
    }

    /**
     * Clean and disable the cahce before update.
     *
     * @param AnCommandContext $context
     */
    protected function _beforeEntityUpdate(AnCommandContext $context)
    {
        self::$_cache->exchangeArray(array());
    }

    /**
     * Enables the cache.
     */
    public function enableCache()
    {
        $this->_enable = true;
    }

    /**
     * Disable the cache.
     */
    public function disableCache()
    {
        $this->_enable = false;
    }

    /**
     * Empty the cache.
     * 
     * @param AnDomainQuery $query
     */
    public function emptyCache($query)
    {
        self::$_cache->offsetSet($this->_getCacheKey($query), null);
    }

    /**
     * Returns a key to use for cache.
     * 
     * @param AnDomainQuery $query
     * 
     * @return string
     */
    protected function _getCacheKey($query)
    {
        return (string) $query;
    }

    /**
     * Return the handle.
     *
     * @return string
     */
    public function getHandle()
    {
        return AnMixinAbstract::getHandle();
    }
}
