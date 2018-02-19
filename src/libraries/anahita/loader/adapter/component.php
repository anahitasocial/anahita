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
 * Loads Anahita related objects.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnLoaderAdapterComponent extends KLoaderAdapterAbstract
{
    /**
     * The adapter type.
     *
     * @var string
     */
    protected $_type = 'com';

    /**
     * The class prefix.
     *
     * @var string
     */
    protected $_prefix = 'Com';

    /**
     * Get the path based on a class name.
     *
     * @param  string		  	The class name
     *
     * @return string|false Returns the path on success FALSE on failure
     */
    public function findPath($classname, $basepath = null)
    {
        $path = false;

        /*
         * Exception rule for Exception classes
        *
        * Transform class to lower case to always load the exception class from the /exception/ folder.
        */
        if ($pos = strpos($classname, 'Exception')) {
            $filename = substr($classname, $pos + strlen('Exception'));
            $classname = str_replace($filename, ucfirst(strtolower($filename)), $classname);
        }

        $word = strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $classname));

        $parts = explode(' ', $word);

        if (array_shift($parts) == 'com') {
            //Switch the basepath
            if (!empty($basepath)) {
                $this->_basepath = $basepath;
            }

            $component = 'com_'.strtolower(array_shift($parts));
            $file = array_pop($parts);
            $path = null;
            if (count($parts)) {
                if ($parts[0] != 'view') {
                    foreach ($parts as $key => $value) {
                        $parts[$key] = AnInflector::pluralize($value);
                    }
                } else {
                    $parts[0] = AnInflector::pluralize($parts[0]);
                }
                $path = implode('/', $parts);
            }

            $path = '/components/'.$component.'/'.$path;

            $filepath = $path.'/'.$file.'.php';
            $basepath = $this->_basepath;

            if (array_value($parts, -1) == 'exceptions') {
                if (!file_exists($basepath.$filepath)) {
                    $filepath = $path.'/default.php';
                }
            }

            if (count($parts) == 2 && $parts[0] == 'domains') {
                if ($parts[1] == 'entities' && ANPATH_SITE != $this->_basepath) {
                    //set the basepath of entities to the site
                    if (!file_exists($basepath.$filepath)) {
                        $basepath = ANPATH_SITE;
                    }
                }
            }

            $path = $basepath.$filepath;
        }

        return $path;
    }
}
