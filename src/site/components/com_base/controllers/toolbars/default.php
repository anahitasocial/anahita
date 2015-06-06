<?php

/** 
 * LICENSE: ##LICENSE##
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
     * Edit Command for an entity
     *
     * @param LibBaseTemplateObject $command The action object
     *
     * @return void
     */
    protected function _commandEdit($command)
    {
        $entity = $this->getController()->getItem();
        $view = $this->getController()->getView()->getName();
 
        $layout = pick($command->layout, 'edit');
    
        $command->append(array('label'=>JText::_('LIB-AN-ACTION-EDIT')))
        ->href($entity->getURL().'&layout='.$layout);
        
        if(KInflector::isPlural($view))
        {
            $command->setAttribute('data-action', 'edit');
        }
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
        ->href(JRoute::_($entity->getURL()))
        ->setAttribute('data-action', 'delete')
        ->setAttribute('data-redirect', JRoute::_('index.php?'))
        ->class('action-delete');
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
        $voted = $entity->votedUp(get_viewer());
         
        $action = $voted ? 'unvote' : 'vote';
        $class = 'action-'.$action;
         
        if(is($entity, 'ComBaseDomainEntityComment'))
        {
           $action .= 'comment';
           $class .= 'comment';
        }
        
        $label = $voted ? JText::_('LIB-AN-ACTION-UNVOTE') : JText::_('LIB-AN-ACTION-VOTE');
        
        $command
        ->setName($action)
        ->append(array('label' =>$label))
        ->href(JRoute::_($entity->getURL()))
        ->class($class)
        ->setAttribute('data-action', $action)
        ->setAttribute('data-nodeid', $entity->id);
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
    
        $action = ($entity->subscribed(get_viewer()) ? 'unsubscribe' : 'subscribe');
        $label 	= JText::_('LIB-AN-ACTION-'.strtoupper($action));
        
        $command->append(array('label'=>$label))
            ->href($entity->getURL())
            ->class('action-'.$action)
            ->setAttribute('data-action',$action);
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
        $status = $entity->openToComment ? 0 : 1;
    
        $command->append(array('label'=>$label))
        ->href($entity->getURL())
        ->class('action-commentstatus')
        ->setAttribute('data-action','commentstatus')
        ->setAttribute('data-status', $status);
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