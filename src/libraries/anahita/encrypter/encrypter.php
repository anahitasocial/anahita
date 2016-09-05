<?php

/**
 * Encrypter Class
 *
 * This class is mostly from the Laravel's Encrypter class
 * and it is appropriated for Anahita. Thank you OpenSource and Laravel!
 * Checkout Laravel's framework here https://github.com/laravel/framework it is
 * a great project.
 *
 * @package 	Anahita.Framework
 * @copyright   TAYLOR OTWELL, www.laravel.com
 * @copyright   rmd Studio Inc, www.rmdStudio.com
 * @license     MIT https://opensource.org/licenses/MIT
 *
 */
class AnEncrypter extends KObject
{
    /**
     * The encryption key.
     *
     * @var string
     */
    protected $_key;

    /**
     * The algorithm used for encryption.
     *
     * @var string
     */
    protected $_cipher;

    /**
  	 * Constructor
  	 *
  	 * Prevent creating instances of this class by making the contructor private
  	 *
  	 * @param 	object 	An optional KConfig object with configuration options
  	 */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $key = (string) $config->key;
        $cipher = $config->cipher;

        if ($this->supported($key, $cipher)) {
            $this->_key = $key;
            $this->_cipher = $cipher;
        } else {
            throw new AnEncrypterException('The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths.');
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
    		'cipher' => 'AES-128-CBC',
            'key' => ''
        ));

        parent::_initialize($config);
    }

    /**
     * Determine if the given key and cipher combination is valid.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return bool
     */
    public function supported($key, $cipher)
    {
        $length = mb_strlen($key, '8bit');
        return ($cipher === 'AES-128-CBC' && $length === 16) || ($cipher === 'AES-256-CBC' && $length === 32);
    }

    /**
     * Encrypt the given value.
     *
     * @param  string  $value
     * @return string
     */
    public function encrypt($value)
    {
        $iv = function_exists('random_bytes') ? random_bytes(16) : openssl_random_pseudo_bytes(16);
        $value = openssl_encrypt(serialize($value), $this->_cipher, $this->_key, 0, $iv);

        if ($value === false) {
            throw new AnEncrypterException('Could not encrypt the data.');
        }

        $mac = $this->_hash($iv = base64_encode($iv), $value);
        $json = json_encode(compact('iv', 'value', 'mac'));

        if (! is_string($json)) {
            throw new AnEncrypterException('Could not encrypt the data.');
        }

        return base64_encode($json);
    }

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @return string
     */
    public function decrypt($payload)
    {
        $payload = $this->_getJsonPayload($payload);
        $iv = base64_decode($payload['iv']);
        $decrypted = openssl_decrypt($payload['value'], $this->_cipher, $this->_key, 0, $iv);

        if ($decrypted === false) {
            throw new AnEncrypterException('Could not decrypt the data.');
        }

        return unserialize($decrypted);
    }

    /**
     * Create a MAC for the given value.
     *
     * @param  string  $iv
     * @param  string  $value
     * @return string
     */
    protected function _hash($iv, $value)
    {
        return hash_hmac('sha256', $iv.$value, $this->_key);
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param  string  $payload
     * @return array
     */
    protected function _getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);

        if (! $this->_validPayload($payload)) {
            throw new AnEncrypterException('The payload is invalid.');
        }

        if (! $this->_validMac($payload)) {
            throw new AnEncrypterException('The MAC is invalid.');
        }

        return $payload;
    }

    /**
     * Verify that the encryption payload is valid.
     *
     * @param  mixed  $payload
     * @return bool
     */
    protected function _validPayload($payload)
    {
        return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']);
    }

    /**
     * Determine if the MAC for the given payload is valid.
     *
     * @param  array  $payload
     * @return bool
     */
    protected function _validMac(array $payload)
    {
        $bytes = function_exists('random_bytes') ? random_bytes(16) : openssl_random_pseudo_bytes(16);
        $calcMac = hash_hmac('sha256', $this->_hash($payload['iv'], $payload['value']), $bytes, true);
        return hash_equals(hash_hmac('sha256', $payload['mac'], $bytes, true), $calcMac);
    }

    /**
     * Get the encryption key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }
}
