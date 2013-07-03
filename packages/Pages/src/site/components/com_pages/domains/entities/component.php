<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Pages
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
 * @package    Com_Pages
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPagesDomainEntityComponent extends ComMediumDomainEntityComponent
{
	/**
	 * Initializes the default configuration for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param KConfig $config An optional KConfig object with configuration options.
	 *
	 * @return void
	 */
	protected function _initialize(KConfig $config)
	{
		$config->append(array(
				'behaviors' => array(
						'searchable'=>array('class'=>'ComPagesDomainEntityPage')
				)
		));
	
		parent::_initialize($config);
	}

	/**
	 * Return an array of permission object
	 *
	 * @return array
	 */
	public function getPermissions()
	{
	    $permissions = parent::getPermissions();
	    $permissions['com://site/pages.domain.entity.page'][] = 'edit';
	    unset($permissions['com://site/pages.domain.entity.revision']);
	    return $permissions;
	}
	
	/**
	 * @{inheritdoc}
	 */
	protected function _setGadgets($actor, $gadgets, $mode)
	{
		if ( $mode == 'profile' )
			$gadgets->insert('pages',array(
					'title' 		=> JText::_('COM-PAGES-GADGET-ACTOR-PAGES'),
					'url'   		=> 'option=com_pages&view=pages&layout=gadget&oid='.$actor->id,
					'action'        => JText::_('LIB-AN-GADGET-VIEW-ALL'),
					'action_url'		=> 'option=com_pages&view=pages&oid='.$actor->id,
			));
		else
			$gadgets->insert('pages',array(
					'title' 	=> JText::_('COM-PAGES-GADGET-ACTOR-DASHBOARD'),
					'url'   	=> 'option=com_pages&view=pages&layout=gadget&filter=leaders',
					'action'        => JText::_('LIB-AN-GADGET-VIEW-ALL'),
					'action_url' 	=> 'option=com_pages&view=pages&filter=leaders'
			));
	}
	
	/**
	 * @{inheritdoc}
	 */
	protected function _setComposers($actor, $composers, $mode)
	{
		if ( $actor->authorize('action','com_pages:page:add') )
			$composers->insert('page-composer',array(
					'title'	       => JText::_('COM-PAGES-COMPOSER-PAGE'),
					'placeholder'  => JText::_('COM-PAGES-PAGE-ADD'),
					'url'      => 'option=com_pages&view=page&layout=composer&oid='.$actor->id,
			));
	}	
}