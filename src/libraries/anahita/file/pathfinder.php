<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 *
 * @link       http://www.Anahita.io
 */

/**
 * Path Finder. This class finds a relative path within list of search paths.
 * 
 * <code>
 * $finder = AnService::get('anahita:file.pathfinder');
 * $finder->addSearchDirs(array($path1,$path2));
 * $finder->getPath($relative_path);
 * </code>
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnFilePathfinder extends AnObject
{
    /**
     * Base media paths to search.
     * 
     * @var array
     */
    protected $_dirs = array();

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        parent::_initialize($config);
    }

    /**
     * Adds an array of search dirs to search for a path.
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
        foreach ($dirs as $dir) {
            // no surrounding spaces allowed!
            $dir = $this->_unifyPath(trim($dir));

            // remove trailing slash
            if (substr($dir, -1) == DS) {
                $dir = substr_replace($dir, '', -1);
            }

            // add to the top of the search dirs
            array_unshift($this->_dirs, $dir);
        }

        return $this;
    }

    /**
     * Return an array of search directories.
     * 
     * @return array
     */
    public function getSearchDirs()
    {
        return $this->_dirs;
    }

    /**
     * Gets a file path of a relative path by searching the directories.
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
     * Search for a relative path within the search directories.
     * 
     * @param string $path Relative path
     * 
     * @return string
     */
    protected function _findPath($path)
    {
        $path = $this->_unifyPath($path);
        $dirs = $this->_dirs;

        $file = null;

        foreach ($dirs as $dir) {
            if (is_readable(realpath($dir.DS.$path))) {
                $file = $dir.DS.$path;
                break;
            }
        }

        return $file;
    }

    /**
     * Unify directory separator to one used by environemnt OS.
     * 
     * @param string $path
     *
     * @return string
     */
    protected function _unifyPath($path)
    {
        return preg_replace('%[/\\\\]%', DS, $path);
    }
}
