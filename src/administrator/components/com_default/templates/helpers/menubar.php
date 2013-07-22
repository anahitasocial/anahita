<?php
/**
 * @version     $Id: menubar.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Template Menubar Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateHelperMenubar extends KTemplateHelperAbstract
{
	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KConfig $config)
    {
    	$config->append(array(
    		'menubar' => null,
        ));

        parent::_initialize($config);
    }

 	/**
     * Render the menubar
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function render($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'menubar' => null
        ));

		$html = '';

		if (version_compare(JVERSION, '1.7', 'ge')) {
			$html .= '<div id="submenu-box"><div class="m">';
		}

        $html .= '<ul id="submenu">';
	    foreach ($config->menubar->getCommands() as $command)
	    {
	        $html .= '<li>';
            $html .= $this->command(array('command' => $command));
            $html .= '</li>';
        }

        $html .= '</ul>';

		if (version_compare(JVERSION, '1.7', 'ge')) {
			$html .= '<div class="clr"></div></div></div>';
		}


		return $html;
    }

    /**
     * Render a menubar command
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function command($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'command' => null
        ));

        $command = $config->command;

        //Add a nolink class if the command is disabled
        if($command->disabled) {
            $command->attribs->class->append(array('nolink'));
        }

        if($command->active) {
             $command->attribs->class->append(array('active'));
        }

        //Explode the class array
        $command->attribs->class = implode(" ", KConfig::unbox($command->attribs->class));

        if ($command->disabled) {
			$html = '<span '.KHelperArray::toString($command->attribs).'>'.JText::_($command->label).'</span>';
		} else {
			$html = '<a href="'.$command->href.'" '.KHelperArray::toString($command->attribs).'>'.JText::_($command->label).'</a>';
		}

    	return $html;
    }
}