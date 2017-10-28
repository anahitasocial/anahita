<?php

 /**
  * Html Transport.
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
 class LibBaseDispatcherResponseTransportHtml extends LibBaseDispatcherResponseTransportAbstract
 {
     /**
      * For all the success HTML responses unless its ajax, perform a redirect if the location
      * is set.
      *
      * (non-PHPdoc)
      *
      * @see LibBaseDispatcherResponseTransportAbstract::sendHeaders()
      */
     public function sendHeaders()
     {
         $response = $this->getResponse();
         $headers = $response->getHeaders();
         $isAjax = $response->getRequest()->isAjax();

         if (isset($headers['Location']) && $response->isSuccess() && !$isAjax) {
             $response->setStatus(KHttpResponse::SEE_OTHER);
         }

         return parent::sendHeaders();
     }
 }
