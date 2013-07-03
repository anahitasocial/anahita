<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Style Filter
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseTemplateFilterStyle extends ComDefaultTemplateFilterStyle
{  
    /**
     * Render style information
     * 
     * @param string    The style information
     * @param boolean   True, if the style information is a URL
     * @param array     Associative array of attributes
     * @return string
     */
    protected function _renderStyle($style, $link, $attribs = array())
    {
    	//get the correct URL   	
        if ( $link ) {    		
    		$style   = pick($this->getService('com://site/base.template.asset')->getURL($style), $style);
    	}
    	
    	//if ajax try to get the content of the file
        if(KRequest::type() == 'AJAX' ) {
            if ( $link ) {
            	$file	  = $this->getService('com://site/base.template.asset')->getFilePath($style);
            	if ( $file ) {
            		$style  = file_get_contents($file);
            		$link   = false;
            	}
            }
        }
        
        return parent::_renderStyle($style, $link, $attribs);
    }
}