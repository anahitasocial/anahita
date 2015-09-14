<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Articles
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
 * @package    Com_Articles
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComArticlesControllerToolbarActorbar extends ComMediumControllerToolbarActorbar
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
            
		$viewer = get_viewer();
		$actor	= pick($this->getController()->actor, $viewer);
		$layout = pick($this->getController()->layout, 'default');
		$name	= $this->getController()->getIdentifier()->name;

		$this->setTitle(JText::sprintf('COM-ARTICLES-ACTOR-HEADER-'.strtoupper($name).'S', $actor->name));
	
		//create navigations
		$this->addNavigation('articles',
								JText::_('COM-ARTICLES-LINK-ARTICLES'),
								array('option'=>'com_articles', 'view'=>'articles', 'oid'=>$actor->id),
								$name == 'article');
    }    
}