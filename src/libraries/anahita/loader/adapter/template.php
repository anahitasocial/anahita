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
 * Template Loader.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnLoaderAdapterTemplate extends KLoaderAdapterAbstract
{
    /** 
     * The adapter type.
     * 
     * @var string
     */
    protected $_type = 'tmpl';

    /**
     * The class prefix.
     * 
     * @var string
     */
    protected $_prefix = 'Tmpl';

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

        if (strpos($classname, $this->_prefix) === 0) {
            $word = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $classname));
            $parts = explode('_', $word);

            if (array_shift($parts) == 'tmpl') {
                $name = array_shift($parts);

                $file = array_pop($parts);

                if (count($parts)) {
                    if ($parts[0] != 'view') {
                        foreach ($parts as $key => $value) {
                            $parts[$key] = AnInflector::pluralize($value);
                        }
                    } else {
                        $parts[0] = AnInflector::pluralize($parts[0]);
                    }

                    $path = implode('/', $parts).'/'.$file;
                } else {
                    $path = $file;
                }

                $path = $this->_basepath.'/templates/'.$name.'/'.$path.'.php';
            }
        }

        return $path;
    }
}
