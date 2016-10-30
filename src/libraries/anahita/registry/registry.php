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
 * @link       http://www.GetAnahita.com
 */

/**
 * Memory based registry.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnRegistry extends ArrayObject
{
    /**
     * Cache.
     *
     * @var bool
     */
    protected $_cache = false;

    /**
     * Cache Prefix.
     *
     * @var bool
     */
    protected $_cache_prefix = 'koowa-cache-loader';

    /**
     * Enable class caching.
     *
     * @param  bool	Enable or disable the cache. Default is TRUE.
     *
     * @return bool TRUE if caching is enabled. FALSE otherwise.
     */
    public function enableCache($enabled = true)
    {
        if ($enabled && extension_loaded('apcu')) {
            $this->_cache = true;
        } else {
            $this->_cache = false;
        }

        return $this->_cache;
    }

    /**
     * Set the cache prefix.
     *
     * @param string The cache prefix
     */
    public function setCachePrefix($prefix)
    {
        $this->_cache_prefix = $prefix;
    }

    /**
     * Get the cache prefix.
     *
     * @return string The cache prefix
     */
    public function getCachePrefix()
    {
        return $this->_cache_prefix;
    }

    /**
     * Get an item from the array by offset.
     *
     * @param   int     The offset
     *
     * @return mixed The item from the array
     */
    public function offsetGet($offset)
    {
        if (!parent::offsetExists($offset)) {
            if ($this->_cache) {
                $result = apcu_fetch($this->_cache_prefix.'-'.$offset);
            } else {
                $result = false;
            }
        } else {
            $result = parent::offsetGet($offset);
        }

        return $result;
    }

    /**
     * Set an item in the array.
     *
     * @param   int     The offset of the item
     * @param   mixed   The item's value
     *
     * @return object KObjectArray
     */
    public function offsetSet($offset, $value)
    {
        if ($this->_cache) {
            apcu_store($this->_cache_prefix.'-'.$offset, $value);
        }

        parent::offsetSet($offset, $value);
    }

    /**
     * Check if the offset exists.
     *
     * @param   int     The offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        if (false === $result = parent::offsetExists($offset)) {
            if ($this->_cache) {
                $result = apcu_exists($this->_cache_prefix.'-'.$offset);
            }
        }

        return $result;
    }

    /**
     * (non-PHPdoc).
     *
     * @see ArrayObject::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        if ($this->_cache) {
            apcu_delete($this->_cache_prefix.'-'.$offset);
        }

        return parent::offsetUnset($offset);
    }

    /**
     * Load all the existing key/value into the memory.
     */
    public function deleteAll()
    {
        clean_apc_with_prefix($this->getCachePrefix());
    }
}
