<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Content
 * @subpackage Router
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Base Router
 *
 * @category   Anahita
 * @package    Com_Content
 * @subpackage Router
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComContentRouter extends ComBaseRouterDefault
{
	public function build(&$query)
	{
		$segments = array();
		if ( isset($query['Itemid']) ) 
		{
			$menu = &JSite::getMenu();
			$menuItem = &$menu->getItem($query['Itemid']);
			if ( $menuItem ) 
			{
				$query['id'] = $menuItem->route;			
				unset($query['view']);
				unset($query['Itemid']);
				unset($query['layout']);
			}
		}
		
		if ( isset($query['view']) ) {
			$segments[] = $query['view'];
			unset($query['view']);			
		}

		if ( isset($query['id']) ) {
			$segments[] = $query['id'];
			unset($query['id']);
		}
				
		return $segments;	
	}
	
	public function parse(&$segments)
	{	
		$vars = array();
		
		$route  = implode('/', $segments);
		$menu 	= &JSite::getMenu();
		$item	= $menu->getItems('route', $route, true);
		if ( $item ) {
			$vars = $item->query;
			$vars['Itemid'] = $item->id;
		}
		
		elseif ( count($segments) == 2 )
		{
			$vars['view'] = array_shift($segments);						
			$vars['id']   = array_shift($segments);
		}		
		
		return $vars;		
	}
}
