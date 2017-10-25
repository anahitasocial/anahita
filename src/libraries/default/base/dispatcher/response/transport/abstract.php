<?php

 /**
  * Dispatcher Response Transport.
  *
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */
 abstract class LibBaseDispatcherResponseTransportAbstract extends KObject
 {
     /**
      * Response object.
      *
      * @var LibBaseDispatcherResponse
      */
     protected $_response;

     /**
      * Constructor.
      *
      * @param KConfig $config An optional KConfig object with configuration options.
      */
     public function __construct(KConfig $config)
     {
         parent::__construct($config);
         $this->_response = $config->response;
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
             'response' => null,
         ));

         parent::_initialize($config);
     }

     /**
      * Return the transport response.
      *
      * @return LibBaseDispatcherResponse
      */
     public function getResponse()
     {
         return $this->_response;
     }

     /**
      * Send the headers.
      */
     public function sendHeaders()
     {
         if (! headers_sent()) {
             $response = $this->getResponse();
             foreach ($response->getHeaders() as $name => $value) {
                 header($name.': '.$value, false);
             }
             header('HTTP/1.1'.' '.$response->getStatusCode().' '.$response->getStatusMessage());
         }

         return $this;
     }

     /**
      * Sends the response body.
      */
     public function sendContent()
     {
         print $this->getResponse()->getContent();
         return $this;
     }

     /**
      * Sends the transport.
      */
     public function send()
     {
         $response = $this->getResponse();

         if (in_array($response->getStatusCode(), array(204, 304))) {
             $response->setContent(null);
         }

        //Send headers and content
        $this->sendHeaders()->sendContent();
        fastcgi_finish_request();
     }
 }
