<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Application
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * JSON view
 *
 * @category   Anahita
 * @package    Lib_Application
 * @subpackage View 
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibApplicationViewJson extends LibBaseViewJson
{
    /**
     * Content
     * 
     * var string|KException
     */
    public $content;
    
    /**
     * Just return the content
     * 
     * @return string
     */
    public function display()
    {
        $this->output = $this->content;
        
        //if content is an exception then
        //handle               
        if ( $this->content instanceof Exception )
        {
            $data   = array(); 
            $data['code']     = $this->content->getCode();
            $data['message']  = (string)$this->content->getMessage();                    
            if ( $this->content instanceof AnErrorException ) {
                $data['errors']  = $this->_toData($this->content->getErrors());
            }                        
           //Encode data
            $this->output = json_encode($data);            
        }
        
        return $this->output;
    }
    
    /**
     * Converts an array of error into an array of serializable data
     * 
     * @param array $error An array of errors
     * 
     * @return array
     */
    protected function _toData($errors)
    {
        $data = array();
        
        foreach($errors as $error) 
        {
            if ( $error instanceof Exception ) {
                $data[] = array('code'=>$error->getCode(),'message'=>$error->getMessage());   
            } elseif ( $error instanceof AnError ) {
                $data[] = $error->toArray();
            }
        }
        
        return $data;
    }
}