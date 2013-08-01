<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Medium
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Abstract Medium Router
 * 
 * @category   Anahita
 * @package    Com_Medium
 */
abstract class ComMediumRouterAbstract extends ComBaseRouterDefault
{
    /**
     * Build the route
     *
     * @param   array   An array of URL arguments
     * @return  array   The URL arguments to use to assemble the subsequent URL.
     */
    public function build(&$query)
    {
    	if ( isset($query['alias']) && isset($query['id']) ) {
    		$query['id'] = $query['id'].'-'.$query['alias'];
    		unset($query['alias']);
    	}
    	    	
        $segments = array();
        
        if ( isset($query['oid']) ) 
        {
        	if ( $query['oid'] == 'viewer' ) {
        		$query['oid'] = get_viewer()->uniqueAlias;
        	}        	     
            $segments[] = '@'.$query['oid'];
            unset($query['oid']);
        }
        
        $segments = array_merge($segments, parent::build($query));        
        
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
    	$path = implode('/', $segments);
    	$vars = array();
    	 
    	$matches = array();
    	if ( preg_match('/(\d+)-([^\/]+)/', $path, $matches) ) {
    		$vars['alias'] = $matches[2];
    		$path = str_replace($matches[0], $matches[1], $path);
    		$segments = array_filter(explode('/', $path));
    	}
    	    	
    	$path = implode('/',$segments);
    	    	
    	$matches = array();
        	
    	if ( preg_match('/@(\w+)/', $path, $matches) ) {
    		$vars['oid'] = $matches[1];
    		if ( isset($matches[2]) ) {
    			$vars['view'] = $matches[2];
    		}
    		$path = ltrim(str_replace($matches[0], '', $path),'/');
    		$segments = array_filter(explode('/', $path));    		
    	}
               
        $vars = array_merge($vars, parent::parse($segments));
		
        if ( isset($vars['id']) && current($segments) ) {
            $vars['alias'] = array_shift($segments);
        }
        
        return $vars;
    }
}