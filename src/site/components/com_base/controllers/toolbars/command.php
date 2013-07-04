<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Toolbar command. This class extends the base template object and provides
 * specific methods for setting toolbar attributes
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerToolbarCommand extends LibBaseTemplateObject
{
    /**
     * 
     * @param string $label The command label
     * 
     * @return ComBaseControllerToolbarCommand
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
    
    /**
     * Return the command label
     * 
     * @return string
     */
    public function getLabel()
    {
       return $this->label;   
    }
        
    /**
     * Set the command Href (or URL)
     * 
     * @param string $href The command URL
     * 
     * @return ComBaseControllerToolbarCommand
     */
    public function setHref($href)
    {
        $this->setAttribute('href', (string)JRoute::_($href));   
        return $this;
    }
    
    /**
     * Set the command Href (or URL) 
     * 
     * @return string
     */
    public function getHref()
    {
        $this->getAttribute('href');   
    }

    /**
     * Command data. This is an array of key/value pair or json encoded
     * string of data that is submitted to serve as part of the command
     * 
     * @param string $data Command data
     * 
     * @return ComBaseControllerToolbarCommand
     */
    public function setData($data)
    {
        $this->setAttribute('data-data', $data);   
        return $this;
    }
        
    /**
     * Return the command data
     * 
     * @return string|array A key/value pair array or json encoded string
     */
    public function getData()
    {
        $this->getAttribute('data-data');   
    }
    
    /**
     * Return the command method. 
     * 
     * @return string Return a HTTP method. @see KHttpRequest
     */
    public function getMethod()
    {
        $this->getAttribute('data-method');
    } 
        
    /**
     * Set the command method of submittion. The method must be one of the 
     * KHttpRequest
     * 
     * @param string $method The post method. Must be one of the @see KHttpRequest
     *  
     * @return ComBaseControllerToolbarCommand
     */
    public function setMethod($method)
    {
        $this->setAttribute('data-method',$method);
        return $this;
    }     
    
    /**
     * Calls on of the method above.
     * 
     * @{inheritdoc}
     */
    public function __call($method, $arguments)
    {
       if ( count($arguments) && 
            in_array(strtolower($method), array('method','data','href','label')) )
       {
            $this->{'set'.ucfirst($method)}($arguments[0]);
       } 
       else {
            parent::__call($method, $arguments);
       }
       
       return $this;
    }
}