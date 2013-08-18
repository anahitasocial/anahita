<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Lots of cool functions 
 */

/**
 * Prints deprecated messages
 *
 * @param string $msg Message to display as deprecated
 *
 * @return void
 */
function deprecated($msg = null)
{
    $traces = debug_backtrace();

    array_shift($traces);

    $trace = array_shift($traces);

    $called_method = '';

    if ( isset($trace['class']) ) {
        $called_method = $trace['class'].'::';
    }
    if ( isset($trace['function']))
        $called_method .= $trace['function'].'()';

    $trace = array_shift($traces);

    $calling_method = '';

    if ( isset($trace['class']) ) {
        $calling_method = $trace['class'].'::';
    }

    if ( isset($trace['function']) && $trace['function'] != 'include') {
        $calling_method .= $trace['function'].'()';
    }
    if ( empty($calling_method) && isset($trace['file']) ) {
        if ( isset($trace['function']) && $trace['function'] == 'include') {
            $calling_method = $trace['args'][0];
        } else
            $calling_method = $trace['file'];
    }

    if ( isset($trace['line']) )
        $calling_method = $calling_method.' line:'.$trace['line'];
    //called from $calling_method
    $message = "$called_method has been deprecated";
    if ( $msg )
        $message .= '. '.$msg;

    trigger_error($message, E_USER_WARNING);
}

function _die($message='')
{
    trigger_error('DIE :'.$message, E_USER_WARNING);
    die;
}

/**
 *
 * Block functions Capture a block of text and pass it to a method
 *
 */

/**
 * Global stack of all the blocks
 *
 * @var Array
 */
global $__blocks;

$__blocks = array();

/**
 * Start caputring
 *
 * @return void
 */
function capture()
{
    global $__blocks;

    $args = func_get_args();

    if ( count($args) > 0 ) {
        $__blocks[] = $args;
    }

    ob_start();
}

/**
 * End Capture. Returns the capture body
 *
 * @return string
 */
function end_capture()
{
    global $__blocks;

    $body = ob_get_contents();

    ob_end_clean();

    if ( count($__blocks) )
    {
        $args 	= array_pop($__blocks);
        $method = array_shift($args);
        $args[] = $body;
        return call_user_func_array($method, $args);
    }

    return $body;
}

/**
 * Return true if all the values are the same
 *
 * @return boolean
 */
function is_eql()
{
    $values = func_get_args();
    if ( count($values) == 1)
        return true;
    elseif ( count($values) == 2 ) {
        $v1 = $values[0];
        $v2 = $values[1];
        if ( $v1 instanceof KObject && $v1->inherits('AnDomainEntityAbstract') )
            return $v1->eql($v2);
        if ( $v1 instanceof AnDomainAttributeInterface )
            return $v1 == $v2;
        else {
            return $v1 === $v2;
        }
    } else {
        for($i=0;$i<count($values)-2;$i++)	{
            for($j=1;$j<count($values)-1;$j++) {
                if ( is_eql($values[$i], $values[$j])  === false )
                    return false;
            }
        }
        return true;
    }
}

/**
 * Return whether object is an instnaces of one the $classes passes as set of arguments
 *
 * @return boolean
 */
function is()
{
    $classes 	= func_get_args();
    $object 	= array_shift($classes);
    foreach($classes as $class)
    {
        if ( $object instanceof KObject )
        {
            $ret = $object->inherits($class);
        }
        elseif (is_object($object))
        {
            $ret = $object instanceof $class;
        }
        elseif (is_string($object) && !in_array($class, array('boolean','integer','double','string','array','object','resource')) && class_exists($object))
        {
            $ret = $object == $class || is_subclass_of($object,$class) || in_array($class, class_implements($object));
        }
        else
        {
            $ret = gettype($object) == strtolower($class);
        }

        if ( $ret === true )
            return true;
    }

    return false;
}

/**
 * When __toString throws error it's a headahce for debuggin
 * this method safely converts an object to string that if it
 * throws an error it can be caught 
 * 
 * @param mixed $object
 */
