<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package	   Com_Todos
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Todo Toolbar
 * 
 * @category   Anahita
 * @package	   Com_Todos
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComTodosControllerToolbarTodo extends ComMediumControllerToolbarDefault
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
		{ 
			$this->addCommand('edit', array('layout'=>'form'))
			->getCommand('edit')
			->setAttribute('data-action', 'edit');	
			
			$this->addCommand('enable', array('ajax'=>true));	
		}	

		if($entity->authorize('delete') && $this->getController()->filter != 'leaders')
		    $this->addCommand('delete');		
	}
	
    /**
     * Add Admin Commands for an entity
     *
     *
     * @return void
     */
    public function addAdministrationCommands()
    {
		$this->addCommand('enable');
			
		parent::addAdministrationCommands();
	}
	
	/**
	 * Enable Action for an entity
	 *
	 * @param LibBaseTemplateObject $command Command Object
	 *
	 * @return void
	 */
	protected function _commandEnable($command)
	{	
	    $entity = $this->getController()->getItem();
	    	    
	    $label 	= JText::_('COM-TODOS-ACTION-'.strtoupper($entity->enabled ? 'disable' : 'enable'));
	
        $action = ( $entity->enabled ) ? 'disable' : 'enable';
        
	    $command->append(array('label'=>$label));
	           
	    if($command->ajax)
	    {
	        $command
	        ->href($entity->getURL().'&layout=list')
	        ->setAttribute('data-action', $action);
	    }
	    else
	    {
	    	$command
	    	->href( $entity->getURL().'&action='.$action )
	    	->setAttribute('data-trigger','PostLink');
	    }
	}
	
    /**
     * New button toolbar
     *
     * @param LibBaseTemplateObject $command The action object
     *
     * @return void
     */
    protected function _commandNew($command)
    {
        $command
        ->append(array('label' => JText::_('COM-TODOS-TOOLBAR-TODO-NEW') ))
        ->href('#')
        ->setAttribute('data-trigger', 'ReadForm');
    }
}