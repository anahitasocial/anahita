<?php

require_once 's3lib.php';

/**
 * Amazon S3 storage plugin.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class PlgStorageS3 extends PlgStorageAbstract
{
    /**
     * S3 storage.
     *
     * @var object Amazon S3
     */
    protected $_s3;

    /**
     * S3 bucket
     *
     * @var string
     */
    protected $_bucket;

    /**
     * use SSL
     *
     * @var boolean
     */
    protected $_ssl;

    /**
     * Constructor.
     *
     * @param mixed $dispatcher A dispatcher
     * @param array $config     An optional KConfig object with configuration options.
     */
    public function __construct($dispatcher = null,  KConfig $config)
    {
        parent::__construct($dispatcher, $config);

        $this->_bucket = ($config->bucket != '') ? $config->bucket : $this->_params->bucket;
        $this->_ssl = (boolean) $config->ssl;

        $this->_s3 = new S3(
            $this->_params->access_key,
            $this->_params->secret_key,
            $this->_ssl
        );
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
             'bucket' => '',
             'ssl' => is_ssl()
        ));

        parent::_initialize($config);
    }

    /**
     * {@inheritdoc}
     */
    protected function _read($path)
    {
        return $this->_s3->getObject($this->_bucket, $path)->body;
    }

    /**
     * {@inheritdoc}
     */
    protected function _write($path, $data, $public)
    {
        $acl = $public ? S3::ACL_PUBLIC_READ : S3::ACL_PRIVATE;
        return $this->_s3->putObject($data, $this->_bucket, $path, $acl);
    }

    /**
     * {@inheritdoc}
     */
    protected function _exists($path)
    {
        return $this->_s3->getObjectInfo($this->_bucket, $path, false);
    }

    /**
     * {@inheritdoc}
     */
    protected function _delete($path)
    {
        return $this->_s3->deleteObject($this->_bucket, $path);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getUrl($path)
    {
        if ($this->_ssl) {
            $url = 'https://s3.amazonaws.com/'.$this->_bucket.'/'.$path;
        } else {
            $url = 'http://'.$this->_bucket.'.s3.amazonaws.com/'.$path;
        }

        return $url;
    }
}
