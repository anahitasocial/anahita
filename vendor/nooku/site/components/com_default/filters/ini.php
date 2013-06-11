<?php
/**
* @version      $Id: ini.php 4652 2012-05-21 18:19:26Z johanjanssens $
* @package      Nooku_Components
* @subpackage   Default
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link         http://www.nooku.org
*/

jimport('joomla.registry.format');
jimport('joomla.registry.format.ini');

/**
 * INI filter
 *
 * If the value being sanitized is a INI string it will be decoded, otherwise
 * the value will be encoded upon sanitisation.
 *
 * The format is non-standard, used by JRegistry as seen in JParameter
 *
 * @author      Stian Didriksen <stian@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 * @uses        JRegistryFormatINI
 */
class ComDefaultFilterIni extends KFilterAbstract
{
    /**
     * Constructor
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //Don't walk the incoming data array or object
        $this->_walk = false;
    }

    /**
     * Validate a value
     *
     * @param    scalar Value to be validated
     * @return   bool   True when the variable is valid
     */
    protected function _validate($value)
    {
        $handler = JRegistryFormat::getInstance('INI');
        return is_string($value) && !is_null($handler->stringToObject($value));
    }

    /**
     * Sanitize a value
     *
     * @param   scalar  Value to be sanitized
     * @return  string
     */
    protected function _sanitize($value)
    {
        $result  = null;

        if(!($value instanceof JRegistry))
        {
            $handler = JRegistryFormat::getInstance('INI');

            if($value instanceof KConfig) {
                $value = $value->toArray();
            }

            if(is_string($value)) {
                $result = $handler->stringToObject($value);
            }

            if(is_array($value)) {
                $value = (object) $value;
            }

            if(is_null($result)) {
                $result = $handler->objectToString($value, null);
            }
        }
        else $result = $value->toString('INI');

        return $result;
    }
}