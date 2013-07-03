<?php

/** 
 * LICENSE: 
 * 
 * @category   Anahita
 * @package    Com_Bazaar
 * @subpackage Domain_Model
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Extension 
 *
 * @category   Anahita
 * @package    Com_Bazaar
 * @subpackage Domain_Model
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBazaarDomainModelExtension extends KObject 
{
	/**
	 * Get a list of extensions
	 * 
	 * @return array
	 */
	static public function getExtensions()
	{
		$db 	= KService::get('koowa:database.adapter.mysqli');

		$extensions    = array();		
		$extensions[]  = self::getInstance(array('name'=>'anahita','type'=>'system', 'version'=>Anahita::getVersion()));		
		$names  = $db->select('SELECT `option` FROM #__components', KDatabase::FETCH_FIELD_LIST);		
		foreach($names as $name) {
			$file = JPATH_ADMINISTRATOR.'/components/'.$name.'/manifest.xml';
			if ( file_exists($file) ) {
				$data = JApplicationHelper::parseXMLInstallFile($file);
				$extensions[] = self::getInstance(array('name'=>$name, 'version'=>@$data['version'], 'type'=>'component'));
			}
		}
		
		$modules  = $db->select('SELECT `module` AS name, `client_id` FROM #__modules', KDatabase::FETCH_OBJECT_LIST);
		foreach($modules as $module) {
			$path = $module->client_id == 1 ? JPATH_ADMINISTRATOR : JPATH_SITE;
			$file = $path.'/modules/'.$module->name.'/'.$module->name.'.xml';
			if ( file_exists($file) ) {
				$data = JApplicationHelper::parseXMLInstallFile($file);				
				$extensions[] = self::getInstance(array('name'=>$module->name, 'version'=>@$data['version'], 'type'=>'module', 'client'=>$module->client_id));
			}
		}
		
		
		$plugins  = $db->select('SELECT folder, element, client_id FROM #__plugins', KDatabase::FETCH_OBJECT_LIST);
		foreach($plugins as $plugin) {
			$file = JPATH_SITE.'/plugins/'.$plugin->folder.'/'.$plugin->element.'.xml';
			if ( file_exists($file) ) {
				$data = JApplicationHelper::parseXMLInstallFile($file);
				$extensions[] = self::getInstance(array('name'=>'plg_'.$plugin->folder.'_'.$plugin->element, 'version'=>@$data['version'], 'type'=>'plugin', 'client'=>$plugin->client_id));
			}
		}
		
		foreach(array(JPATH_SITE, JPATH_ADMINISTRATOR) as $base)
		{
			$templates = JFolder::folders($base.'/templates');
			
			foreach($templates as $template) {
				$file = $base.'/templates/'.$template.'/templateDetails.xml';
				if ( file_exists($file) ) {
					$data = JApplicationHelper::parseXMLInstallFile($file);					
					$extensions[] = self::getInstance(array('name'=>'tmpl_'.strtolower(@$data['name']), 'version'=>@$data['version'], 'type'=>'template', 'client'=>@$data['client']));
				}
			}
		}

		return $extensions;
	}
	
	/**
	 * Return an instance of Extension
	 * 
	 * @return ComBazaarDomainModelExtension
	 */
	static public function getInstance($config)
	{
		static $instance;
		$config   = new KConfig($config);
		$instance = $instance ? clone $instance : new self();
		$instance->type 	= $config->type;
		$instance->version	= $config->version;
		$instance->name 	= $config->name;
		$instance->client	= $config->client == 1 ? 'admin' : 'site';
		return $instance;
	}
	
	/**
	 * Client ID
	 * 
	 * @var string
	 */
	public $client;
	
	/**
	 * Type
	 * 
	 * @var int
	 */
	public $type;
	
	/**
	 * Name
	 * 
	 * @var name
	 */
	public $name;
	
	/**
	 * Version
	 * 
	 * @var string
	 */
	public $version;
	
	/**
	 * 
	 * @return string
	 */
	public function getId()
	{
	    return $this->name;	    
	}
	
	/**
     * Return the getId
     *
     * @return string
	 */
	public function __toString()
	{
	     return $this->getId();   
	}
}