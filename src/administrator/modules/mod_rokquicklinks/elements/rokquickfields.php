<?php
/**
 * @package RokQuickLinks - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die( 'Restricted access' );

class JElementRokQuickFields extends JElement {
	
	var	$_name = 'rokquickfields';
	var $directory = null;
	
	function fetchElement($name, $value, &$node, $control_name){
		
		$document 	=& JFactory::getDocument();
		$path = JURI::Root(true)."/administrator/modules/mod_rokquicklinks/";
		$document->addStyleSheet($path.'admin/css/quickfields.css');
		$document->addScript($path.'admin/js/quickfields'.$this->_getJSVersion().'.js');
		
		$value = str_replace("'", '"', $value);
		$this->directory = $node->attributes('directory');
		
		$output = "";
		
		// hackish way to close tables that we don't want to use
		$output .= '</td></tr><tr><td colspan="2">';
		
		// real layout
		$output .= '<table class="admintable quicklinkstable"><tr><td><div id="quicklinks-admin">'."\n";
		$output .=  $this->populate($value);
		$output .= '</div></tr></td></table>'."\n";
		
		$output .= "<input id='quicklinks-dir' value='".JURI::Root(true).$this->directory ."' type='hidden' />";
		$output .= "<input id='params".$name."' name='params[".$name."]' type='hidden' value='".$value."' />";
		
		echo $output;
	}
	
	function populate($value){
		$blocks = json_decode($value, true);
		$output = '';
		
		for($i = 1; $i <= count($blocks); $i++){
			$output .= $this->layout($blocks[$i - 1], $i);
		}
		
		return $output;
	}
	
	function populateIcons($selectedIcon = false){
		$path = JPATH_ROOT . str_replace('/', DS, $this->directory);
		$icons = scandir($path);
		$output = '';
		
		foreach($icons as $icon){
			$pathinfo = pathinfo($icon);
			$ext = $pathinfo['extension'];
			
			if ($ext == 'png' || $ext == 'jpg' || $ext == 'bmp' || $ext == 'gif'){
				if (basename($selectedIcon) == $pathinfo['filename'] . "." . $ext) $selected = ' selected="selected"';
				else $selected = '';
				
				$output .= '<option value="'.$pathinfo['basename'].'"'.$selected.'>'.$pathinfo['filename'].'</option>'."\n";
			}
		}
		
		return $output;
	}
	
	function layout($block, $index){
		$icon = JUri::root(true) . $this->directory . $block['icon'];
		$title = $block['title'];
		$link = $block['link'];
		
		return '
			<div class="quicklinks-block">
				<div class="quicklinks-icon"><img src="'.$icon.'" /></div>
				<div class="quicklinks-title">
					<span>Title</span>
					<input class="text_area quick-input" id="paramstitle-'.$index.'" name="params[title-'.$index.']" value="'.$title.'" type="text" />
				</div>
				<div class="quicklinks-link">
					<span>Link</span>
					<input class="text_area quick-input" id="paramslink-'.$index.'" name="params[link-'.$index.']" value="'.$link.'" type="text" />
				</div>
				<div class="quicklinks-iconslist">
					<span>Icon</span>
					<select class="inputbox quicklinks-select" id="paramsicon-'.$index.'" name="params[icon-'.$index.']">
						'.$this->populateIcons($icon).'
					</select>
				</div>
				
				<div class="quicklinks-controls">
					<div class="quicklinks-add"></div>
					<div class="quicklinks-remove"></div>
				</div>
				<div class="quicklinks-move"></div>
			</div>
		';
	}
	
	function _getJSVersion() {
		if (version_compare(JVERSION, '1.5', '>=') && version_compare(JVERSION, '1.6', '<')){
			if (false && JPluginHelper::isEnabled('system', 'mtupgrade')){
				return "-mt1.2";
			} else {
				return "";
			}
		} else {
			return "";
		}
	}
	
}