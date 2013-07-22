<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Controller Toolbar
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Controller_Toolbar
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseControllerToolbarDefault extends ComBaseControllerToolbarAbstract
{     
    /**
     * Before Controller _actionRead is executed
     *
     * @param KEvent $event
     *
     * @return void
     */
    public function onBeforeControllerGet(KEvent $event)
    {
        $this->getController()->toolbar = $this;
    }
    
    /**
     * Render the toolbars after the controller GET
     *
     * @param KEvent $event
     *
     * @return void
     */
    public function onAfterControllerGet(KEvent $event)
    {
        $event->result;
        
        $can_render = is_string($event->result)  && 
                      $this->getController()->isDispatched() &&
                      KRequest::type()   == 'HTTP' &&
                      KRequest::format() == 'html';
        
        if ( $can_render ) 
        {
            $ui     = $this->getController()->getView()->getTemplate()->getHelper('ui');
            
            $data = array(
               'menubar'  => $this->getController()->menubar,
               'actorbar' => $this->getController()->actorbar,
               'toolbar'  => $this->getController()->toolbar    
            );
            
            $filter = $this->getController()->getView()->getTemplate()->getFilter('module');
            
            if ( $filter ) {          
                $module = '<module position="toolbar" style="none">'.$ui->header($data).'</module>';
                $filter->write($module);
            }
        }
    }    
           
    /**
     * Edit Command for an entity
     *
     * @param LibBaseTemplateObject $command The action object
     *
     * @return void
     */
    protected function _commandEdit($command)
    {
        $entity = $this->getController()->getItem();
        $layout = pick($command->layout, 'edit');
    
        $command->append(array('label'=>JText::_('LIB-AN-ACTION-EDIT')))
        ->href($entity->getURL().'&layout='.$layout)
        ;
    }
    
    /**
     * Delete Command for an entity
     *
     * @param LibBaseTemplateObject $command The action object
     *
     * @return void
     */
    protected function _commandDelete($command)
    {
        $entity = $this->getController()->getItem();
    
        $command->append(array('label'=>JText::_('LIB-AN-ACTION-DELETE')))
        ->href($entity->getURL().'&action=delete')
        ->setAttribute('data-trigger','Remove');
    }
    
    /**
     * Vote Command for an entity
     *
     * @param LibBaseTemplateObject $command The action object
     *
     * @return void
     */
    protected function _commandVote($command)
    {
        $entity = $this->getController()->getItem();
    
        $command->append(array('label'=>JText::_('LIB-AN-ACTION-VOTE')));
    
        $voted  = $entity->votedUp(get_viewer());
        $vote_action = $command;
        $vote_id 	 = uniqid();
        $unvote_id	 = uniqid();
        $command_name = 'action';
    
        if ( is($entity, 'ComBaseDomainEntityComment') ) {
            $command_name = 'comment[action]';
        }
    
        $vote_action
        ->href($entity->getURL()."&$command_name=vote")
        ->class('vote-action vote btn btn-mini')
        ->setAttribute('data-trigger','VoteLink')
        ->setAttribute('data-votelink-toggle', $unvote_id)
        ->setAttribute('data-votelink-object', $entity->id)
        ->id($vote_id);
        
        $unvote_action = $this->addCommand('unvote', JText::_('LIB-AN-ACTION-UNVOTE'))
        ->getCommand('unvote')
        ->href($entity->getURL()."&$command_name=unvote")
        ->class('vote-action unvote btn btn-mini btn-inverse')
        ->setAttribute('data-trigger','VoteLink')
        ->setAttribute('data-votelink-toggle', $vote_id)
        ->setAttribute('data-votelink-object', $entity->id)
        ->id($unvote_id)
        ;
    
        if ( $voted )
            $vote_action->setAttribute('data-behavior', 'Hide',',')->setAttribute('data-hide-element','!>');
        else
            $unvote_action->setAttribute('data-behavior', 'Hide',',')->setAttribute('data-hide-element','!>');
    }
    
    /**
     * Subscribe Action
     *
     * @param LibBaseTemplateObject $command The action object
     *
     * @return void
     */
    protected function _commandSubscribe($command)
    {
        $entity = $this->getController()->getItem();
    
    
        $label 	= JText::_('LIB-AN-ACTION-'.strtoupper($entity->subscribed(get_viewer()) ? 'unsubscribe' : 'subscribe'));
    
        $command
        ->append(array('label'=>$label))
        ->href($entity->getURL().'&action='.($entity->subscribed(get_viewer()) ? 'unsubscribe' : 'subscribe'))
        ->setAttribute('data-trigger','Submit');
        ;
    }
    
    
    /**
     * Close/Open comment Action
     *
     * @param LibBaseTemplateObject $command The action object
     *
     * @return void
     */
    protected function _commandCommentstatus($command)
    {
        $entity = $this->getController()->getItem();
    
        $label  = $entity->openToComment ? JTEXT::_('LIB-AN-ACTION-CLOSE-COMMENTING') : JTEXT::_('LIB-AN-ACTION-OPEN-COMMENTING');
    
        $command->append(array('label'=>$label))
        ->href($entity->getURL().'&action=commentstatus&status='.(!$entity->openToComment))
        ->setAttribute('data-trigger','Submit');
    }
    
    /**
     * Enable/Disable action
     *
     * @param LibBaseTemplateObject $command The action object
     *
     * @return void
     */
    protected function _commandEnable($command)
    {
        $entity = $this->getController()->getItem();
    
        $label 	= JText::_('LIB-AN-ACTION-'.strtoupper($entity->enabled ? 'disable' : 'enable'));
    
        $command->append(array('label'=>$label))
        ->href($entity->getURL().'&action='.($entity->enabled ? 'disable' : 'enable'))
        ->setAttribute('data-trigger','Submit');
    }
}