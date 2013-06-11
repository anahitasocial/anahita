<?php
/**
 * @version     $Id: array.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Koowa_Helper
 * @subpackage	Array
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Array helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Helper
 * @subpackage  Array
 * @static
 */
class KHelperArray
{
    /**
     * Typecast each element of the array. Recursive (optional)
     *
     * @param   array   Array to typecast
     * @param   string  Type (boolean|int|float|string|array|object|null)
     * @param   boolean Recursive
     * @return  array
     */
    public static function settype(array $array, $type, $recursive = true)
    {
        foreach($array as $k => $v)
        {
            if($recursive && is_array($v)) {
                $array[$k] = self::settype($v, $type, $recursive);
            } else {
                settype($array[$k], $type);
            }
        }
        return $array;
    }

    /**
     * Count array items recursively
     *
     * @param   array
     * @return  int
     */
    public static function count(array $array)
    {
        $count = 0;

        foreach($array as $v){
            if(is_array($v)){
                $count += self::count($v);
            } else {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Merge two arrays recursively
     *
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * KHelperArray::merge(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function and the datatypes of the values in the arrays are unchanged.
     *
     * @param array
     * @param array
     * @return array    An array of values resulted from merging the arguments together.
     */
    public static function merge( array &$array1, array &$array2 )
    {
        $args   = func_get_args();
        $merged = array_shift($args);

        foreach($args as $array)
        {
            foreach ( $array as $key => &$value )
            {
                if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ){
                    $merged [$key] = self::merge ( $merged [$key], $value );
                } else {
                    $merged [$key] = $value;
                }
            }
        }

        return $merged;
    }

    /**
     * Extracts a column from an array of arrays or objects
     *
     * @param   array   List of arrays or objects
     * @param   string  The index of the column or name of object property
     * @return  array   Column of values from the source array
     */
    public static function getColumn(array $array, $index)
    {
        $result = array();

        foreach($array as $k => $v)
        {
            if(is_object($v)) {
                $result[$k] = $v->$index;
            } else {
                $result[$k] = $v[$index];
            }
        }

        return $result;
    }

    /**
     * Utility function to map an array to a string
     *
     * @static
     * @param   array|object    The array or object to transform into a string
     * @param   string          The inner glue to use, default '='
     * @param   string          The outer glue to use, default ' '
     * @param   boolean
     * @return  string  The string mapped from the given array
     */
    public static function toString($array = null, $inner_glue = '=', $outer_glue = ' ', $keepOuterKey = false)
    {
        $output = array();

        if($array instanceof KConfig)
        {
            $data = array();
            foreach($array as $key => $item)
            {
                $data[$key] = (string) $item;
            }
            $array = $data;
        }

        if(is_object($array)) {
            $array = (array) KConfig::unbox($array);
        }

        if(is_array($array))
        {
            foreach($array as $key => $item)
            {
                if(is_array($item))
                {
                    if($keepOuterKey) {
                        $output[] = $key;
                    }

                    // This is value is an array, go and do it again!
                    $output[] = KHelperArray::toString($item, $inner_glue, $outer_glue, $keepOuterKey);
                }
                else $output[] = $key.$inner_glue.'"'.str_replace('"', '&quot;', $item).'"';
            }
        }

        return implode($outer_glue, $output);
    }
}