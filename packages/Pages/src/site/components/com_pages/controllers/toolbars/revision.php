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
 * Revision Toolbar
 *
 * @category   Anahita
 * @package    Com_Pages
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComPagesControllerToolbarRevision extends ComMediumControllerToolbarDefault
{	
    /**
     * Set the toolbar commands
     * 
     * @return void
     */
    public function addToolbarCommands()
    {
		$entity = $this->getController()->getItem();
		
		$this->addCommand('view');
		
		if ( $entity->owner->authorize('administration') ) 
			$this->addCommand('restore');
	}
	
	/**
	 * View command
	 *
	 * @param LibBaseTemplateObject $command Command Object
	 *
	 * @return void
	 */
	protected function _commandView($command)
	{
	    $entity = $this->getController()->getItem();
	    $command->append(array('label'=>JText::_('COM-PAGES-PAGE-CURRENT-VERSION')));
	    $command->href('option=com_pages&view=page&id='.$entity->parent->id);
	}
	
	/**
	 * Restore command
	 *
	 * @param LibBaseTemplateObject $command Command object
	 *
	 * @return void
	 */
	protected function _commandRestore($command)
	{
	    $entity = $this->getController()->getItem();  
	    $command->append(array('label'=>JText::_('COM-PAGES-PAGE-REVISION-RESTORE')));
	    $command->href('option=com_pages&view=revision&action=restore&id='.$entity->id)
	    ->setAttribute('data-trigger','Submit');	    
	}	
}