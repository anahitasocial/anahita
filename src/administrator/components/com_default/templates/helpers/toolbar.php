<?php
/**
 * @version     $Id: behavior.php 3364 2011-05-25 21:07:41Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Template Toolbar Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateHelperToolbar extends KTemplateHelperAbstract
{
	/**
     * Render the toolbar title
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function title($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'toolbar' => null
        ));

        $html = '<div class="header pagetitle icon-48-'.$config->toolbar->getIcon().'">';

        if (version_compare(JVERSION,'1.6.0','ge')) {
			$html .= '<h2>'.JText::_($config->toolbar->getTitle()).'</h2>';
        } else {
            $html .= JText::_($config->toolbar->getTitle());
        }

		$html .= '</div>';

        return $html;
    }

    /**
     * Render the toolbar
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function render($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'toolbar' => null
        ));

        if (version_compare(JVERSION,'1.6.0','ge')) {
		  $html	= '<div class="toolbar-list" id="toolbar-'.$config->toolbar->getName().'">';
        } else {
          $html = '<div class="toolbar" id="toolbar-'.$config->toolbar->getName().'">';
        }

        $html .= '<table class="toolbar">';
	    $html .= '<tr>';
	    foreach ($config->toolbar->getCommands() as $command)
	    {
            $name = $command->getName();

	        if(method_exists($this, $name)) {
                $html .= $this->$name(array('command' => $command));
            } else {
                $html .= $this->command(array('command' => $command));
            }
       	}
		$html .= '</tr>';
		$html .= '</table>';

		$html .= '</div>';

		return $html;
    }

    /**
     * Render a toolbar command
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function command($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'command' => NULL
        ));

        $command = $config->command;

         //Add a toolbar class
        $command->attribs->class->append(array('toolbar'));

        //Create the id
        $id = 'toolbar-'.$command->id;

		$command->attribs->class = implode(" ", KConfig::unbox($command->attribs->class));

        $html  = '<td class="button" id="'.$id.'">';
        $html .= '	<a '.KHelperArray::toString($command->attribs).'>';
        $html .= '		<span class="'.$command->icon.'" title="'.JText::_($command->title).'"></span>';
       	$html .= JText::_($command->label);
       	$html .= '   </a>';
        $html .= '</td>';

    	return $html;
    }

	/**
     * Render a separator
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function separator($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'command' => NULL
        ));

        $command = $config->command;

       	$html = '<td class="divider"></td>';

    	return $html;
    }

	/**
     * Render a modal button
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function modal($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
        	'command' => NULL
        ));

        $html  = $this->getTemplate()->renderHelper('behavior.modal');
        $html .= $this->command($config);

    	return $html;
    }
}