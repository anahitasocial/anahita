<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_INVITES
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
 * @package    Com_invites
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */

class ComInvitesControllerToolbarActorbar extends ComBaseControllerToolbarActorbar
{
    /**
     * Before _actionGet controller event
     *
     * @param  KEvent $event Event object 
     * 
     * @return string
     */
    public function onBeforeControllerGet(KEvent $event)
    {            
        $this->setActor(get_viewer());
        
    	parent::onBeforeControllerGet($event);
    
        $data 	= $event->data;
		$viewer = get_viewer();
		$actor	= $viewer;
		$layout = pick($this->getController()->layout, 'default');
		$name	= $this->getController()->getIdentifier()->name;
		if ( $name == 'connection' ) {
		    $name = $this->getController()->service; 
		}    
		    
		$this->setTitle(JText::sprintf('COM-INVITES-ACTOR-HEADER-'.strtoupper($name).'S', $actor->name));
			
		//create navigations
		$this->addNavigation('email',
								JText::_('COM-INVITES-LINK-EMAIL'),
								'option=com_invites&view=email',
								$name == 'email');

		if ( ComConnectHelperApi::enabled('facebook') )
            $this->addNavigation('facebook', 
								JText::_('COM-INVITES-LINK-FACEBOOK'), 
								'option=com_invites&view=connections&service=facebook', 
								$name == 'facebook');
    }
}