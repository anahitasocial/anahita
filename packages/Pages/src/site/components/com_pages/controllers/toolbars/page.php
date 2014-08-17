<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Pages
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Page Toolbar
 *
 * @category   Anahita
 * @package    Com_Pages
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPagesControllerToolbarPage extends ComMediumControllerToolbarDefault
{			
    /**
     * Called before list commands
     * 
     * @return void
     */
    public function addListCommands()
    {
		$entity = $this->getController()->getItem();

		if($entity->authorize('vote'))
			$this->addCommand('vote');

		if($entity->authorize('edit'))	
			$this->addCommand('edit');

		if($entity->authorize('delete'))
			$this->addCommand('delete');
	}
	
    /**
     * Set the toolbar commands
     * 
     * @return void
     */
    public function addToolbarCommands()
    {	
		$entity = $this->getController()->getItem();
		
		if($entity->authorize('vote'))
		    $this->addCommand('vote');
			
		if($entity->authorize('edit')) 			
		    $this->addCommand('edit');
			
		if($entity->owner->authorize('administration')) 
			$this->addAdministrationCommands();		
			
		if($entity->authorize('subscribe') || $entity->subscribed(get_viewer())) 			
		    $this->addCommand('subscribe');
			
		if($entity->authorize('delete')) 			
		    $this->addCommand('delete');
	}
}