function to_str($object)
{
    if ( is_object($object) && 
            is_callable(array($object, '__toString') ))
    {
        $string = $object->__toString();
    } else 
        $string = (string)$object;
            
    return $string;
}

/**
 * Check if a value is within range of $min, $max
 *
 * @param int $value         An integer value
 * @param int $min           The maximum of the range
 * @param int $max           The minimum of the range
 * @param boolean $inclusive Boolean flag to whether consider the max as part of the range
 *
 * @return boolean
 */
function in_range($value, $min, $max, $inclusive = true)
{
    if ( $value >= $min && $value <= $max)
    {
        if ( !$inclusive )  {
            if ( $value == $max )
                return false;
        }

        return true;
    }

    return false;
}

/**
 * Picks the first non-null value of a set of arguments
 *
 * @return mixed
 */
function pick()
{
    $args = func_get_args();
    foreach($args as $arg)
    {
        if ( $arg === null )
            continue;
        else return $arg;
    }
    return null;
}

/**
 * Fast translation method
 *
 * @param array   $text  An array of texts
 * @param boolean $force Boolean value whether to translate or not
 *
 * @return string
 */
function translate($texts, $force = true)
{
    settype($texts, 'array');
    $debug		  = isset($_GET['dbg']);
    $debug_list	  = array();
    $lang  		  = JFactory::getLanguage();
    $has_key 	  = version_compare(JVERSION,'1.6.0','ge');
    $translatable = false;
    foreach($texts as $text) {
        if ( strpos($text, '_') ) {
            $text = strtoupper(str_replace('_','-',$text));
        }
        if ( $has_key ? $lang->hasKey($text) : isset($lang->_strings[$text]) ) {
            if ( $debug ) {
                $debug_lists[] = $text.'=>'.$lang->_($text);
                continue;
            }
            $text = $lang->_($text);
            $translatable = true;
            break;
        } elseif ( $debug ) {
            $debug_lists[] = $text;
        }
    }

    if ( $debug ) {
        return '['.implode(',', $debug_lists).']';
    }
    if ( !$translatable && !$force ) {
        return null;
    }

    return $text;
}

/**
 * Calls a method of an object. Optimized version of call_user_function
 *
 * @param object $object    The object ot call a method. If null, the only the method is called
 * @param string $method    A method to call on the passed object
 * @param array  $arguments An array of arugments to be passed to method
 *
 * @return mixed
 * 
 * @deprecated Use invoke_callback instead
 */
function call_object_method($object, $method, array $arguments)
{
    // Call_user_func_array is ~3 times slower than direct method calls.
    switch(count($arguments))
    {
        case 0 :
            $result = $object ? $object->$method() : $method;
            break;
        case 1 :
            $result = $object ? $object->$method($arguments[0]) : $method($arguments[0]);
            break;
        case 2:
            $result = $object ? $object->$method($arguments[0], $arguments[1]) : $method($arguments[0], $arguments[1]);
            break;
        case 3:
            $result = $object ? $object->$method($arguments[0], $arguments[1], $arguments[2]) : $method($arguments[0], $arguments[1], $arguments[2]);
            break;
        default:
            // Resort to using call_user_func_array for many segments
            $callback = $object ? array($object, $method) : $method;
        $result = call_user_func_array($callback, $arguments);
    }
    return $result;
}

/**
 * Calls a valid callback. Optimized version of call_user_function
 *
 * @param callable $callback  A valid callback
 * @param array    $arguments An array of arugments to be passed
 *
 * @return mixed
 */
