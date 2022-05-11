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
 * @link       http://www.Anahita.io
 */

/**
 * Compontn Locator. If a component is not found, it first look at the default classes.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnServiceLocatorComponent extends AnServiceLocatorAbstract
{
    /**
     * The type.
     *
     * @var string
     */
    protected $_type = 'com';

    /**
     * Get the classname based on an identifier.
     *
     * @param 	mixed  		 An identifier object - anahita:[path].name
     *
     * @return string|false Return object on success, returns FALSE on failure
     */
    public function findClass(AnServiceIdentifier $identifier)
    {
        $path = AnInflector::camelize(implode('_', $identifier->path));
        $classname = 'Com'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
        $loader = $this->getService('anahita:loader');
        //Manually load the class to set the basepath
        if (! $loader->loadClass($classname, $identifier->basepath)) {
            //the default can be in either in the default folder
            //be a registered default class
            $classname = AnServiceClass::findDefaultClass($identifier);
            //hack
            if ($classname == 'AnDomainBehaviorDefault') {
                $classname = null;
            }
            
            if (! $classname) {
                $classname = $this->_findClass($identifier);
            }
        }

        return $classname;
    }

    /**
     * Find a class.
     *
     * @param AnServiceIdentifier $identifier
     *
     * @return string
     */
    protected function _findClass($identifier)
    {
        $loader = $this->getService('anahita:loader');
        $classname = null;
        //Create the fallback path and make an exception for views
        $classpath = $identifier->path;
        $classtype = !empty($classpath) ? array_shift($classpath) : '';
        $paths = array();
        $paths[] = ucfirst($classtype).AnInflector::camelize(implode('_', $classpath));
        
        if ($classtype == 'view') {
            $paths[] = ucfirst($classtype);
        }

        $paths = array_unique($paths);

        $namespaces = array();
        $namespaces[] = 'Com'.ucfirst($identifier->package);
        $namespaces[] = 'Lib'.ucfirst($identifier->package);
        $namespaces[] = 'ComBase';
        $namespaces[] = 'LibBase';
        $namespaces[] = 'ComDefault';
        $namespaces[] = 'An';
        $namespaces = array_unique($namespaces);
        $classes = array();
        
        foreach ($namespaces as $namespace) {
            foreach ($paths as $path) {
                $names = array();
                $names[] = ucfirst($identifier->name);
                $names[] = empty($path) ? ucfirst($identifier->name).'Default' : 'Default';
                
                foreach ($names as $name) {
                    $class = $namespace.$path.$name;
                    $classes[] = $class;
                    
                    if ($loader->findPath($class, $identifier->basepath) &&
                         $loader->loadClass($class, $identifier->basepath)) {
                        $classname = $class;
                        break 3;
                    }
                }
            }
        }

        return $classname;
    }
    
    /**
     * Get the path based on an identifier
     *
     * @param  object  	An identifier object - com:[//application/]component.view.[.path].name
     * @return string	Returns the path
     */
    public function findPath(AnServiceIdentifier $identifier)
    {
        $path  = '';
        $parts = $identifier->path;
                
        $component = 'com_'.strtolower($identifier->package);
            
        if (! empty($identifier->name)) {
            if (count($parts)) {
                if ($parts[0] != 'view') {
                    foreach ($parts as $key => $value) {
                        $parts[$key] = AnInflector::pluralize($value);
                    }
                } else {
                    $parts[0] = AnInflector::pluralize($parts[0]);
                }
                
                $path = implode('/', $parts).'/'.strtolower($identifier->name);
            } else {
                $path  = strtolower($identifier->name);
            }
        }
                
        $path = $identifier->basepath.'/components/'.$component.'/'.$path.'.php';
        return $path;
    }
}
