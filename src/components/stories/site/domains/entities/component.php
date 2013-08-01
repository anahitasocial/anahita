<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Story component
 *
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesDomainEntityComponent extends ComComponentsDomainEntityComponent
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
			//'assignment_option'   => self::ASSIGNMENT_OPTION_ALWAYS
		));
	
		return parent::_initialize($config);
	}
	
	/**
	 * The stories always show first
	 * 
	 * @return int
	 */
	public function getPriority()
	{
		return  -PHP_INT_MAX;
	}
	
	/**
	 * On Dashboard event
	 *
	 * @param  KEvent $event The event parameter
	 *
	 * @return void
	 */
	public function onProfileDisplay(KEvent $event)
	{
		$actor       = $event->actor;
		$gadgets     = $event->gadgets;
		$composers   = $event->composers;
		$this->_setGadgets($actor, $gadgets, 'profile');	
	}
	 
	/**
	 * On Dashboard event
	 *
	 * @param  KEvent $event The event parameter
	 *
	 * @return void
	 */
	public function onDashboardDisplay(KEvent $event)
	{
		$actor      = $event->actor;
		$gadgets    = $event->gadgets;
		$composers  = $event->composers;
		$this->_setGadgets($actor, $gadgets, 'dashboard');		
	}
			
	/**
	 * @{inheritdoc}
	 */
	protected function _setGadgets($actor, $gadgets, $mode)
	{
		$controller = $this->getService('com://site/stories.controller.story');
		$content    = $controller;
	
		if ( $mode == 'profile' )
		{
			$controller->oid($actor->id)->view('stories');
	
			$gadgets->insert('stories', array(
					'title'      => JText::_('COM-STORIES-GADGET-TITLE-STORIES'),
					'show_title' => get_viewer()->guest(),
					'content'    => $content
			));
		} else
		{
			$controller->view('stories')->filter('leaders');
	
			$gadgets->insert('stories', array(
					'title' 		=> JText::_('COM-STORIES-GADGET-TITLE-STORIES'),
					'show_title'    => get_viewer()->guest(),
					'content'       => $content
			));
		}
	}	
}