function invoke_callback($callback, array $arguments = array())
{
    if ( is_array($callback) ) 
    {
        $arguments = array_merge($callback, $arguments);
        if ( count($arguments) < 2 ) {
            throw new \InvalidArgumentException('Not a valid callback');
        }
        $object = array_shift($arguments);
        $method = array_shift($arguments);
    }
    elseif ( is_string($callback) || $callback instanceof \Closure )
    {
        $object = null;
        $method = $callback;    
    } else {
        throw new \InvalidArgumentException('Not a valid callback');
    }
    
    // Call_user_func_array is ~3 times slower than direct method calls.
    switch(count($arguments))
    {
        case 0 :
            $result = $object ? $object->$method() : $method();
            break;
        case 1 :
            $result = $object ? $object->$method($arguments[0]) : $method($arguments[0]);
            break;
        case 2:
            $result = $object ? $object->$method($arguments[0], $arguments[1]) : 
                $method($arguments[0], $arguments[1]);
            break;
        case 3:
            $result = $object ? $object->$method($arguments[0], $arguments[1], $arguments[2]) : $method($arguments[0], $arguments[1], $arguments[2]);
            break;
        case 4:
                $result = $object ? $object->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3]) : $method($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                break;            
        default:
            // Resort to using call_user_func_array for many segments
            $callback = $object ? array($object, $method) : $method;
            $result   = call_user_func_array($callback, $arguments);
    }
    return $result;
}


/**
 * Return a mimetype of a filename based on its extension name
 *
 * @param string $filename Filename
 *
 * @return mixed
 */
function mime_type($filename)
{

    $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    $ext = strtolower(array_pop(explode('.',$filename)));
    if (array_key_exists($ext, $mime_types)) {
        return $mime_types[$ext];
    }
    elseif (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $mimetype;
    }
    else {
        return 'application/octet-stream';
    }
}

/**
 * Inspects an array of objects
 *
 * @param array   $data     The data to inspect
 * @param boolean $var_dump Boolean value whether to dump or just return the inspected set
 *
 * @return mixed
 */
function inspect($data, $var_dump = true)
{
    $dump = array();
    $data = KConfig::unbox($data);
    foreach($data as $key => $value)
    {
        if ( is_array($value) || $value instanceof IteratorAggregate || $value instanceof Iterator  ) {
            $dump[$key] = inspect($value, false);
        } elseif( is_scalar($value) ) {
            $dump[$key] = $value;
        } elseif ( is($value, 'AnDomainEntityAbstract', 'AnDomainEntitysetAbstract') )
        $dump[$key] = $value->inspect(false);
        elseif ( is($value, 'KObjectIdentifiable') )
        $dump[$key] = (string)$value->getIdentifier();
        elseif ( is_object($value) )
        $dump[$key] = get_class($value);
    }

    if ( $var_dump )
        var_dump($dump);
    else return $dump;
}

/**
 * Return an array of parent classes for a given class
 *
 * @param string|object $class   Class object or class name
 * @param string        $break   A prefix to break the loop
 * @param boolean       $reverse The order of the classes array
 *
 * @return array
 */
function get_parents($class, $break = null, $reverse = true)
{
    $class    =  is_string($class) ? $class : get_class($class);
    $classes  = array();
    while($class)
    {
        $class = get_parent_class($class);

        if ( $break && strpos($class, $break) === 0 || empty($class) )
        {
            break;
        }

        $reverse ? array_unshift($classes, $class) : array_push($classes, $class);
    }
    return $classes;
}

/**
 * Return a configuation value for an extension
 *
 * @param string  $extension The extension name
 * @param string  $key       The configuration key
 * @param boolean $default   A default value to return if no there are no values
 *
 * @return string
 */
function get_config_value($extension, $key = null, $default = null)
{
    if ( strpos($extension,'.') ) {
        $default = $key;
        list($extension, $key) = explode('.', $extension);
    }

    if ( !strpos($extension, '_') ) {
        $extension = 'com_'.$extension;
    }

    list($type, $name) = explode('_', $extension);

    if ( $type == 'com' ) {
        $params = JComponentHelper::getParams('com_'.$name);
    }

    return $key ?  $params->get($key, $default) : $params;
}

/**
 * Dispatches a plugin event. This method will load first load the necessary plugins and then
 * dispatches the passed event.
 *
 * @param string  $plugin     The plugin name
 * @param array   $args       An array of arugments
 * @param mixed   $dispatcher The dispatcher to use, by default it uses koowa:event.dispatcher
 *
 * @return void
 */
