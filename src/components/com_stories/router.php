<?php
/**
* @version		$Id$
 * @category	Anahita
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

function StoriesBuildRoute( &$query ) {
	
	$segments = array();
	
	if ( isset($query['view']) ) {
		$segments[] = $query['view'];
		unset($query['view']);
	} 

	if ( isset($query['id']) && !is_array($query['id']) ) {
		$segments[] = $query['id'];
		unset($query['id']);
	}	
	
	return $segments;
}

function StoriesParseRoute( $segments ) {
	
	$vars = array();
	
	$vars['view']   = array_shift($segments);
	
	if ( count($segments) )
		$vars['id'] = array_shift($segments);
				
	return $vars;
}