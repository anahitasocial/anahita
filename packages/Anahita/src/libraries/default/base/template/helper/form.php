<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Form Helper
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseTemplateHelperForm extends KTemplateHelperAbstract 
{			
	/**
	 * Renders a form using an xml path
	 *
	 * @param  array $config
	 * @return void
	 */
	public function render($config=array())
	{		
		$config      = new KConfig($config);
		
		$config->append(array(
			'group' 	=> '_default',
			'name'		=> 'params'
		));
		
        $parameter	= $this->getParameters($config);
        
        return $this->_render($parameter, $config);
	}
	
	/**
	 * Renders a form using an xml path
	 *
	 * @param  array $config
	 * @return void
	 */
	public function getParameters($config=array())
	{
		$config    = new KConfig($config);
		
		$config->append(array(
			'data'  			=> array(),					
			'element_paths'		=> array(dirname(__FILE__).'/forms')
		));
		
		$content 	 = file_exists($config->path) ? file_get_contents($config->path) : '';
		$paths	 	 = array();
		
		//replace all the addpath="{KServiceIdentifier}" with real path
		if ( preg_match_all('/addpath="([^"]+)"/', $content, $paths) ) 
		{
			$replaces = array();
			foreach($paths[1] as $path) 
			{
				if ( strpos($path,'.') ) {
					$replaces[] = str_replace(JPATH_ROOT.'/', '', dirname(KLoader::path($path.'.dummy')));
				} else
					$replaces[] = $path;
			}
			$content = str_replace($paths[1], $replaces, $content);
		}		
		
		$xml 	 	  	 = & JFactory::getXMLParser('Simple');
		
		$parameter    	 = new JParameter('');
		$data			 = KConfig::unbox($config->data);
		
		if ( $data instanceof JParameter )
		    $data = $data->toArray();
		
		if ( is_array($data) )
			$parameter->loadArray($data);
		else
			$parameter->loadINI($data);
			
		$parameter->template_data = $config->template_data;
			
		foreach($config->element_paths as $path )	
			$parameter->addElementPath($path);
		
		if ($xml->loadString($content))
			if ($params = & $xml->document->params) 
				foreach ($params as $param)
					$parameter->setXML( $param );
					
		return $parameter;
	}
	
	/**
	 * Render a parameter
	 * 
	 * @param  JParameter $parameter
	 * @param  KConfig 	  $config
	 * @return string
	 */
	protected function _render($parameter, $config)
	{
		return $parameter->render($config->name, $config->group);
	}
}