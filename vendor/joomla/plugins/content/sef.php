<?php
/**
 * @version		$Id: Sef.php 14401 2010-01-26 14:10:00Z louis $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Sef Content Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 		1.5
 */
class plgContentSef extends JPlugin
{

	/**
	 * Sef prepare content method
	 *
	 * Method is called by the view
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number
	 */
	function onPrepareContent( &$article, &$params, $limitstart )
	{
		global $mainframe;
		
		$base   = JURI::base(true).'/';
		$bodyText 	= $article->text;

		// do the SEF substitutions
       	$regex  = '#href="index.php\?([^"]*)#m';
      	$bodyText = preg_replace_callback( $regex, array('plgContentSef', 'route'), $bodyText );

       	$protocols = '[a-zA-Z0-9]+:'; //To check for all unknown protocals (a protocol must contain at least one alpahnumeric fillowed by :
      	$regex     = '#(src|href)="(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
        $bodyText    = preg_replace($regex, "$1=\"$base\$2\"", $bodyText);
		$regex     = '#(onclick="window.open\(\')(?!/|'.$protocols.'|\#)([^/]+[^\']*?\')#m';
		$bodyText    = preg_replace($regex, '$1'.$base.'$2', $bodyText);
		
		// ONMOUSEOVER / ONMOUSEOUT
		$regex 		= '#(onmouseover|onmouseout)="this.src=([\']+)(?!/|'.$protocols.'|\#|\')([^"]+)"#m';
		$bodyText 	= preg_replace($regex, '$1="this.src=$2'. $base .'$3$4"', $bodyText);
		
		// Background image
		$regex 		= '#style\s*=\s*[\'\"](.*):\s*url\s*\([\'\"]?(?!/|'.$protocols.'|\#)([^\)\'\"]+)[\'\"]?\)#m';
		$bodyText 	= preg_replace($regex, 'style="$1: url(\''. $base .'$2$3\')', $bodyText);
		
		// OBJECT <param name="xx", value="yy"> -- fix it only inside the <param> tag
		$regex 		= '#(<param\s+)name\s*=\s*"(movie|src|url)"[^>]\s*value\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
		$bodyText 	= preg_replace($regex, '$1name="$2" value="' . $base . '$3"', $bodyText);
		
		// OBJECT <param value="xx", name="yy"> -- fix it only inside the <param> tag
		$regex 		= '#(<param\s+[^>]*)value\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"\s*name\s*=\s*"(movie|src|url)"#m';
		$bodyText 	= preg_replace($regex, '<param value="'. $base .'$2" name="$3"', $bodyText);

		// OBJECT data="xx" attribute -- fix it only in the object tag
		$regex = 	'#(<object\s+[^>]*)data\s*=\s*"(?!/|'.$protocols.'|\#|\')([^"]*)"#m';
		$bodyText 	= preg_replace($regex, '$1data="' . $base . '$2"$3', $bodyText);
		
		// restore the editor contents
		$article->text = $bodyText;
	}
	
	/**
     * Replaces the matched tags
     *
     * @param array An array of matches (see preg_match_all)
     * @return string
     */
   	 function route( &$matches )
     {
		$original       = $matches[0];
       	$url            = $matches[1];

		$url = str_replace('&amp;','&',$url);

       	$route          = JRoute::_('index.php?'.$url);
      	return 'href="'.$route;
      }

}
