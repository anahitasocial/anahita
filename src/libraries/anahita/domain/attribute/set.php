<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * A set data type. A comma seperated list of values.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnDomainAttributeSet extends AnObjectArray implements AnDomainAttributeInterface, Countable
{
    /**
     * Set the set data.
     *
     * @param array $data The set data
     */
    public function setData(array $data)
    {
        if (count($data)) {
            $data = array_combine($data, $data);
        }

        $this->_data = $data;

        return $this;
    }

    /**
     * overwrites the offsetSet. In a set attribute both they key/value are 
     * the passed value.
     * 
     * @return AnDomainAttributeSet
     */
    public function offsetSet($key, $value)
    {
        $this->_data[$value] = $value;
    }

    /**
     * Return the count.
     * 
     * @return int
     */
    public function count()
    {
        return count($this->_data);
    }

    /**
     * Instantiate the set attribute with a comma seperate list of value.
     *
     * @param string $data A comma seperated list of values
     * 
     * @return AnDomainAttributeSet
     */
    public function unserialize($data)
    {
        if ($data) {
            $data = explode(',', pick($data, ''));
            $data = array_combine($data, $data);
        } else {
            $data = array();
        }

        $this->_data = $data;

        return $this;
    }

    /**
     * Return a string date.
     * 
     * @return string
     */
    public function serialize()
    {
        return implode(',', array_unique($this->_data));
    }
}
