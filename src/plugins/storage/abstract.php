<?php

/**
 * Abstract storage plugin.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
abstract class PlgStorageAbstract extends AnObject
{
    /**
     * Storage Parameter Configuration.
     *
     * @var AnConfig
     */
    protected $_params;

    /**
    * Storage folder
    *
    *  @var string
    */
    protected $_folder;

    /**
     * Constructor.
     *
     * @param mixed $dispatcher A dispatcher
     * @param array $config     An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config, $dispatcher = null)
    {
        $this->_params = $config->meta;
        
        parent::__construct($config);

        $this->_folder = $config->folder;

        AnService::set('plg:storage.default', $this);
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
        $config->append(array(
            'folder' => 'assets',
        ));

        parent::_initialize($config);
    }

    /**
     * Return the content of a path. The boolean value determins whether to look for the
     * path in the public or protected folder.
     *
     * @param string $path   The path to read the content for
     * @param bool   $public Determines if the path is in the public/protected folder
     *
     * @return string
     */
    public function read($path, $public = true)
    {
        $path = $this->_relativepath($path, $public);

        return $this->_read($path);
    }

    /**
     * Write data to a path. The boolean value determins whether to look for the
     * path in the public or protected folder.
     *
     * @param string $path   The path
     * @param string $data   The data to write to the path
     * @param bool   $public Determines if the path is in the public/protected folder
     *
     * @return string
     */
    public function write($path, $data, $public = true)
    {
        $path = $this->_relativepath($path, $public);
        $this->_write($path, $data, $public);
    }

    /**
     * Delete the content stored at path.
     *
     * @param string $path   The path
     * @param bool   $public Determines if the path is in the public/protected folder
     *
     * @return string
     */
    public function delete($path, $public = true)
    {
        $path = $this->_relativepath($path, $public);
        $this->_delete($path);
    }

    /**
     * Return whether a path exists or not.
     *
     * @param string $path   The path
     * @param bool   $public Determines if the path is in the public/protected folder
     *
     * @return string
     */
    public function exists($path, $public = true)
    {
        $path = $this->_relativepath($path, $public);

        return $this->_exists($path);
    }

    /**
     * Return a path URL (unique resource locator).
     *
     * @param string $path   The path
     * @param bool   $public Determines if the path is in the public/protected folder
     *
     * @return string
     */
    public function getUrl($path, $public = true)
    {
        $path = $this->_relativepath($path, $public);

        return $this->_getUrl($path);
    }

    /**
     * Internal method to create the the complete path based on whether it's a public/protected
     * path.
     *
     * @param string $path   The path
     * @param bool   $public Determines if the path is in the public/protected folder
     *
     * @return string
     */
    protected function _relativepath($path, $public)
    {
        return $this->_folder.'/'.($public ? "public/$path" : "private/$path");
    }

    /**
     * Abstract Read Method. Impleneted by subsclasses.
     *
     * Return the content of a path
     *
     * @param string $path The path
     *
     * @return string
     */
    abstract protected function _read($path);

    /**
     * Abstract Write Method. Impleneted by subsclasses.
     *
     * Write data to path
     *
     * @param string $path   The path
     * @param string $data   The data to store
     * @param bool   $public If the data is accessible by public
     *
     * @return string
     */
    abstract protected function _write($path, $data, $public);

    /**
     * Abstract Delete Method. Impleneted by subsclasses.
     *
     * Deletes the data stored at a path
     *
     * @param string $path The path
     *
     * @return string
     */
    abstract protected function _delete($path);

    /**
     * Abstract Exists Method. Impleneted by subsclasses.
     *
     * Returns whether a path exists or not
     *
     * @param string $path The path
     *
     * @return string
     */
    abstract protected function _exists($path);

    /**
     * Abstract getURL Method. Impleneted by subsclasses.
     *
     * Returns a path unique resource locator (URL)
     *
     * @param string $path The path
     *
     * @return string
     */
    abstract protected function _getUrl($path);
}
