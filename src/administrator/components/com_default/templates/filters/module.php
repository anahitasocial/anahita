<?php
/**
 * @version     $Id: module.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Module Template Filter
 * 
 * This filter allow to dynamically inject data into module position.
 * 
 * Filter will parse elements of the form <modules position="[position]">[content]</modules> 
 * and prepend or append the content to the module position. 
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateFilterModule extends KTemplateFilterAbstract implements KTemplateFilterWrite
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
	 * @return ComDefaultTemplateFilterModule
	 */
    public function write(&$text)
    {   
		$matches = array();
		
		if(preg_match_all('#<module([^>]*)>(.*)</module>#siU', $text, $matches)) 
		{	
		    foreach($matches[0] as $key => $match)
			{
			    //Remove placeholder
			    $text = str_replace($match, '', $text);
			    
			    //Create attributes array
				$attributes = array(
					'style' 	=> 'component',
					'params'	=> '',	
					'title'		=> '',
					'class'		=> '',
				    'content'   => 'prepend'
				);
				
		        $attributes = array_merge($attributes, $this->_parseAttributes($matches[1][$key])); 
				
		        //Create module object
			    $module   	       = new KObject();
			    $module->id        = uniqid();
				$module->content   = $matches[2][$key];
				$module->position  = $attributes['position'];
				$module->params    = $attributes['params'];
				$module->showtitle = !empty($attributes['title']);
				$module->title     = $attributes['title'];
				$module->attribs   = $attributes;
				$module->user      = 0;
				$module->module    = 'mod_dynamic';
				
				//Store the module for rendering
			    JFactory::getDocument()->modules[$attributes['position']][] = $module;
			}
		}
		
		return $this;
    }    
}

/**
 * Modules Renderer
 * 
 * This is a specialised modules renderer which prepends or appends the dynamically created modules 
 * to the list of modules before rendering them.
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class JDocumentRendererModules extends JDocumentRenderer
{
	public function render( $position, $params = array(), $content = null )
	{
        //Get the modules
		$modules = JModuleHelper::getModules($position);
		
		if(isset($this->_doc->modules[$position])) 
		{
		    foreach($this->_doc->modules[$position] as $module) 
		    { 
		        switch($module->attribs['content'])
		        {
		            case 'append'  :  
		                array_unshift($modules, $module); 
		                break;
		                 
		            case 'replace' :
		                 unset($modules);
		                $modules[] = $module;
		                break;
		                
		            case 'prepend' :    
		            default        :
		                array_push($modules, $module); 
		        }
		    }
		}
		
		//Render the modules
		$renderer = $this->_doc->loadRenderer('module');
		
		$contents = '';
		foreach ($modules as $module)  {
			$contents .= $renderer->render($module, $params, $content);
		}
		
		return $contents;
	}
}