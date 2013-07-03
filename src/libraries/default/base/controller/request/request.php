<?php

/** 
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Response
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Request object. 
 * 
 * A temporary request object until moving to Nooku 13.1
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Response
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
 class LibBaseControllerRequest extends KConfig
 {   
     /**
      * Sets a query value
      * 
      * @param string $key
      * @param mixed $value
      * 
      * @return LibBaseControllerRequest
      */  
     public function set($key, $value)
     {
         $this->$key = $value;
     }
     
     /**
      * Return the request format
      * 
      * @param $format The default format 
      * 
      * @return string
      */
     public function getFormat($format = 'html')
     {
         return $this->get('format', 'html');
     }   

     /**
      * set the request format
      * 
      * @param string $format Format
      * 
      * @return void
      */
     public function setFormat($format)
     {
         $this->offsetSet('format', $format);
         return $this;
     }
     
     /**
      * Removes a key from a request
      * 
      * @param string $key
      * 
      * @return void
      */
     public function remove($key)
     {
         $this->offsetUnset($key);
         return $this;
     }
     
     /**
      * Return whether it has a key or not
      * 
      * @param string $key
      * 
      * @return boolean
      */
     public function has($key)
     {
         return isset($this->_data[$key]);
     }
     
     /**
      * Retunr if the request is get
      * 
      * @return boolean
      */
     public function isGet()
     {
         return KRequest::method() == 'GET';
     }
     
     /**
      * Retunr if the request is post
      *
      * @return boolean
      */
     public function isPost()
     {
         return $this->getMethod() == 'POST';
     }

     /**
      * Retunr if the request is post
      *
      * @return boolean
      */
     public function isDelete()
     {
         return $this->getMethod() == 'DELETE';
     }

     /**
      * Return if the request is put
      *
      * @return boolean
      */
     public function isPut()
     {
         return $this->getMethod() == 'PUT';
     }

     /**
      * Return if the request is put
      *
      * @return boolean
      */
     public function getMethod()
     {
         return KRequest::method();
     }     
     
     /**
      * Returns the HTTP referrer.
      *
      * 'referer' a commonly used misspelling word for 'referrer'
      * @see     http://en.wikipedia.org/wiki/HTTP_referrer
      *
      * @param   boolean     Only allow internal url's
      * @return  KHttpUrl    A KHttpUrl object
      */
     public function getReferrer($isInternal = true)
     {
         return KRequest::referrer($isInternal);    
     }
     
     /**
      * Return if the request is ajax
      * 
      * @return boolean
      */
     public function isAjax()
     {
         return KRequest::type() == 'AJAX';
     }
     
 }