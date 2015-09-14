<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Articles
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
 * @package    Com_Articles
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComArticlesDomainEntityComponent extends ComMediumDomainEntityComponent
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
					'scopeable'=>array('class'=>'ComArticlesDomainEntityArticle'),
					'hashtagable'=>array('class'=>'ComArticlesDomainEntityArticle')
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
	    $permissions['com://site/articles.domain.entity.article'][] = 'edit';
	    unset($permissions['com://site/articles.domain.entity.revision']);
	    return $permissions;
	}
	
	/**
	 * @{inheritdoc}
	 */
	protected function _setGadgets($actor, $gadgets, $mode)
	{
		if ( $mode == 'profile' )
			$gadgets->insert('articles',array(
					'title' 		=> JText::_('COM-ARTICLES-GADGET-ACTOR-ARTICLES'),
					'url'   		=> 'option=com_articles&view=articles&layout=gadget&oid='.$actor->id,
					'action'        => JText::_('LIB-AN-GADGET-VIEW-ALL'),
					'action_url'		=> 'option=com_articles&view=articles&oid='.$actor->id,
			));
		else
			$gadgets->insert('articles',array(
					'title' 	=> JText::_('COM-ARTICLES-GADGET-ACTOR-DASHBOARD'),
					'url'   	=> 'option=com_articles&view=articles&layout=gadget&filter=leaders',
					'action'        => JText::_('LIB-AN-GADGET-VIEW-ALL'),
					'action_url' 	=> 'option=com_articles&view=articles&filter=leaders'
			));
	}
	
	/**
	 * @{inheritdoc}
	 */
	protected function _setComposers($actor, $composers, $mode)
	{
		if($actor->authorize('action','com_articles:article:add'))
			$composers->insert('article-composer',array(
					'title'	       => JText::_('COM-ARTICLES-COMPOSER-ARTICLE'),
					'placeholder'  => JText::_('COM-ARTICLES-ARTICLE-ADD'),
					'url'      => 'option=com_articles&view=article&layout=composer&oid='.$actor->id,
			));
	}

	/**
	 * @{inheritdoc}
	 */
	protected function _setMenuLinks($actor, $menuItems)
	{
		$menuItems->insert('articles-articles', array(
			'title' => JText::_('COM-ARTICLES-MENU-ITEM-ARTICLES'),
			'url' => 'option=com_articles&view=articles&oid='.$actor->uniqueAlias
		));
	}
}