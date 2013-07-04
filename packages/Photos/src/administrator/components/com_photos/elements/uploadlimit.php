<?php 
/**
 * @version		$Id
 * @category	Anahita
 * @package		Anahita_Social_Applications
 * @subpackage	Photos
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

class JElementUploadlimit extends JElement
{
	function fetchElement($name, $value, &$node, $control_name)
	{
		$attr = '';
		$values = array();
		$uploadMaxFilesize = (int) ini_get('upload_max_filesize');
		$postMaxSize = (int) ini_get('post_max_size');
		$name =  $control_name.'['.$name.'][]';
		
		if($postMaxSize > 10)
			$postMaxSize = 10;
			
		$limit = ($uploadMaxFilesize < $postMaxSize ) ? $uploadMaxFilesize : $postMaxSize;
		
		for( $i=1; $i< $limit + 1; $i++)
			$values[] = array('text'=> $i, 'value'=> $i);
		
		return JHTML::_('select.genericlist',  $values, $name, $attr, 'value', 'text', $value);
	}
}