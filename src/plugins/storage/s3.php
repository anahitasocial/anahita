<?php
require_once ANPATH_VENDOR . DS . 'autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\Credentials\Credentials;

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
    const ACL_PUBLIC_READ = 'public-read';
    const ACL_PRIVATE = 'private';
    
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
     * Constructor.
     *
     * @param mixed $dispatcher A dispatcher
     * @param array $config     An optional AnConfig object with configuration options.
     */
    public function __construct($dispatcher = null,  AnConfig $config)
    {
        parent::__construct($dispatcher, $config);

        $this->_s3 = new Aws\S3\S3Client([
            'profile' => $config->profile,
            'version' => 'latest',
            'region' => $config->region,
            'scheme' => 'https',
            'credentials' => $config->credentials->toArray(),
            // 'debug' => (bool) ANDEBUG,
        ]);
        
        $this->_bucket = $config->bucket;
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
             'bucket' => $this->_params->bucket,
             'region' => $this->_params->region,
             'profile' => $this->_params->profile,
             'credentials' => array(
                 'key' => $this->_params->access_key,
                 'secret' => $this->_params->secret_key,
             )
        ));

        parent::_initialize($config);
    }

    /**
     * {@inheritdoc}
     */
    protected function _read($path)
    {
        // return $this->_s3->getObject($this->_bucket, $path)->body;
        return $this->_s3->getObject(array(
            'Bucket' => $this->_bucket,
            'Key' => $path,
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function _write($path, $data, $public)
    {
        $acl = $public ? self::ACL_PUBLIC_READ : self::ACL_PRIVATE;
        // return $this->_s3->putObject($data, $this->_bucket, $path, $acl);
        
        try {
            $this->_s3->putObject(array(
                'ACL' => $acl,
                'Body' => (string) $data,
                'Bucket' => $this->_bucket,
                'Key' => $path,
            ));
        } catch (Aws\S3\Exception\S3Exception $e) {
            error_log($e->getMessage());
        }
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function _exists($path)
    {
        $res = $this->_s3->getObject(array(
            'Bucket' => $this->_bucket,
            'Key' => $path,
        ));
        
        return !empty($res['Body']);
    }

    /**
     * {@inheritdoc}
     */
    protected function _delete($path)
    {
        return true;
        return $this->_s3->deleteObject(array(
            'Bucket' => $this->_bucket,
            'Key' => $path,
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function _getUrl($path)
    {   
        return $this->_s3->getObjectUrl($this->_bucket, $path);
    }
}
