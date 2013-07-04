<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Topics
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Component object
 *
 * @category   Anahita
 * @package    Com_Topics
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComTopicsDomainEntityComponent extends ComMediumDomainEntityComponent
{
	/**
	 * @{inheritdoc}
	 */
	protected function _setGadgets($actor, $gadgets, $mode)
	{
		if ( $mode == 'profile' )
			$gadgets->insert('topics-gadget',array(
					'title' 	=> JText::_('COM-TOPICS-GADGET-ACTOR-PROFILE'),
					'url'   	=> 'option=com_topics&view=topics&layout=gadget&oid='.$actor->id,
					'action'        => JText::_('LIB-AN-GADGET-VIEW-ALL'),
					'action_url'	=> 'option=com_topics&view=topics&oid='.$actor->id
			));
		else
			$gadgets->insert('topics-gadget',array(
					'title' 		=> JText::_('COM-TOPICS-GADGET-ACTOR-DASHBOARD'),
					'url'   		=> 'option=com_topics&view=topics&layout=gadget&filter=leaders',
					'action'        => JText::_('LIB-AN-GADGET-VIEW-ALL'),
					'action_url'	=> 'option=com_topics&view=topics&filter=leaders'
			));
	}
	
	/**
	 * @{inheritdoc}
	 */
	protected function _setComposers($actor, $composers, $mode)
	{
		if ( $actor->authorize('action','com_topics:topic:add') )
			$composers->insert('photos-composer',array(
					'title'    => JText::_('COM-TOPICS-COMPOSER-TOPIC'),
					'placeholder'  => JText::_('COM-TOPICS-TOPIC-ADD'),
					'url'      => 'option=com_topics&view=topic&layout=composer&oid='.$actor->id
			));
	}	
}