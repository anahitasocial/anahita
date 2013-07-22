<?php
/**
 * @version � 1.5.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.base.tree');

class RTAdminCSSMenu extends JTree
{
	/**
	 * CSS string to add to document head
	 * @var string
	 */
	var $_css = null;
	var $_menudata = null;

	function __construct()
	{
		$this->_root = new JMenuNode('ROOT');
		$this->_current = & $this->_root;
	}
	
	function init($menudata) 
	{
		$this->_menudata = $menudata;
	}

	function addSeparator()
	{
		$this->addChild(new JMenuNode(null, null, 'separator', false));
	}

	function renderMenu($id = 'menu', $class = '')
	{
		global $mainframe;

		$depth = 1;

		if(!empty($id)) {
			$id='id="'.$id.'"';
		}

		if(!empty($class)) {
			$class='class="'.$class.'"';
		}

		/*
		 * Recurse through children if they exist
		 */
		while ($this->_current->hasChildren())
		{
			echo "<ul ".$id." ".$class.">\n";
			foreach ($this->_current->getChildren() as $child)
			{
				$this->_current = & $child;
				$this->renderLevel($depth);
			}
			echo "</ul>\n";
		}

		if ($this->_css) {
			// Add style to document head
			$doc = & JFactory::getDocument();
			$doc->addStyleDeclaration($this->_css);
		}
	}

	function renderLevel($depth)
	{
		/*
		 * Build the CSS class suffix
		 */

		
		$class = 'li-'.RTAdminCSSMenu::cleanName($this->_current->title);
		
		if(strpos($this->_current->class,'separator')!==false) {
			$class .= ' separator';
		}

		if(strpos($this->_current->class,'disabled')!==false) {
			$class .= ' disabled';
		}
		
		if ($this->_current->hasChildren()) {
			$class .= ' parent';
			$this->_current->class.= ' daddy';
			
		}
		
		
		if ($depth == 1) {
			$class .= ' root';
			if (RTAdminCSSMenu::_isActive($this->_current->title))
				$class .= ' active';
		}
		
		$this->_current->class.= ' item';

		//increment depth 
		$depth++;
		
		/*
		 * Print the item
		 */
		echo '<li class="'.$class.'">';

		/*
		 * Print a link if it exists
		 */
		if ($this->_current->link != null) {
			echo "<a class=\"".$this->_current->class."\" href=\"".$this->_current->link."\"><span>".$this->_current->title."</span></a>";
		} elseif ($this->_current->title != null) {
			echo "<span class=\"".$this->_current->class."\"><span>".$this->_current->title."</span></span>\n";
		} else {
			echo "<span></span>";
		}

		/*
		 * Recurse through children if they exist
		 */
		while ($this->_current->hasChildren())
		{
			if ($this->_current->class) {
				echo '<ul class="level'.$depth.' parent-'.RTAdminCSSMenu::cleanName($this->_current->title).'">'."\n";
			} else {
				echo '<ul>'."\n";
			}
			foreach ($this->_current->getChildren() as $child)
			{
				$this->_current = & $child;
				$this->renderLevel($depth);
			}
			echo "</ul>\n";
		}
		echo "</li>\n";
	}
	
	function _isActive($toplevel) {
	
		global $option;
		
		$menus =& $this->_menudata;
		
		
		switch ($toplevel) {

		
			case 'Dashboard':
				if ($option == 'com_cpanel') return true;
				break;
				
			case 'Articles':
				if (RTAdminCSSMenu::_isOption($menus['Dashboard'])) return true;
				break;
				
			case 'Menus':
				if (RTAdminCSSMenu::_isOption($menus['Articles'])) return true;
				break;
				
			case 'Users':
				if (RTAdminCSSMenu::_isOption($menus['Users'])) return true;
				break;
				
			case 'Extend':
				if (RTAdminCSSMenu::_isOption($menus['Extend'])) return true;
				break;
				
			case 'Configure':
				if (RTAdminCSSMenu::_isOption($menus['Config'])) return true;
				break;
		
			case 'Help':
				if (RTAdminCSSMenu::_isOption($menus['Help'])) return true;
				break;	
		
		}
		return false;
		
	}
	
	function _isOption($opts_array) {
	
		global $option;
		
		foreach ($opts_array as $opts) {
			$bits = explode(':',$opts);
			if (sizeof($bits) == 2) {
				$query = explode('=',$bits[1]);
				if ($option == $bits[0] && JRequest::getString($query[0]) == $query[1]) return true;
			} else {
				if ($option == $bits[0]) return true;
			}
		}
		return false;
	}
	
	function cleanName($name) {
	
		$name = strtolower(strip_tags(str_replace(' ','-',$name)));
		$name = str_replace('/','-',$name);
		return $name;
	}
}

class JMenuNode extends JNode
{
	/**
	 * Node Title
	 */
	var $title = null;

	/**
	 * Node Id
	 */
	var $id = null;


	/**
	 * Node Link
	 */
	var $link = null;

	/**
	 * CSS Class for node
	 */
	var $class = null;

	/**
	 * Active Node?
	 */
	var $active = false;


	function __construct($title, $link = null, $class = null, $active = false)
	{
		$this->title	= $title;
		$this->link		= JFilterOutput::ampReplace($link);
		$this->class	= $class;
		$this->active	= $active;
		$this->id		= str_replace(" ","-",$title);

	}


}