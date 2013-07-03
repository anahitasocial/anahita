<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Stories Toolbar
 *
 * @category   Anahita
 * @package    Com_Stories
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComStoriesControllerToolbarStory extends ComBaseControllerToolbarDefault
{ 
    /**
     * Set the list commands
     * 
     * @return void
     */
    public function addListCommands()
    {
        $story = $this->getController()->getItem();
                
        if ( $story->authorize('vote') ) {
            $this->getController()->setItem($story->object);            
            $this->addCommand('vote');            
            $this->getController()->setItem($story);
        }
        
        if ( $story->authorize('add.comment') ) 
        {
            $this->getController()->setItem($story->object);
            
            $this->addCommand('comment')
                 ->getCommand('comment')
                 ->storyid($story->id)
                ;
            
            $this->getController()->setItem($story);
        }
        
        if ( $story->numOfComments > 10 ) {
            $this->addCommand('view');
        }
        
        if( $story->authorize('delete') )
            $this->addCommand('delete');
    }
    
    /**
     * View Stories
     *
     * @param LibBaseTemplateObject $command The command object
     *
     * @return void
     */
    protected function _commandView($command)
    {
        $entity = $this->getController()->getItem();
        $label = sprintf( JText::_('COM-STORIES-VIEW-ALL-COMMENTS'), $entity->getNumOfComments());
        $command->append(array('label'=>$label));
        $command->href(JRoute::_($entity->getURL()));
    }
     
    /**
     * Comment command
     *
     * @param LibBaseTemplateObject $command The command object
     *
     * @return void
     */
    protected function _commandComment($command)
    {
        $entity = $this->getController()->getItem();
        
        $command->append(array('label'=>JText::_('LIB-AN-ACTION-COMMENT')))
            ->href(JRoute::_($entity->getURL()))
            ->class('comment')
            ;
    }
     
    /**
     * Delete Command for a story
     *
     * @param LibBaseTemplateObject $command The command object
     *
     * @return void
     */
    protected function _commandDelete($command)
    {
        $entity = $this->getController()->getItem();
        $link   = 'option=com_stories&view=story';
        foreach($entity->getIds() as $id) {
            $link .= '&id[]='.$id;
        }
        $command->append(array('label'=>JText::_('LIB-AN-ACTION-DELETE')))
        ->href(JRoute::_($link.'&_action=delete'))
        ->setAttribute('data-trigger','Remove');
    }
}