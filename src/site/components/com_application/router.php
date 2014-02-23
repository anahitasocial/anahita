<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Application
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * JRouter application. Temporary until merged with the KDispatcherRouter
 *
 * @category   Anahita
 * @package    Com_Application
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComApplicationRouter extends LibApplicationRouter
{    
    /**
     * Parses the URI
     * 
     * @param JURI $uri
     * 
     * @return void
     */
	public function parse(&$url)
	{
	    $this->_fixUrlForParsing($url);
	    
	    //if the url path is empty then and no option
	    //isset the add the menu path by default
	    if ( empty($url->path) && !isset($url->query['option'])) {
	        $url->path = 'menu';
	    }
	    
	    $this->_parse($url);
	    
	    if ( empty($url->query['Itemid']) ) {
	        $url->query['Itemid'] = null;
	    }
	}
}
