<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
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
                
        if ( $story->authorize('vote') )
        {
            $entity = $story->hasObject() ? $story->object : $story;             
        
            if ( !is_array($entity) )
                $this->addCommand('vote');
        }
        
        $commentable = $story->authorize('add.comment');
        
        if ( $commentable !== false ) {
            if ( $story->hasObject() && is_array($story->object) )
                $commentable = false;
        }
        
        if( $commentable ) {
            $this->addCommand('comment');
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
        $command->href($entity->getURL());
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
            ->href($entity->getURL())
            ->class('comment')
            ->storyid($entity->id);     
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
    
        $command->append(array('label'=>JText::_('LIB-AN-ACTION-DELETE')))
        ->href($entity->getStoryURL(true).'&action=delete')
        ->setAttribute('data-trigger','Remove');
    }
        
}