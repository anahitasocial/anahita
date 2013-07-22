<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Module Filter
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseTemplateFilterModule extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
                'priority' => KCommand::PRIORITY_LOW,
        ));
    
        parent::_initialize($config);
    }
    
    /**
     * Find any <module></module> elements and inject them into the JDocument object
     *
     * @param string Block of text to parse
     * 
     * @return void
     */
    public function write(&$text)
    {
        $matches = array();
    
        if(preg_match_all('#<module([^>]*)>(.*)</module>#siU', $text, $matches))
        {
            $modules = array();
            foreach($matches[0] as $key => $match)
            {
                $text           = str_replace($match, '', $text);
                                
                $attributes = array(
                    'style'     => 'default',
                    'params'    => '',
                    'title'             => '',
                    'class'             => '',
                    'prepend'   => true
                );
                
                $attributes = array_merge($attributes, $this->_parseAttributes($matches[1][$key]));         
                
                
                if ( !empty($attributes['class']) ) {
                    $attributes['params'] .= ' moduleclass_sfx= '.$attributes['class'];
                }
                
                $module                = new KObject();
                $module->id        = uniqid();
                $module->content   = $matches[2][$key];               
                $module->position  = $attributes['position'];
                $module->params    = $attributes['params'];
                $module->showtitle = !empty($attributes['title']);
                $module->title     = $attributes['title'];                
                $module->name      = $attributes['position'];
                $module->attribs   = $attributes;
                $module->user      = 0;
                $module->module    = 'mod_dynamic';                
                $modules[]                 = $module;
            }
                        
            $mods =& JModuleHelper::_load();
            $mods = array_merge($modules, $mods);
        }

                
        return $this;
    }    
}