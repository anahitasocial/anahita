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
 * Template Locator. 
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnServiceLocatorTemplate extends KServiceLocatorAbstract
{
    /** 
     * The type.
     * 
     * @var string
     */
    protected $_type = 'tmpl';

    /**
     * Get the classname based on an identifier.
     *
     * @param 	mixed  		 An identifier object - koowa:[path].name
     *
     * @return string|false Return object on success, returns FALSE on failure
     */
    public function findClass(KServiceIdentifier $identifier)
    {
        $classname = 'Tmpl'.ucfirst($identifier->package).AnInflector::implode($identifier->path).ucfirst($identifier->name);

        if (!$this->getService('koowa:loader')->loadClass($classname, $identifier->basepath)) {
            $classname = AnServiceClass::findDefaultClass($identifier);

            if (!$classname) {
                //$path      = AnInflector::implode($identifier->path);
                $classpath = $identifier->path;
                $classtype = !empty($classpath) ? array_shift($classpath) : '';

                //Create the fallback path and make an exception for views
                $path = ($classtype != 'view') ? ucfirst($classtype).AnInflector::camelize(implode('_', $classpath)) : ucfirst($classtype);

                $classes[] = 'Tmpl'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
                $classes[] = 'Tmpl'.ucfirst($identifier->package).$path.'Default';
                $classes[] = 'ComApplication'.$path.ucfirst($identifier->name);
                $classes[] = 'ComApplication'.$path.'Default';
                $classes[] = 'LibApplication'.$path.ucfirst($identifier->name);
                $classes[] = 'LibApplication'.$path.'Default';
                $classes[] = 'LibBase'.$path.ucfirst($identifier->name);
                $classes[] = 'LibBase'.$path.'Default';
                $classes[] = 'K'.$path.ucfirst($identifier->name);
                $classes[] = 'K'.$path.'Default';

                foreach ($classes as $class) {
                    if ($this->getService('koowa:loader')->loadClass($class,  $identifier->basepath)) {
                        $classname = $class;
                        break;
                    }
                }

                if ($classname) {
                    AnServiceClass::setDefaultClass($identifier, $classname);
                }
            }
        }

        return $classname;
    }

    /**
     * Get the path based on an identifier.
     *
     * @param  object   An identifier object - com:[//application/]component.view.[.path].name
     *
     * @return string Returns the path
     */
    public function findPath(KServiceIdentifier $identifier)
    {
        $path = '';
        $parts = $identifier->path;

        $theme = strtolower($identifier->package);

        if (!empty($identifier->name)) {
            if (count($parts)) {
                if ($parts[0] != 'html') {
                    foreach ($parts as $key => $value) {
                        $parts[$key] = AnInflector::pluralize($value);
                    }
                }

                $path = implode('/', $parts).'/'.strtolower($identifier->name);
            } else {
                $path = strtolower($identifier->name);
            }
        }

        $path = $identifier->basepath.'/templates/'.$theme.'/'.$path.'.php';

        return $path;
    }
}
