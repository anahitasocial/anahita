<?php

/**
 * Local storage plugin.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class PlgStorageLocal extends PlgStorageAbstract
{

    /**
    *  Path to the root
    *
    *  @var string
    */
    protected $_root = ANPATH_ROOT;

    /**
    *  Base uri
    *
    *  @var string
    */
    protected $_base_uri = '';

    /**
     * Constructor.
     *
     * @param mixed $dispatcher A dispatcher
     * @param array $config     An optional KConfig object with configuration options.
     */
    public function __construct($dispatcher = null,  $config = array())
    {
        parent::__construct($config);
        $this->_base_uri = KRequest::base();
    }

    /**
     * {@inheritdoc}
     */
    protected function _read($path)
    {
        $path = $this->_realpath($path);
        return file_get_contents($path);
    }

    /**
     * {@inheritdoc}
     */
    protected function _write($path, $data, $public)
    {
        $path = $this->_realpath($path);
        $dir = dirname($path);
        $success = false;

        if (!is_dir($dir)) {
            @mkdir($dir, 0707, true);
        }

        return file_put_contents($path, (string) $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _exists($path)
    {
        $path = $this->_realpath($path);
        return file_exists($path);
    }

    /**
     * {@inheritdoc}
     */
    protected function _delete($path)
    {
        $path = $this->_realpath($path);

        if (is_dir($path)) {
            @rmdir($path);
        } elseif (file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _getUrl($path)
    {
        return $this->_base_uri.DS.$path;
    }

    /**
     * Return the realpath path of a relative path.
     *
     * @param string $path The path to append
     *
     * @return
     */
    protected function _realpath($relative)
    {
        return $this->_root.DS.str_replace('/', DS, $relative);
    }
}
