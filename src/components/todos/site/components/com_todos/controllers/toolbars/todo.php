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
 		
 		if ( $entity->authorize('vote') )
			$this->addCommand('vote');	
					
		if ( $entity->authorize('edit') ) 
		{ 
			$this->addCommand('edit', array('layout'=>'form'))
			->getCommand('edit')
			->setAttribute('data-trigger','Request')
			->setAttribute('data-request-options','{replace:\'!.an-entity\'}');	
			
			$this->addCommand('enable', array('ajax'=>true));	
		}	

		if ( $entity->authorize('delete') && $this->getController()->filter != 'leaders' )
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
	 * Add Action for an entity
	 *
	 * @param LibBaseTemplateObject $command Command Object
	 *
	 * @return void
	 */
	protected function _commandNew($command)
	{		 
		 $entity = $command->entity;
		
		$command
		->append(array('label'=>JText::_('COM-TODOS-TOOLBAR-TODO-NEW')))
		->href('option=com_todos&view=todo&layout=add')
		->setAttribute('data-trigger','ReadForm');
	} 
	
	/**
	 * Delete Action for an entity
	 *
	 * @param LibBaseTemplateObject $command Command Object
	 *
	 * @return void
	 */
	protected function _commandEnable($command)
	{	
	    $entity = $this->getController()->getItem();
	    	    
	    $label 	= JText::_('COM-TODOS-ACTION-'.strtoupper($entity->enabled ? 'disable' : 'enable'));
	
	    $url = $entity->getURL().'&action='.($entity->enabled ? 'disable' : 'enable');
	    
	    $command->append(array('label'=>$label));
	           
	    if($command->ajax)
	    {
	        $command
	        ->href($url.'&layout=list')
	        ->setAttribute('data-trigger', 'Request')
	        ->setAttribute('data-request-options',"{method:'post', replace:'!.an-entity'}");
	    }
	    else
	    {
	    	$command
	    	->href($url)
	    	->setAttribute('data-trigger','Submit');
	    }
	}
}