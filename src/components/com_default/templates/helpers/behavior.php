<?php
/**
 * @version     $Id: behavior.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */


/**
 * Date Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateHelperBehavior extends KTemplateHelperBehavior
{
	/**
	 * Method to load the mootools framework into the document head
	 *
	 * @return string   The html output
	 */
	public function mootools($config = array())
	{
		$config = new KConfig($config);
		$html ='';

		// Only load once
		if (!isset(self::$_loaded['mootools']))
		{
			JHTML::_('behavior.mootools', false);
			self::$_loaded['mootools'] = true;
		}

		return $html;
	}

    /**
     * Keep session alive
     *
     * This will send an ascynchronous request to the server via AJAX on an interval
     *
     * @return string   The html output
     */
    public function keepalive($config = array())
    {
        //Get the config session lifetime
        $lifetime = JFactory::getSession()->getExpire() * 1000;

        //Refresh time is 1 minute less than the liftime
        $refresh =  ($lifetime <= 60000) ? 30000 : $lifetime - 60000;

        $config = new KConfig($config);
        $config->append(array(
            'refresh' => $refresh
        ));

        return parent::keepalive($config);
    }


   	/**
	 * Render a modal box
	 *
	 * @return string	The html output
	 */
	public function modal($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'selector' => 'a.modal',
			'options'  => array('disableFx' => true)
 		));
		
		$html = '';
		
		$signature = 'modal-'.$config->selector;
		if (!isset(self::$_loaded[$config->selector])) 
		{
		    $html = JHTML::_('behavior.modal', $config->selector, $config->toArray());
		    self::$_loaded[$signature] = true;
		}
		
		return $html;
	}
}