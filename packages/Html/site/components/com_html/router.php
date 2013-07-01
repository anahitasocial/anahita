<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Topics
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

class ComHtmlRouter extends ComBaseRouterAbstract 
{
    /**
     * (non-PHPdoc)
     * @see ComBaseRouterAbstract::build()
     */
    public function build(&$query)
    {
        unset($query['view']);
        $segments = array();
        if ( isset($query['layout']) )
        {
            $query['layout'] = urldecode($query['layout']);
            $segments = explode('/', $query['layout']);            
        }        
        unset($query['layout']);
        return $segments;
    }
    
    /**
     * (non-PHPdoc)
     * @see ComBaseRouterAbstract::parse()
     */
    public function parse(&$segments)
    {
        $query = array('view'=>'content');
        if ( count($segments) ) {
            $query['layout'] = str_replace('-','_', implode('/', $segments));
        }        
        return $query;
    }    
}
