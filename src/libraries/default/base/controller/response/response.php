<?php


 /**
  * LICENSE: This source file is subject to version 3.01 of the PHP license
  * that is available through the world-wide-web at the following URI:
  * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
  * the PHP License and are unable to obtain it through the web, please
  * send a note to license@php.net so we can mail you a copy immediately.
  *
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
  *
  * @link       http://www.GetAnahita.com
  */

 /**
  * Response Object.
  *
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */
 class LibBaseControllerResponse extends KObject
 {
     /**
      * Response status code.
      *
      * @var int
      */
     protected $_status_code;

     /**
      * Response status message.
      *
      * @var int
      */
     protected $_status_message;

     /**
      * Redirect options.
      *
      * @var KConfig
      */
     protected $_redirect;

     /**
      * Array of headers.
      *
      * @var array
      */
     protected $_headers = array();

     /**
      * Content type.
      *
      * @var string
      */
     protected $_content_type;

     /**
      * Set the response content.
      *
      * @var string
      */
     protected $_content;

     /**
      * Constructor.
      *
      * @param KConfig $config An optional KConfig object with configuration options.
      */
     public function __construct(KConfig $config)
     {
         parent::__construct($config);

         $this->setContent($config->content);
         $this->setContentType($config->content_type);
         $this->setStatus($config->status_code, $config->status_message);
         $this->_headers = $config->headers;
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
             'content' => '',
             'content_type' => 'text/html',
             'status_code' => KHttpResponse::OK,
             'status_message' => 'OK',
             'headers' => array(),
         ));

         parent::_initialize($config);
     }

     /**
      * Return the status code.
      *
      * @return int
      */
     public function getStatusCode()
     {
         return $this->_status_code;
     }

     /**
      * Return the status code.
      *
      * @return string
      */
     public function getStatusMessage()
     {
         return $this->_status_message;
     }

     /**
      *  Set the response status.
      *
      * @param int    $status  The response status code
      * @param string $message The status message
      *
      * @return LibBaseControllerResponseAbstract
      */
     public function setStatus($status, $message = null)
     {
         if (!$status) {
             throw new InvalidArgumentException('Response status is missing');
         }

         if (!$message && !KHttpResponse::getMessage($status)) {
             throw new InvalidArgumentException('Response message is missing');
         }

         $this->_status_code = $status;
         $this->_status_message = $message;
     }

     /**
      * Sets status or a header.
      *
      * @param string $key     A string key
      * @param string $value   A string value
      */
     public function __set($key, $value)
     {
         if ($key == 'status') {
             $this->setStatus($value);
         } elseif ($key == 'redirect') {
             $this->setRedirect($value);
         } elseif ($key == 'content') {
             $this->setContent($value);
         } else {
             $key = implode('-', array_map('ucfirst', AnInflector::explode($key)));
             $this->setHeader($key, $value);
         }
     }

     /**
      * Set a URL for browser redirection.
      *
      * @param string $location The location of the redirect
      * @param string $message  The redirect header message
      * @param int    $code     The redirect header code
      */
     public function setRedirect($location, $message = null, $code = null)
     {
         $this->_redirect = null;

         if (! empty($location)) {

             $isCallable = is_callable(array($location, '__toString'));

             if (! is_string($location) && !is_numeric($location) && !$isCallable) {
                 throw new \UnexpectedValueException(
                    'The Response location must be a string or object implementing __toString(), "'.gettype($location).'" given.'
                );
             }

             if (! $code) {
                 $code = $this->_status_code;
             }

             $this->setStatus($code, $message);
             $this->setHeader('Location', $location);
         }

         return $this;
     }

     /**
      * Set the response content.
      *
      * @param string $content The response content
      *
      * @return LibBaseControllerResponseAbstract
      */
     public function setContent($content)
     {
         $this->_content = $content;
         return $this;
     }

     /**
      * Return the response content. If the content is a closure
      * or a object implementing __toString then evaluate first
      * then return the content.
      *
      * @return string
      */
     public function getContent()
     {
         return $this->_content;
     }

     /**
      * Set the content type.
      *
      * @param string $type
      *
      * @return LibBaseControllerResponseAbstract
      */
     public function setContentType($type)
     {
         $this->_content_type = $type;
         $this->_headers['Content-Type'] = $type.'; charset=utf-8';

         return $this;
     }

     /**
      * Return the content type.
      *
      * @return string
      */
     public function getContentType()
     {
         return $this->_content_type;
     }

     /**
      * Set the header value.
      *
      * @param string $name   The header name
      * @param string $value  The header value
      */
     public function setHeader($name, $value)
     {
         $this->_headers[$name] = $value;
     }

     /**
      * Removes a header.
      *
      * @param string $name The header name
      */
     public function removeHeader($name)
     {
         unset($this->_headers[$name]);
     }

     /**
      * Return the value for a header.
      *
      * @param string $name Header name
      *
      * @return string
      */
     public function getHeader($name)
     {
         return isset($this->_headers[$name]) ? $this->_headers[$name] : null;
     }

     /**
      * Return all the headers.
      *
      * @return KConfig
      */
     public function getHeaders()
     {
         return $this->_headers;
     }

    /**
     * Check if an http status code is an error.
     *
     * @return bool TRUE if the status code is an error code
     */
    public function isError()
    {
        return ($this->getStatusCode() >= 400);
    }

    /**
     * Do we have a redirect.
     *
     * @return bool
     */
    public function isRedirect()
    {
        $code = $this->getStatusCode();
        return (300 <= $code && 400 > $code);
    }

    /**
     * Was the response successful.
     *
     * @return bool
     */
    public function isSuccess()
    {
        $code = $this->getStatusCode();
        return (200 <= $code && 300 > $code);
    }
 }
