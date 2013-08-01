<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Actorbar. 
 *
 * @category   Anahita
 * @package    Com_Photos
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPhotosControllerToolbarActorbar extends ComMediumControllerToolbarActorbar
{
    /**
     * Before controller action
     *
     * @param  KEvent $event Event object 
     * 
     * @return string
     */
    public function onBeforeControllerGet(KEvent $event)
    {        
        parent::onBeforeControllerGet($event);

		$viewer = $this->getController()->viewer;
		$actor	= pick($this->getController()->actor, $viewer);
		$layout = pick($this->getController()->getRequest()->layout, 'default');
		
		$name	= $this->getController()->getIdentifier()->name;
		$filter = $this->getController()->filter;
		//create pathway

		//create title
		if( $layout == 'upload' )
			$this->setTitle(JText::sprintf('COM-PHOTOS-UPLOAD-PHOTOS', $actor->name));
		else if( $name == 'set' )
			$this->setTitle(JText::sprintf('COM-PHOTOS-HEADER-ACTOR-SETS', $actor->name));
		else if( $name == 'photo' && $filter == 'leaders' )
			$this->setTitle(JText::sprintf('COM-PHOTOS-HEADER-ACTOR-LEADERS-PHOTOS', $actor->name));
		else
			$this->setTitle(JText::sprintf('COM-PHOTOS-HEADER-ACTOR-PHOTOS', $actor->name));
		
		//create navigations
		$this->addNavigation( 'photos',
					JText::_('COM-PHOTOS-LINKS-PHOTOS'),
					array('option'=>'com_photos', 'view'=>'photos', 'oid'=>$actor->uniqueAlias),
					$name == 'photo' && (in_array($layout, array('default', 'add', 'masonry'))) && $filter == '');

		if($actor->photos->getTotal() > 0)			
			$this->addNavigation('sets', JText::_('COM-PHOTOS-LINKS-SETS'), 
						array('option'=>'com_photos', 'view'=>'sets','oid'=>$actor->uniqueAlias), 
						$name == 'set' && in_array($layout, array('default', 'add', 'edit')));
		
		if( $viewer->eql($actor) )
		{
				$this->addNavigation('leaders', 
					JText::_('COM-PHOTOS-LINKS-LEADERS'), 
					array('option'=>'com_photos', 'view'=>'photos', 'filter'=>'leaders', 'oid'=>$actor->uniqueAlias),
					$name == 'photo' && in_array($layout, array('default', 'masonry')) && $filter == 'leaders');
		}
    }    
}
