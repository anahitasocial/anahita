<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Tmpl_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Base theme template helper
 * 
 * @category   Anahita
 * @package    Tmpl_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComApplicationTemplateHelperModules extends LibApplicationTemplateHelperModules
{         
    /**
     * Return a module content. This method checks with a cache to see
     * 
     * @param stdclass $module The module to render
     * @param array    $config The module attributes
     * 
     * @return string
     */
    public function renderModule($module, $config = array())
    {
       //added caching
       if ( is_object($module) ) 
       {
            $params = new JParameter($module->params);
            $conf   =& JFactory::getConfig();
            if ($params->get('cache', 0) && $conf->getValue( 'config.caching' ))
            {
                $cache =& JFactory::getCache($module->module, 'output');
                $cache->setLifeTime( $params->get( 'cache_time', $conf->getValue( 'config.cachetime' ) * 60 ) );
                $cache->setCacheValidation(true);
                $id = $module->id.JFactory::getUser()->get('aid', 0);
                if ( $cache->get($id) === false ) {
                   $cache->store(parent::renderModule($module, $config), $id);    
                }
                
                return $cache->get($id);
            }
       }
       
       return parent::renderModule($module, $config);
    }
}