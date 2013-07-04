<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Abstract Actor Router
 * 
 * @category   Anahita
 * @package    Com_Actors
 */
abstract class ComActorsRouterAbstract extends ComBaseRouterDefault
{
    /**
     * Build the route
     *
     * @param   array   An array of URL arguments
     * @return  array   The URL arguments to use to assemble the subsequent URL.
     */
    public function build(&$query)
    {
    	if ( isset($query['alias']) && isset($query['id']) ) 
    	{
    		if ( !isset($query['get']) ) {
    			$query['id'] = $query['id'].'-'.$query['alias'];
    		}
    		
    		unset($query['alias']);	
    	}
    	
        $has_id = isset($query['id']);
        $segments = parent::build($query);
        
        if ( $has_id ) 
        {
        	if ( isset($query['get']) ) {
        		$segments[] = $query['get'];
        		if ( $query['get'] == 'graph' ) {
        			if ( !isset($query['type']) ) {
        				$query['type'] = 'followers';
        			}
        			$segments[] = $query['type'];
        			unset($query['type']);
        		}
        		unset($query['get']);
        	}
        } 
        else if ( isset($query['oid']) ) 
        {
        	if ( $query['oid'] == 'viewer' ) {
        		$query['oid'] = get_viewer()->uniqueAlias;
        	}
        	$segments[] = '@'.$query['oid'];
        	unset($query['oid']);
        }
        
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
    	
    	$last = AnHelperArray::getValueAtIndex($segments, AnHelperArray::LAST_INDEX);    	
    	
    	if ( preg_match('/@\w+/', $last) ) {
    		$vars['oid'] = str_replace('@','',array_pop($segments));
    	}
		
        $vars = array_merge($vars, parent::parse($segments));
        
        if ( isset($vars['get']) && $vars['get'] == 'graph' ) {        	
        	$vars['type'] = count($segments) ? array_shift($segments) : 'followers';        	
        }
       
       
        return $vars;
    }
}