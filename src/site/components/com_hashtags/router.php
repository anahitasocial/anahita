<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Hashtags
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */

/**
 * Hashtag Router
 * 
 * @category   Anahita
 * @package    Com_Hashtags
 */
class ComHashtagsRouter extends ComBaseRouterDefault
{
	/**
     * Build the route
     *
     * @param   array   An array of URL arguments
     * @return  array   The URL arguments to use to assemble the subsequent URL.
     */
 	public function build(&$query)
    {
    	if ( isset($query['alias']) ) 
    	{
    		$query['id'] = $query['alias'];
    		unset($query['alias']);    		
    	}
	
    	return parent::build($query);        
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
    	if(preg_match('/(\d+)-([^\/]+)/', $path, $matches)) 
    	{	
    		print_($matches[2]);
    		
    		$vars['alias'] = $matches[2];
    		$path = str_replace($matches[0], $matches[1], $path);
    		$segments = array_filter(explode('/', $path));    		
    	}
    	
        return $vars;
    }
}