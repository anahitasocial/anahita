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
 * Compontn Locator. If a component is not found, it first look at the default classes.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnServiceLocatorComponent extends KServiceLocatorComponent
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
     * @param 	mixed  		 An identifier object - koowa:[path].name
     *
     * @return string|false Return object on success, returns FALSE on failure
     */
    public function findClass(KServiceIdentifier $identifier)
    {
        $path = AnInflector::camelize(implode('_', $identifier->path));
        $classname = 'Com'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
        $loader = $this->getService('koowa:loader');
          //Manually load the class to set the basepath
        if (!$loader->loadClass($classname, $identifier->basepath)) {
            //the default can be in either in the default folder
            //be a registered default class
            $classname = AnServiceClass::findDefaultClass($identifier);
            //hack
            if ($classname == 'AnDomainBehaviorDefault') {
                $classname = null;
            }
            if (!$classname) {
                $classname = $this->_findClass($identifier);
            }
        }

        return $classname;
    }

    /**
     * Find a class.
     * 
     * @param KServiceIdentifier $identifier
     * 
     * @return string
     */
    protected function _findClass($identifier)
    {
        $loader = $this->getService('koowa:loader');
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
        $namespaces[] = 'K';
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
}
