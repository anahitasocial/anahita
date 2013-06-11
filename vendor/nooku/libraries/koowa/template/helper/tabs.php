<?php
/**
 * @version     $Id: behavior.php 1051 2009-07-13 22:08:57Z Johan $
 * @package     Koowa_Template
 * @subpackage  Helper
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Template Tabs Behavior Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Template
 * @subpackage  Helper
 * @uses        KArrayHelper
 */
class KTemplateHelperTabs extends KTemplateHelperBehavior
{
    /**
     * Creates a pane and creates the javascript object for it
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function startPane( $config = array() )
    {
        $config = new KConfig($config);
        $config->append(array(
            'id'      => 'pane',
            'attribs' => array(),
            'options' => array()
        ));

        $html  = '';

        // Load the necessary files if they haven't yet been loaded
        if (!isset($this->_loaded['tabs']))
        {
            $html .= '<script src="media://lib_koowa/js/tabs.js" />';
            $this->_loaded['tabs'] = true;
        }

        $id      = strtolower($config->id);
        $attribs = KHelperArray::toString($config->attribs);
        //Don't pass an empty array as options
        $options = $config->options->toArray() ? ', '.$config->options : '';

        $html .= "
            <script>
                window.addEvent('domready', function(){ new Koowa.Tabs('tabs-".$id."'".$options."); });
            </script>";

        $html .= '<dl class="tabs" id="tabs-'.$id.'" '.$attribs.'>';
        return $html;
    }

    /**
     * Ends the pane
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function endPane($config = array())
    {
        return '</dl>';
    }

    /**
     * Creates a tab panel with title and starts that panel
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function startPanel( $config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'title'     => '',
            'attribs'   => array(),
            'options'   => array(),
            'translate' => true
        ));

        $title   = $config->translate ? JText::_($config->title) : $config->title;
        $attribs = KHelperArray::toString($config->attribs);

        return '<dt '.$attribs.'><span>'.$title.'</span></dt><dd>';
    }

    /**
     * Ends a tab page
     *
     * @param   array   An optional array with configuration options
     * @return  string  Html
     */
    public function endPanel($config = array())
    {
        return '</dd>';
    }
}