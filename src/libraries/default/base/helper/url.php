<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Template helper used to build an URL. 
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseHelperUrl extends KObject implements KServiceInstantiatable
{
    /**
     * Force creation of a singleton
     *
     * @param KConfigInterface 	$config    An optional KConfig object with configuration options
     * @param KServiceInterface	$container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier))
        {
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }
    
        return $container->get($config->service_identifier);
    }
        
	/**
	 * Get a Route for a URL
	 * 
	 * @param string|array $url    The url. Can be query fragments 
	 * @param boolean      $fqr    Fully qualified route
	 * 
	 * @return string
	 */
	public function getRoute($url='', $fqr = true)
	{
		if ( !is_array($url) )
		{
		    if ( strpos($url, 'http') === 0 ) {
		        return $url;
		    }
		    if ( strpos($url, '/') === 0 ) {
		        return JRoute::_($url);
		    }
		    $url = str_replace('index.php?', '', trim($url));
		    $vars = array();
		    parse_str($url, $vars);
		    $url = $vars;
		}
        
		$parts = array();
				
		if ( !isset($url['option']) ) {
		    $parts['option'] = KRequest::get('get.option','cmd');
		}

		//if not view is set the set
		if ( !isset($url['view']) ) 
		{
		    $parts['view'] = KRequest::get('get.view','cmd');

		    //only try to set the layout if we are setting the view
		    if ( !isset($url['layout']) && KRequest::has('get.layout') ) {
		        $parts['layout'] = KRequest::get('get.layout','cmd');
		    }
		}
		
		//carry format
		if ( !isset($url['format']) && KRequest::has('get.format') ) {
		    $parts['format'] = KRequest::get('get.format','cmd');
		}	
						
		foreach($parts as $key => $value)
		{
		    $url[$key] = $value;
		}
		
		//unset html format since it's a default
		if ( isset($url['format']) && $url['format'] == 'html' ) {
		    unset($url['format']);
		}
		
		//unset default lyout since it's a default
		if ( isset($url['layout']) && $url['layout'] == 'default' ) {
		    unset($url['layout']);
		}
		
		$route  = JRoute::_('index.php?'.http_build_query($url), false);
		
		return $route;
	}
}
 