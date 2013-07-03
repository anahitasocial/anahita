<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Medium Controller Toolbar
 *
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMediumControllerToolbarDefault extends ComBaseControllerToolbarDefault
{    
    /**
     * Called after controller browse
     *
     * @param KEvent $event
     *
     * @return void
     */
    public function onAfterControllerBrowse(KEvent $event)
    {                
        $filter = $this->getController()->filter;
        
        if ( $this->getController()->canAdd() && $filter != 'leaders' ) 
        {
            $this->addCommand('new');
        }        
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
                
        if(	$entity->authorize('subscribe') || ( $entity->isSubscribable() && $entity->subscribed(get_viewer())))
            $this->addCommand('subscribe');
                
        if ( $entity->authorize('edit') )
            $this->addCommand('edit');
        
        if ( $entity->isOwnable() && $entity->owner->authorize('administration') )
            $this->addAdministrationCommands();       
        
        if ( $entity->authorize('delete') )
            $this->addCommand('delete');        
    }
     
    /**
     * Called before list commands
     * 
     * @return void
     */
    public function addListCommands()
    {
        $entity = $this->getController()->getItem();
        
        if ( $entity->authorize('vote') ) {
            $this->addCommand('vote');
        }
        
        if ( $entity->authorize('delete') ) {
            $this->addCommand('delete');
        }
    } 

    /**
     * Add Admin Commands for an entity
     * 
     * @return void
     */
    public function addAdministrationCommands()
    {
        $entity = $this->getController()->getItem();
        
        if ( $entity->isOwnable() && $entity->owner->authorize('administration') )
        {
            if ( $entity->isEnablable() )
                $this->addCommand('enable');
    
            if ( $entity->isCommentable() )
                $this->addCommand('commentstatus');
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
        $actor  = $this->getController()->actor;
        $name   = $this->getController()->getIdentifier()->name;
        $labels = array();
        $labels[] = strtoupper('com-'.$this->getIdentifier()->package.'-toolbar-'.$name.'-new');
        $labels[] = 'New';
        $label = translate($labels);
        $url   = 'option=com_'.$this->getIdentifier()->package.'&view='.$name.'&oid='.$actor->id.'&layout=add';
        $command->append(array('label'=>$label))
                ->href($url);
    }
}