<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Mod_Menu
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Navigation Module
 *
 * @category   Anahita
 * @package    Mod_Menu
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ModMenuHtml extends ModBaseHtml
{	
	/**
	 * (non-PHPdoc)
	 * @see LibBaseViewHtml::display()
	 */
	public function display()
	{
		$items	  = pick($this->_state->getList(), array());
		
		$array    = array();
		
		$children = array();
		
		$user = JFactory::getUser();
		
		foreach($items as $item)
		{
			//don't show the menu items that are set to registered/special
			if ( $item->access >= 1 && 
						!$user->id)
				continue;

			//do not display the home link
			if($item->home == 1 && $this->_state->home_menuitem == 0)
			    continue;
						
			//check if it has any children. then claim them
			if ( isset($children[$item->id]) ) {
				$item->subitems = $children[$item->id];
			}
		
			if ( isset($item->parent) && $item->parent > 0 )
			{
				if ( isset($array[$item->parent]) )
				{
					$parent = $array[$item->parent];
					if ( !isset($parent->subitems) ) {
						$parent->subitems = array();
					}
					$parent->subitems[] = $item;
				}
				else {
					$children[$item->parent][] = $item;
				}
			}
			else {
				$array[$item->id] = $item;
			}
		}
		
		$this->items = $array;
		
		return parent::display();
	}
	
	/**
	 * Loads the template
	 * 
	 * (non-PHPdoc)
	 * @see LibBaseViewTemplate::load()
	 */
	public function load($template, array $data = array())
	{
		if ( strpos($template,'type_') === 0 ) {
			if ( !$this->getTemplate()->findFile($template) ) {
				$template = 'type_default';
			}
		}
		
		return parent::load($template, $data);
	}
	
	/**
	 * Cleanup the menu route
	 * 
	 * (non-PHPdoc)
	 * @see LibBaseViewAbstract::getRoute()
	 */
	public function getRoute($route = '', $fqr = true)
	{				
		if ( is_object($route) ) {			
						
			switch($route->type)
			{
				case 'menulink' :
					$query = $route->query;
					if ( isset($query['Itemid']) ) {
						$route = $menu = &JSite::getMenu()->getItem($query['Itemid']);
					}  else {
						$route = null;
					}
					if ( !$route ) break;
				case 'component' : 
					$route = 'index.php?option=com_menu&id='.$route->id;
					break;
				case 'url' :
					return $route->link;
				default :
					return '';
			}
		}
				
		if ( strpos($route, 'index.php?') === 0) {			
			$route  = parent::getRoute($route);
		}
		
		return $route;				
	}
}