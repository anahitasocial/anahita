<?php
/**
 * @version � 1.5.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

//Register the session storage class with the loader
JLoader::register('JButton', dirname(__FILE__).DS.'toolbar'.DS.'button.php');

/**
 * ToolBar handler
 *
 * @package 	Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
class RTToolbar extends JToolBar
{
	
	var $_actions = array();
	var $_first = array();


	/**
	 * Render
	 *
	 * @access	public
	 * @param	string	The name of the control, or the default text area if a setup file is not found
	 * @return	string	HTML
	 */
	function render()
	{
		//The toolbar isn't used, fall back to module buffer to make sure the custom toolbar renders
		//@TODO likely no longer needed
		/*
		if(!$this->_bar && $this->_name == 'toolbar') {
		
			$document = JFactory::getDocument();
			$buffer = $document->getBuffer();

			if(isset($buffer['modules']['toolbar'])) return $buffer['modules']['toolbar'];
		}
		//*/
	
		$html = array ();

		// Start toolbar div
		$html[] = '<div class="mc-toolbar '.$this->_name.'" id="'.$this->_name.'">';
		$html[] = '<ul>';
		
		foreach ($this->_first as $button) {
			$html[] = $this->renderButton($button,'button special');
		}
		
		if (count($this->_actions) > 0) {
			if (count($this->_actions) > 1) {
				$html[] = '<li class="button dropdown"><a href="#" id="actionsToggle"><span class="select-active">Actions</span><span class="select-arrow">&#x25BE;</span></a>';
				$html[] = '<ul class="mc-dropdown">';
				foreach ($this->_actions as $button) {
					$html[] = $this->renderButton($button,'sub');
				}
				$html[] = '</ul>';
				$html[] = '</li>';
			} else {
				$html[] = $this->renderButton($this->_actions[0]);
			}
		}

		// Render each button in the toolbar
		foreach ($this->_bar as $button) {
			$html[] = $this->renderButton($button);
		}
		
		

		// End toolbar div
		$html[] = '</ul>';
		$html[] = '</div>';

		return implode("\n", $html);
	}

	/**
	 * Render a parameter type
	 *
	 * @param	object	A param tag node
	 * @param	string	The control name
	 * @return	array	Any array of the label, the form element and the tooltip
	 */
	function renderButton( &$node, $class=null )
	{
		if(is_object($node) && method_exists($node, 'render')) return str_replace('td', 'li', $node->render());
	
		// Get the button type
		$type = $node[0];

		$button = & $this->loadButtonType($type);
		if (method_exists($button,'setClass')) $button->setClass($class);

		/**
		 * Error Occurred
		 */
		if ($button === false) {
			return JText::_('Button not defined for type').' = '.$type;
		}
		
		$button_output = $button->render($node);
		
		return $button_output;
	}

}