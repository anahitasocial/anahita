<?php
 
/**
 * Array Helper.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @author     Johan Janssens <johan@nooku.org>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnHelperArray
{
    /**
     * Index flags.
     */
    const LAST_INDEX = PHP_INT_MAX;
    const FIRST_INDEX = PHP_INT_MIN;

    /**
     * Index an object (or an array) using one of it's attribute.
     *
     * @param array  $items An array of object or associative array
     * @param string $key   Attribute by which to index the array
     *
     * @return array
     */
    public static function indexBy($items, $key)
    {
        $array = array();

        foreach ($items as $item) {
            $array[self::getValue($item, $key)] = $item;
        }

        return $array;
    }

    /**
     * Collects $key from an array of items.
     *
     * @param array        $items An array of object or associative array
     * @param string|array $key   The key to collect the value for
     *
     * @return array
     */
    public static function collect($items, $key)
    {
        $array = array();

        foreach ($items as $v) {
            if (is_array($key)) {
                foreach ($key as $index => $k) {
                    if (!isset($array[$k])) {
                        $array[$k] = array();
                    }
                    $array[$k][] = self::getValue($v, $k);
                }
            } else {
                $array[] = self::getValue($v, $key);
            }
        }

        return $array;
    }

    /**
     * Groups an array of items by their common $key.
     *
     * @param array  $items An array of object or associative array
     * @param string $key   Attribute by which to index the array
     *
     * @return array
     */
    public static function groupBy($items, $key)
    {
        $array = array();

        foreach ($items as $item) {
            $value = self::getValue($item, $key);
            if (!isset($array[$value])) {
                $array[$value] = array();
            }
            $array[$value][] = $item;
        }

        return $array;
    }

    /**
     * Return a unique array of $array. This method also handles object as value.
     *
     * @param array $array An Array
     *
     * @return array
     */
    public static function unique($array)
    {
        $unique = array();
        foreach ($array as $item) {
            if (!in_array($item, $unique, true)) {
                $unique[] = $item;
            }
        }

        return $unique;
    }

    /**
     * Insert into an array. If no offset is given then the values
     * are inserted at the end of the list. Returns the new array with
     * value inserted into.
     *
     * @param array $array  The orignal array
     * @param array $values An array of values to be inserted
     *
     * @return array
     */
    public static function insert($array, $values, $index = null)
    {
        $values = (array) KConfig::unbox($values);
        $array = (array) KConfig::unbox($array);
        if ($index === null) {
            foreach ($values as $value) {
                array_push($array, $value);
            }
        } else {
            array_splice($array, $index, 0, $values);
        }

        return $array;
    }

    /**
     * Unset a list of values from an array. This method both unset any key that exists
     * in the $values array as well as any values that exists in the $values array. This method
     * modifies the $array object.
     *
     * @param array $array An Array values to unset
     *
     * @return array
     */
    public static function unsetValues($array, $values)
    {
        settype($values, 'array');

        foreach ($array as $index => $item) {
            foreach ($values as $value) {
                if (!is_numeric($index)) {
                    $item = $index;
                }

                if ($value == $item) {
                    unset($array[$index]);
                }
            }
        }

        return $array;
    }

    /**
     * Flattens a multi-dimensial array and return all the values as one single array.
     *
     * @param array $array The array to be flattened
     *
     * @return array
     */
    public static function getValues($array)
    {
        settype($array, 'array');
        $values = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $values = array_merge($values, self::getValues($value));
            } else {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * Return the simple scalar array of the object.
     *
     * @param mixed $object An object to be converted to an array
     *
     * @return array
     */
    public static function toArray($object)
    {
        $object = KConfig::unbox($object);

        if (is($object, 'KObjectArray') || is($object, 'KObjectSet')) {
            return $object->toArray();
        }

        return (array) $object;
    }

    /**
     * Get the value of an item (array|object) using a $key. The $key can be a string path.
     *
     * @param object $item The object whose attribute value is being returend
     * @param string $key  The attribute name
     *
     * @return mixed
     */
    public static function getValue($item, $key)
    {
        $parts = explode('.', $key);
        $value = $item;
        foreach ($parts as $part) {
            if ($value) {
                if (is($value, 'KObject')) {
                    $value = $value->get($part);
                } else {
                    $value = is_array($value) ? $value[$part] : $value->$part;
                }
            }
        }

        return $value;
    }

    /**
     * Return an interator for an object.
     *
     * @param mixed $object An Iteratable or NonInterable object
     *
     * @return Iteratorable
     */
    public static function getIterator($object)
    {
        if (!self::isIterable($object)) {
            $object = array($object);
        }

        return $object;
    }

    /**
     * Get the value at an index. The index can be an integer or 'first' or 'last'. If the
     * index doesn't exists it returns null.
     *
     * @param array $array
     * @param mixed $index
     */
    public static function getValueAtIndex($array, $index)
    {
        $value = null;

        if (abs((int) $index) == self::LAST_INDEX) {
            $index == self::LAST_INDEX ? end($array) : reset($array);
            $value = current($array);
        } elseif (isset($array[$index])) {
            $value = $array[$index];
        }

        return $value;
    }

    /**
     * Return true if the array is some of kind of iterative array.
     *
     * @param array $array An array of object or associative array
     *
     * @return bool
     */
    public static function isIterable($array)
    {
        return is_array($array) || $array instanceof Iterator;
    }
    
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
     * AnHelperArray::merge(array('key' => 'org value'), array('key' => 'new value'));
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
                    $output[] = AnHelperArray::toString($item, $inner_glue, $outer_glue, $keepOuterKey);
                }
                else $output[] = $key.$inner_glue.'"'.str_replace('"', '&quot;', $item).'"';
            }
        }

        return implode($outer_glue, $output);
    }
}
