<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Lib_Theme
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Grid Template Helper
 * 
 * @category   Anahita
 * @package    Lib_Theme
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibThemeTemplateHelperModules extends KTemplateHelperAbstract
{     
    /**
     * Renders a module
     *
     * @param stdclass $module  Renders a module object
     * @param array    $config  Module default attribs   
     * 
     * @return string
     */
    public function render($modules, $config = array())
    {
        if ( is_string($modules) ) {
            $modules = JModuleHelper::getModules($modules);    
        }
        
        $content = null;
        
        foreach($modules as $module) {
            $content .= $this->renderModule($module, $config);
        }
        
        return $content;
    }
    
    /**
     * Return a module content
     * 
     * @param stdclass $module The module to render
     * @param array    $config The module attributes
     * 
     * @return string
     */
    public function renderModule($module, $config = array())
    {
        if ( is_string($module) ) 
        {
            $module = JModuleHelper::getModule($module);
            if ( empty($module) ) {
                return;
            } 
        }

        if ( isset($module->attribs) ) {
            $config = array_merge($module->attribs, $config);
        }
        
        $config = new KConfig($config);
        
        $config->append(array(
            'style' => 'default'
        ));
                
        $params = new JParameter( $module->params );        
        
        //create an identifier
        $name = preg_replace('/[^A-Z0-9_\.-]/i', '', $module->module);
        $name = str_replace('mod_','mod://'.$this->getIdentifier()->application.'/', $name).'.'.$name;
        
        $identifier = new KServiceIdentifier($name);
        $path       = $identifier->filepath;
        
        // Load the module
        if (!$module->user && file_exists( $path ) )
        {
            $lang =& JFactory::getLanguage();
            $lang->load($module->module);
            
            ob_start();
            require $path;
            $content = ob_get_contents();
            ob_end_clean();
            $module->content = empty($content) ? $module->content : $content;
        }
               
        if ( !$this->_template->findPath('chromes/'.$config->style.'.php') ) {
            $config->style = 'default';
        }
        
        return $this->_template
            ->loadTemplate('chromes/'.$config->style, array(
                'module'  => $module,
                'params'  => $params,
                'attribs' => $config
            ))->render();
    }
}