<?php 
/**
 * @version		Id
 * @category	Anahita
 * @package  	Anahita_Social_Applications
 * @subpackage  Groups
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

function GroupsBuildRoute( &$query ) {
	
	$segments = array();
	
	if ( isset($query['view']) ) {
		$segments[] = $query['view'];
		unset($query['view']);
	} 

	if ( isset($query['id']) ) 
	{
		$id			= $query['id'];
		if ( isset($query['alias']) )
		{
			$id .= '-'.$query['alias'];
			unset($query['alias']);
		}
		
		$segments[] = $id;
		unset($query['id']);		
	}	

	return $segments;
}

function GroupsParseRoute( $segments ) {
	
	$vars = array();
	
	$vars['view']   = array_shift($segments);
	
	if ( count($segments) )
	{
		$vars['id'] = (int)array_shift($segments);		
	}
		
				
	return $vars;
}