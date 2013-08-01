<?php 
/**
 * @version     $Id
 * @category	Com_Subscriptions
 * @package		Template
 * @copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link        http://anahitapolis.com
 */

/**
 * Default Template Helper
 * 
 * @category	Com_Subscriptions
 * @package		Template 
 */
class ComSubscriptionsTemplateHelper extends KTemplateHelperDefault
{
	/**
	 * Renders a plugin parameters
	 *
	 * @param  object $plugin
	 * @return string
	 */
	public function renderParams($plugin, $entity)
	{		 
		 $data	 = $entity->getPluginValues($plugin->name, array());
 		 $file   = JApplicationHelper::getPath( 'plg_xml', $plugin->type.DS.$plugin->name ); 	
		 return $this->getTemplate()->getHelper('form')->render(array('path'=>$file,'data'=>$data, 'group'=>'package','name'=>'plugins['.$plugin->name.']'));		 
	}
	
	/**
	 * Renders an array of packages
	 *
	 * @param  int $selected
	 * @return string
	 */
	public function packages($selected = null)
	{
		$packages	    = $this->getService('repos:subscriptions.package')->getQuery()->fetchSet();		
		$packages	 	= array_combine(AnHelperArray::collect($packages, 'id'),AnHelperArray::collect($packages, 'name'));
		$packages['']	= JText::_('AN-SB-COUPON-SELECT-PACKAGE');
		$packages 		= array_reverse($packages, true);
		return $this->getTemplate()->renderHelper('html.options', $packages, $selected);	
	}
}