function dispatch_plugin($plugin, $args = array(), $dispatcher = null)
{
    $parts  = explode('.', $plugin);
    $event  = array_pop($parts);
    $dispatcher = pick($dispatcher, KService::get('anahita:event.dispatcher'));

    if ( !empty($parts) ) {
        JPluginHelper::importPlugin($parts[0], isset($parts[1]) ? $parts[1] : null, true, $dispatcher);
    }

    if ( $dispatcher instanceof JDispatcher )
        return $dispatcher->trigger($event, $args);
    else
        return $dispatcher->dispatchEvent($event, $args);
}

/**
 * Encode a URL with base64.
 *
 * This method has been taken from http://malevolent.com/weblog/archive/2008/08/29/php-base64-url-encode-decode/
 *
 * @param string $data URL data
 * @return void string
 */
function base64UrlEncode($data)
{
    return strtr(rtrim(base64_encode($data), '='), '+/', '-_');
}

/**
 * Decode a URL with base64
 *
 * @param string $base64 Based64 URL
 * @return void string
 */
function base64UrlDecode($base64)
{
    return base64_decode(strtr($base64, '-_', '+/'));
}

/**
 * Run a shell command in the background
 *
 * @param string $command The command to run
 *
 * @return mixed
 */
function exec_in_background($command)
{
    if(substr(php_uname(), 0, 7) == 'Windows')
    {
        pclose(popen('start "background_exec" ' . $command, 'r'));
    }
    else
    {
        return exec($command . ' > /dev/null & echo $!');
    }
}

/**
 * Flush a chunk of output from the buffer
 *
 * @return void
 */
function flush_chunk()
{
    print str_pad("\n",4096);ob_flush();flush();
}

/**
 * Easy way to set a path for an identifier. The each path segment is seperated by /
 * Allows to set relative path using ..
 *
 * @param KServiceIdentifier $identifier
 * @param string             $path
 *
 * @return void
 */
function append_identifier_path($identifier, $path)
{
    $parts = explode('/', $path);
    $path  = $identifier->path;
    foreach($parts as $part)
    {
        if ( $part == '..')
            array_pop($path);
        else
            array_push($path, $part);
    }
    $identifier->path = $path;
}

/**
 * Return if the actor is the viewer
 *
 * @param ComPeopleDomainEntityActor $actor Actor object
 *
 * @return boolean
 */
function is_viewer($actor)
{
    return $actor && $actor->id == get_viewer()->id;
}

/**
 * Return if the actor is a type of person
 *
 * @param ComPeopleDomainEntityActor $actor Actor Object
 *
 * @return boolean
 */
function is_person($actor)
{
    return $actor && $actor->inherits('ComPeopleDomainEntityPerson');
}

/**
 * Return the viewer object
 *
 * @return ComPeopleDomainEntityPerson
 */
function get_viewer()
{
    return KService::get('com:people.viewer');
}

/**
 * Cleans the APC values that has a key that starts with $prefix
 *
 * @param string $prefix
 * 
 * @return void
 */
function clean_apc_with_prefix($prefix)
{
    if ( extension_loaded('apc')  )
    {
        $info = @apc_cache_info('user');
        
        if ( $info )
        {
            $list = (array)$info['cache_list'];
            //delete all the entiry with the prefix $key
            foreach($list as $entry) {
                if ( strpos($entry['info'], $prefix) === 0 ) {
                    apc_delete($entry['info']);
                }
            }
        }        
    }
}


/**
 * Cleans the apc user cache if it's loaded
 * 
 * @return void
 */
function clean_ap_user_cache()
{
    if ( extension_loaded('apc')  )
    {
        apc_clear_cache('user');     
    }
}

/**
 * Check if an actor is a person type and also is guest
 * 
 * @param ComActorsDomainEntityActor $actor Actor entity
 * 
 * @return boolean
 */
function is_guest($actor)
{
   return is_person($actor) && $actor->userType == 'Guest';
}

/**
 * Check if an actor is a person type and also is admin
 * 
 * @param ComActorsDomainEntityActor $actor Actor entity
 * 
 * @return boolean
 */
