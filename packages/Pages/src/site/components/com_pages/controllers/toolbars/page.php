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

		if( $entity->authorize('vote') )
			$this->addCommand('vote');
			
		if ( $entity->authorize('administration') )	
			$this->addCommand('enable', array('ajax'=>true));

		if ( $entity->authorize('edit') )	
			$this->addCommand('edit');

		if ( $entity->authorize('delete') )
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
		
		if ( $entity->authorize('vote') )
		    $this->addCommand('vote');
			
		if ( $entity->authorize('edit') ) 			
		    $this->addCommand('edit');
			
		if ( $entity->authorize('administration') )				
		    $this->addCommand('enable');
			
		if ( $entity->owner->authorize('administration') ) 
			$this->addAdministrationCommands();		
			
		if($entity->authorize('subscribe') || $entity->subscribed(get_viewer())) 			
		    $this->addCommand('subscribe');
			
		if ( $entity->authorize('delete') ) 			
		    $this->addCommand('delete');
	}
	
	/**
	 * Enable/Disable
	 * 
	 * @param LibBaseTemplateObject $command Command Object
	 * 
	 * @return void
	 */
	protected function _commandEnable($command)
	{
	    $entity = $this->getController()->getItem();
        
	    $ajax   = pick($command->ajax, false);
	    
		$name   = ($entity->enabled) ? 'disable' : 'enable';
				
		$label 	= JText::_('LIB-AN-ACTION-'.strtoupper($name));
		$url 	= $entity->getURL().'&action='.$name;

		$command->append(array('label'=>$label))
		    ->href($url);
		
		if ( $ajax ) {
			$command->setAttribute('href', 'layout=list&filter=','&');
			$command->setAttribute('data-trigger','Request')->setAttribute('data-request-options',"{method:'post',replace:'!.an-entity'}");
		} else {
			$command->setAttribute('data-trigger','Submit');
		}
	}
}