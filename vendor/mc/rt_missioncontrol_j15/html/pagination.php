<?php
/**
 * @version � 1.5.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * This is a file to add template specific chrome to pagination rendering.
 *
 * pagination_list_footer
 * 	Input variable $list is an array with offsets:
 * 		$list[limit]		: int
 * 		$list[limitstart]	: int
 * 		$list[total]		: int
 * 		$list[limitfield]	: string
 * 		$list[pagescounter]	: string
 * 		$list[pageslinks]	: string
 *
 * pagination_list_render
 * 	Input variable $list is an array with offsets:
 * 		$list[all]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[start]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[previous]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[next]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[end]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[pages]
 * 			[{PAGE}][data]		: string
 * 			[{PAGE}][active]	: boolean
 *
 * pagination_item_active
 * 	Input variable $item is an object with fields:
 * 		$item->base	: integer
 * 		$item->link	: string
 * 		$item->text	: string
 *
 * pagination_item_inactive
 * 	Input variable $item is an object with fields:
 * 		$item->base	: integer
 * 		$item->link	: string
 * 		$item->text	: string
 *
 * This gives template designers ultimate control over how pagination is rendered.
 *
 * NOTE: If you override pagination_item_active OR pagination_item_inactive you MUST override them both
 */

function pagination_list_footer($list)
{
	// Initialize variables
	$lang =& JFactory::getLanguage();
	$html  = "<div class=\"mc-limit\">".$list['limitfield'].'<span>'.JText::_('Display Num')." </span></div>";
	$html .= "<div class=\"mc-page-count\">".$list['pagescounter']."</div>";
	$html .= "<del class=\"mc-pagination-container\">\n";
	$html .= "<div class=\"mc-pagination\">";
	$html .= $list['pageslinks'];
	$html .= "</div>";
	$html .= "</del>\n";
	$html .= "<input type=\"hidden\" name=\"limitstart\" value=\"".$list['limitstart']."\" />";

	return $html;
}

function pagination_list_render($list)
{
	// Initialize variables
	$lang =& JFactory::getLanguage();
	$html = null;

	if ($list['start']['active']) {
		$html .= "<div class=\"page-button\"><div class=\"start\">".$list['start']['data']."</div></div>";
	} else {
		$html .= "<div class=\"page-button off\"><div class=\"start\">".$list['start']['data']."</div></div>";
	}
	if ($list['previous']['active']) {
		$html .= "<div class=\"page-button\"><div class=\"prev\">".$list['previous']['data']."</div></div>";
	} else {
		$html .= "<div class=\"page-button\"><div class=\"prev\">".$list['previous']['data']."</div></div>";
	}

	$html .= "\n<div class=\"pages\"><div class=\"page\">";
	foreach( $list['pages'] as $page ) {
		$html .= $page['data'];
	}
	$html .= "\n</div></div>";

	if ($list['next']['active']) {
		$html .= "<div class=\"page-button\"><div class=\"next\">".$list['next']['data']."</div></div>";
	} else {
		$html .= "<div class=\"page-button off\"><div class=\"next\">".$list['next']['data']."</div></div>";
	}
	if ($list['end']['active']) {
		$html .= "<div class=\"page-button\"><div class=\"end\">".$list['end']['data']."</div></div>";
	} else {
		$html .= "<div class=\"page-button off\"><div class=\"end\">".$list['end']['data']."</div></div>";
	}

	return $html;
}

function pagination_item_active(&$item)
{
    $url   = clone KRequest::url();
    $query = $url->getQuery(true);
    
    //For compatibility with Joomla use limitstart instead of offset
    $query['limitstart'] = $item->base;
    
    $url->setQuery($query);
    
    return "<a  title=\"".$item->text."\" href=\"$url\" >".$item->text."</a>";	
}

function pagination_item_inactive(&$item)
{
	return "<span>".$item->text."</span>";
}
?>
