<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Pages
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Page App Delegate
 *
 * @category   Anahita
 * @package    Com_pages
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPagesDelegate extends ComAppsDomainDelegateDefault
{	
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
		
	/**
	 * Return a set of resources and type of operation on each resource
	 * 
	 * @return array
	 */
	public function getResources()
	{
		return array(
			'page' => array('add', 'edit')
		);
	}
}