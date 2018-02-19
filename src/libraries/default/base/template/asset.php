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
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 *
 * @link       http://www.GetAnahita.com
 */

/**
 * Asset Finder.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateAsset extends KObject implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton.
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier)) {
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
     * Base media paths to search.
     *
     * @var array
     */
    protected $_paths = array();

    /**
     * Media physical path on the server.
     *
     * @var array
     */
    protected $_file_paths = array();

    /**
     * URLs.
     *
     * @var array
     */
    protected $_urls = array();

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $paths = array_reverse(KConfig::unbox($config->asset_paths));

        foreach ($paths as $path) {
            $this->addPath($path);
        }
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'asset_paths' => array('media'),
        ));

        parent::_initialize($config);
    }

    /**
     * Add relative search base paths from ANPATH_BASE in which look for the media. The paths are added
     * to the beginging of the search list.
     *
     * @param $path string|array Adds a base path
     */
    public function addPath($path)
    {
        // just force to array
        settype($path, 'array');

        // loop through the path directories
        foreach ($path as $dir) {
            // no surrounding spaces allowed!
            $dir = trim($dir);

            // remove trailing slash
            if (substr($dir, -1) == DIRECTORY_SEPARATOR) {
                $dir = substr_replace($dir, '', -1);
            }

            // add to the top of the search dirs
            array_unshift($this->_paths, $dir);
        }
    }

    /**
     * Return a URL full path. This method should be called after $this->getPath.
     *
     * @param string $url Return the file for a URL
     *
     * @return string|null
     */
    public function getFilePath($url)
    {
        return isset($this->_file_paths[$url]) ? $this->_file_paths[$url] : null;
    }

    /**
     * Searches through a list of paths  to find a media file. To obtain the media
     * full physical path.
     *
     * @param string $url      Asset file whose path is being searched
     * @param string $filepath If a path variable is passed the file path would be set
     *
     * @return string
     */
    public function getURL($url, &$filepath = null)
    {
        $paths = $this->_paths;

        if (array_key_exists($url, $this->_urls)) {
            return $this->_urls[$url];
        }

        settype($paths, 'array'); //force to array

        $this->_urls[$url] = null;

        $base = $this->getService('application')->getRouter()->getBaseUrl();
        $file_path = null;
        $path = null;

        // start looping through the path set
        foreach ($paths as $path) {
            // get the path to the file
            $path = $path.DS.$url;

            // the substr() check added to make sure that the realpath()
            // results in a directory registered so that
            // non-registered directores are not accessible via directory
            // traversal attempts.
            $file_path = ANPATH_ROOT.DS.$path;

            //fixes windows file system issue
            $path = str_replace(DS, '/', $path);

            if (file_exists($file_path)) {
                break;
            }

            $file_path = null;
            $path = null;
        }

        if ($file_path) {
            $path = $base.'/'.$path;
            $this->_urls[$url] = $path;
            $this->_urls[$path] = $path;
            $this->_file_paths[$url] = $file_path;
            $this->_file_paths[$path] = $file_path;
        }

        $filepath = $this->getFilePath($url);

        return $this->_urls[$url];
    }
}
