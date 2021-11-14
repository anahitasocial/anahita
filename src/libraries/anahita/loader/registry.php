<?php
/**
 * @package     Anahita_Loader
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright   Copyright (C) 2018 Rastin Mehr. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 * @link        https://www.Anahita.io
 */
 
 
class AnLoaderRegistry extends ArrayObject
{
    /**
     * Cache
     *
     * @var boolean
     */
    protected $_cache = false;

    /**
     * Cache Prefix
     *
     * @var boolean
     */
    protected $_cache_prefix = 'anahita-cache-loader';

    /**
     * Enable class caching
     *
     * @param  boolean	Enable or disable the cache. Default is TRUE.
     * @return boolean	TRUE if caching is enabled. FALSE otherwise.
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
     * Set the cache prefix
     *
     * @param string The cache prefix
     * @return void
     */
    public function setCachePrefix($prefix)
    {
        $this->_cache_prefix = $prefix;
    }

    /**
     * Get the cache prefix
     *
     * @return string	The cache prefix
     */
    public function getCachePrefix()
    {
        return $this->_cache_prefix;
    }

    /**
     * Get an item from the array by offset
     *
     * @param   int     The offset
     * @return  mixed   The item from the array
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
     * Set an item in the array
     *
     * @param   int     The offset of the item
     * @param   mixed   The item's value
     * @return  object  AnObjectArray
     */
    public function offsetSet($offset, $value)
    {
        if ($this->_cache) {
            apcu_store($this->_cache_prefix.'-'.$offset, $value);
        }

        parent::offsetSet($offset, $value);
    }

    /**
     * Check if the offset exists
     *
     * @param   int     The offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        if (false === $result = parent::offsetExists($offset)) {
            if ($this->_cache) {
                $result = apcu_exists($this->_cache_prefix.'-'.$offset);
            }
        }

        return (bool) $result;
    }
}
