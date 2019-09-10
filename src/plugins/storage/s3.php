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
     * S3 region
     *
     * @var string
     */
    protected $_region;

    /**
     * Constructor.
     *
     * @param mixed $dispatcher A dispatcher
     * @param array $config     An optional AnConfig object with configuration options.
     */
    public function __construct($dispatcher = null,  AnConfig $config)
    {
        parent::__construct($dispatcher, $config);

        $this->_bucket = $config->bucket;
        $this->_region = $config->region;

        $this->_s3 = new S3(
            $config->access_key,
            $config->secret_key,
            $config->ssl,
            's3.amazonaws.com',
            $config->region
        );
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
             'access_key' => $this->_params->access_key,
             'secret_key' => $this->_params->secret_key,
             'bucket' => $this->_params->bucket,
             'region' => $this->_params->region,
             'ssl' => true,
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
        return sprintf('https://s3.%s.amazonaws.com/%s/%s', $this->_region, $this->_bucket, $path);
    }
}