function is_admin($actor)
{
    return is_person($actor) && ($this->userType == 'Administrator' || $this->userType == 'Super Administrator');  
}

/**
 * Prints a query and repalce #__ with jos__
 * 
 */
function print_query($query)
{
    if ( $query instanceof AnDomainEntitysetDefault ) {
        $query = $query->getQuery();
    }
    
    if ( $query instanceof AnDomainQuery ) {
        $repos   = $query->getRepository();
        $context = $repos->getCommandContext();
        $context->operation = AnDomain::OPERATION_FETCH;
        $context->query     = $query;
        $context->mode      = AnDomain::FETCH_ENTITY_SET;
        $query->fetch_mode  = AnDomain::FETCH_ENTITY_SET;
        $repos->getCommandChain()->run('before.fetch', $context);
        $query = (string) $context->query;
    }
    
    print str_replace('#__','jos_', $query)."\G";
}

function trace_mark($message)
{
    global $_checkpoints;
    
    if ( !$_checkpoints ) {
        $_checkpoints = array();
    }
    
    $traces = debug_backtrace();
    array_shift($traces);
    $trace = array_shift($traces);
    $called_method = '';
    if ( isset($trace['class']) ) {
        $called_method = $trace['class'].'::';
    }
    if ( isset($trace['function']))
        $called_method .= $trace['function'].'()';

    $trace = array_shift($traces);

    $calling_method = '';
    
    if ( isset($trace['object']) ) {
        $calling_method = get_class($trace['object']).'::';
    }
    elseif ( isset($trace['class']) ) {
        $calling_method = $trace['class'].'::';
    }

    if ( isset($trace['function']) && $trace['function'] != 'include') {
        $calling_method .= $trace['function'].'()';
    }
    if ( empty($calling_method) && isset($trace['file']) ) {
        if ( isset($trace['function']) && $trace['function'] == 'include') {
            $calling_method = $trace['args'][0];
        } else
            $calling_method = $trace['file'];
    }

    if ( isset($trace['line']) )
        $calling_method = $calling_method.' line:'.$trace['line'];
    $index = count($_checkpoints) + 1;
    $message =  $index.' - '.$calling_method.' '.$message;     
    array_push($_checkpoints,$message);
}

function get_marked_traces()
{
    global $_checkpoints;
    return pick($_checkpoints, array());
}



if (!function_exists('fastcgi_finish_request'))
{
    function fastcgi_finish_request()
    {
        if (PHP_SAPI !== 'cli')
        {
            for ($i = 0; $i < ob_get_level(); $i++) {
                ob_end_flush();
            }
        
            flush();
        }        
    }
}

/**
 * Return if an arary is a hash array
 *
 * @param array $array
 *
 * @return boolean
 */
function is_hash_array($array)
{
    $count = count($array);   
    foreach($array as $key => $value) 
    {
        if ( !is_int($key) || $key >= $count ) {
            return true;
        }
    }
    return false;    
}

/**
 * Return the value of an array at $index or null of not found. A negative number
 * can be passed to return the value from am index counting from the end of the 
 * array
 * 
 * @param array $array The array
 * @param int   $index The index
 * @param mixed[optional] $default Value to return if index is not found
 */
function array_value($array, $index, $default = null)
{
    if ((int)$index == $index and $index < 0) {
        $index = count($array) + $index;
    }
    return isset($array[$index]) ? $array[$index] : $default;
}

/**
 * Fix config bug when hash array and list array are mixed together
 * 
 * @param array $array
 * 
 * @return array
 */
function to_hash($array, $default = array())
{
    $array     = (array)$array;
    $new_array = array();
    foreach($array as $key => $value) 
    {
        if ( is_int($key) ) {
            $key   = $value;
            $value = $default;
        }
        $new_array[$key] = $value;
    }
    return $new_array;
}

/**
 * Return an array group by the value returned by the callback
 * 
 * @param array $array
 * @param mixed $callback
 */
function array_group_by($array, $callback)
{
   $group = array();
   foreach($array as $item) {
       $group[$callback($item)][] = $item;
   }
   return $group;
}
?>