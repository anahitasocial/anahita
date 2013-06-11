<?php
/**
 * @version � 1.5.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_BASE') or die();

require_once(JPATH_ADMINISTRATOR . '/templates/rt_missioncontrol_j15/lib/missioncontrol.class.php');

/**
 * @package     missioncontrol
 * @subpackage  admin.elements
 */
class JElementComponentBlacklist extends JElement
{

    var $element;
    function fetchElement($name, $value, &$node, $control_name)
    {
        // Initialize variables.
        $html = array();
        $id = $control_name . $name;

        $this->element = &$node;
        // Initialize some field attributes.
        $class = array_key_exists('class', $node->_attributes) ? ' class="checkboxes ' . (string)$node->_attributes['class'] . '"'
                : ' class="checkboxes"';

        // Start the checkbox field output.
        $html[] = '<fieldset id="' . $id . '"' . $class . ' style="padding:0;margin:0;">';

        // Get the field options.
        $options = $this->getOptions();

        // Build the checkbox field output.
        $html[] = '<ul style="list-style:none;margin:0;padding:0">';
        foreach ($options as $i => $option)
        {

            // Initialize some option attributes.
            $checked = (in_array((string)$option->value, (array)$value) ? ' checked="checked"' : '');
            $class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
            $disabled = !empty($option->disable) ? ' disabled="disabled"' : '';

            // Initialize some JavaScript option attributes.
            $onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

            $html[] = '<li style="border-bottom:1px solid #f6f6f6;padding:2px 0;">';
            $html[] = '<input type="checkbox" id="' . $id . $i . '" name="' . $control_name . '[' . $name . ']['.$option->value.']"' .
                      ' value="' . htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"'
                      . $checked . $class . $onclick . $disabled . '/>';

            $html[] = '<label for="' . $id . $i . '"' . $class . ' style="margin-left:10px;">' . JText::_($option->text) . '</label>';
            $html[] = '</li>';
        }
        $html[] = '</ul>';

        // End the checkbox field output.
        $html[] = '</fieldset>';

        return implode($html);
    }

    public function getOptions()
    {
        // Initialize variables.
        $options = array();

        $components = $this->getComponents();
        foreach ($components as $component)
        {
            $tmp = JHtml::_('select.option', (string)$component->option, trim((string)$component->name), 'value', 'text', false);
            $options[] = $tmp;
        }
        reset($options);


        return $options;
    }

    /**
	 * Load components
	 *
	 * @access	private
	 * @return	array
	 */
	function getComponents()
	{
		$db = &JFactory::getDBO();

		$query = "SELECT `option`, `name` FROM #__components".
                 " WHERE parent = 0 and `option` is not null order by `name`";
		$db->setQuery( $query );

		if (!($components = $db->loadObjectList( 'option' ))) {
			JError::raiseWarning( 'SOME_ERROR_CODE', "Error loading Components: " . $db->getErrorMsg());
			return false;
		}

		return $components;

	}
}
