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
 * JSON Class to represent a JSON object.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnDomainAttributeJson extends KConfig implements AnDomainAttributeInterface
{
    /**
     * Instantiate the json attribute with JSON values.
     *
     * @param string $data The string represntation of a json value
     *
     * @return AnDomainAttributeJson
     */
    public function unserialize($data)
    {
        $value = @json_decode($data, true);
        
        if (!$value) {
            $value = array();
        }

        $this->append($value);
    }

    /**
     * Return a string date.
     *
     * @return string
     */
    public function serialize()
    {
        return json_encode($this->_data);
    }
}
