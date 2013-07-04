<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_File
 * @subpackage File
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Path Finder. This class finds a relative path within list of search paths
 * 
 * <code>
 * $finder = KService::get('anahita:file.pathfinder');
 * $finder->addSearchDirs(array($path1,$path2));
 * $finder->getPath($relative_path);
 * </code>
 * 
 * @category   Anahita
 * @package    Anahita_File
 * @subpackage File
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnFilePathfinder extends KObject
{        
    /**
     * Base media paths to search
     * 
     * @var array
     */
    protected $_dirs = array();
    
    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);        
    }
    
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    protected function _initialize(KConfig $config)
    {       
        parent::_initialize($config);
    }
        
    /**
     * Adds an array of search dirs to search for a path
     * 
     * @param $paths string|array Adds a base path
     * 
     * @return AnFilePathfinder
     */
    public function addSearchDirs($dirs)
    { 
        // just force to array
        settype($dirs, 'array');

        // loop through the path directories
        foreach ($dirs as $dir)
        {
            // no surrounding spaces allowed!
            $dir = trim($dir);

            // remove trailing slash
            if (substr($dir, -1) == DIRECTORY_SEPARATOR) {
                $dir = substr_replace($dir, '', -1);
            }

            // add to the top of the search dirs
            array_unshift($this->_dirs, $dir);
        }
        
        return $this;
    }
    
    /**
     * Return an array of search directories
     * 
     * @return array
     */
    public function getSearchDirs()
    {
        return $this->_dirs;
    }
        
    /**
     * Gets a file path of a relative path by searching the directories
     * 
     * @param string $path Relative path
     * 
     * @return string
     * 
     * @uses AnFilePathfinder::_findPath to find the path
     */
    public function getPath($path)
    {
        return $this->_findPath($path);
    }
    
    /**
     * Search for a relative path within the search directories
     * 
     * @param string $path Relative path
     * 
     * @return string
     */
    protected function _findPath($path)
    {
        $dirs = $this->_dirs;
        
        $file = null;
        
        foreach($dirs as $dir)
        {
            if ( is_readable(realpath($dir.DS.$path)) ) {
                $file = $dir.DS.$path;
                break;
            }            
        }
        
        return $file;       
    }    
}

?>