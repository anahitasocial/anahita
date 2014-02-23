<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_People
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

class ComConnectRouter extends ComBaseRouterDefault
{
    /**
     * (non-PHPdoc)
     * @see ComBaseRouterAbstract::build()
     */
    public function build(&$query)
    {
        $segments = array();
        if ( isset($query['oid']) ) 
        {
            $segments[] = '@'.$query['oid'];
            unset($query['oid']);
        }
            
        $segments = array_merge($segments, parent::build($query));
       
        if ( isset($query['server']) ) 
        {
            $segments[] = 'server';
            $segments[] = $query['server'];
            unset($query['server']);
        }
        if ( isset($query['get']) == 'accesstoken' ) 
        {
            $segments[] = 'token';
            unset($query['get']);
        }
        return $segments;    
    }
    
    /**
     * (non-PHPdoc)
     * @see ComBaseRouterAbstract::parse()
     */
    public function parse(&$segments)
    {        
        $path  = implode('/', $segments);
        $query = array();
        if ( count($segments) && strpos($segments[0],'@') === 0 )
        {
            $query['oid'] = str_replace('@', '', array_shift($segments));
        } 
        
        $query['view'] = array_shift($segments);
        if ( preg_match('#server\/\w+#', $path) ) 
        {                        
            array_shift($segments);
            $query['server'] = array_shift($segments);            
        }
        if ( isset($query['server']) && 
                count($segments) && 
                $segments[0] == 'token' ) {
            $query['get'] = 'accesstoken';           
        }
        return $query;
    }
}