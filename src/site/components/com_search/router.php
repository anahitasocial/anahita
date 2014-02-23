<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Search
 * @subpackage Dispatcher
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Base Router
 *
 * @category   Anahita
 * @package    Com_Search
 * @subpackage Dispatcher
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSearchRouter extends ComBaseRouterDefault
{    
    /**
     * Build the route
     *
     * @param   array   An array of URL arguments
     * @return  array   The URL arguments to use to assemble the subsequent URL.
     */
    public function build(&$query)
    {
        $segments = array();
        
        if ( isset($query['oid']) ) {
        	$segments[] = '@'.$query['oid'];
        }        
        
        if ( isset($query['q']) ) {
            $segments[] = $query['q'];        
        }
        
        //we don't need the view
        unset($query['view']);
        unset($query['q']);
        
        return $segments;
    }

    /**
     * Parse the segments of a URL.
     *
     * @param   array   The segments of the URL to parse.
     * @return  array   The URL attributes to be used by the application.
     */
    public function parse(&$segments)
    {
        $vars = array();   
        
        if ( preg_match('/@\w+/', current($segments)) ) {
        	$vars['oid'] = str_replace('@','',array_shift($segments));
        }

        if ( count($segments) ) {
            $vars['term'] = array_pop($segments);
        }
            
        return $vars;
    }    
    
}