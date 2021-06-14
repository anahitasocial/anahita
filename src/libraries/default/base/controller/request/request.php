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
  * Request object.
  *
  * A temporary request object until moving to Nooku 13.1
  *
  * @category   Anahita
  *
  * @author     Arash Sanieyan <ash@anahitapolis.com>
  * @author     Rastin Mehr <rastin@anahitapolis.com>
  * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
  *
  * @link       http://www.GetAnahita.com
  */
 class LibBaseControllerRequest extends AnConfig
 {
     /**
      * Sets a query value.
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
      * Return the request format.
      *
      * @param $format The default format
      *
      * @return string
      */
     public function getFormat($format = 'json')
     {
         return $this->get('format', $format);
     }

     /**
      * set the request format.
      *
      * @param string $format Format
      */
     public function setFormat($format)
     {
         $this->offsetSet('format', $format);
         return $this;
     }

     /**
      * Removes a key from a request.
      *
      * @param string $key
      */
     public function remove($key)
     {
         $this->offsetUnset($key);
         return $this;
     }

     /**
      * Return whether it has a key or not.
      *
      * @param string $key
      *
      * @return bool
      */
     public function has($key)
     {
         return isset($this->_data[$key]);
     }

     /**
      * Retunr if the request is get.
      *
      * @return bool
      */
     public function isGet()
     {
         return AnRequest::method() == 'GET';
     }

     /**
      * Retunr if the request is post.
      *
      * @return bool
      */
     public function isPost()
     {
         return $this->getMethod() == 'POST';
     }

     /**
      * Retunr if the request is post.
      *
      * @return bool
      */
     public function isDelete()
     {
         return $this->getMethod() == 'DELETE';
     }

     /**
      * Return if the request is put.
      *
      * @return bool
      */
     public function isPut()
     {
         return $this->getMethod() == 'PUT';
     }
     
     /**
      * Return if the request is patch.
      *
      * @return bool
      */
     public function isPatch()
     {
         return $this->getMethod() == 'PATCH';
     }

     /**
      * Return if the request is put.
      *
      * @return bool
      */
     public function getMethod()
     {
         return AnRequest::method();
     }

     /**
      * Returns the HTTP referrer.
      *
      * 'referer' a commonly used misspelling word for 'referrer'
      *
      * @see     http://en.wikipedia.org/wiki/HTTP_referrer
      *
      * @param   bool     Only allow internal url's
      *
      * @return  AnHttpUrl    A AnHttpUrl object
      */
     public function getReferrer($isInternal = true)
     {
         return AnRequest::referrer($isInternal);
     }

     /**
      * Return if the request is ajax.
      *
      * @return bool
      */
     public function isAjax()
     {
         return AnRequest::type() == 'AJAX';
     }
 }
