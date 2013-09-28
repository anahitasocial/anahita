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
     * Render the toolbars after the controller GET
     *
     * @param KEvent $event
     *
     * @return void
     */
    public function onAfterControllerGet(KEvent $event)
    {        
        $can_render = $this->getController()->isDispatched() 
                        && $this->getController()->getRequest()->getFormat() == 'html'
                        && $this->getController()->getView() instanceof LibBaseViewTemplate
                        && KRequest::type()   == 'HTTP';

        if ( $can_render ) 
        {
            $ui     = $this->getController()->getView()->getTemplate()->getHelper('ui');
            
            $data = array(
               'menubar'  => $this->getController()->menubar,
               'actorbar' => $this->getController()->actorbar,
               'toolbar'  => $this->getController()->toolbar    
            );
            
            $module = '<module position="toolbar" style="none">'.$ui->header($data).'</module>';
            
            $this->getController()
                    ->getView()
                    ->getTemplate()
                    ->loadString($module)->render();
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
        $voted  = $entity->votedUp(get_viewer());
                
        $btn_1_id   = uniqid('v');
        $btn_2_id   = uniqid('u');

        $action_key     = '_action';
        
        $action_value   = $voted ? 'unvote' : 'vote';
        if ( is($entity, 'ComBaseDomainEntityComment') ) {
            $action_value .= 'comment';
        }
        $label          = $voted ? JText::_('LIB-AN-ACTION-UNVOTE') : JText::_('LIB-AN-ACTION-VOTE');
        $command->setName($action_value);        
        $command->append(array('label' =>$label));
        $class          = 'btn btn-mini';
        if ( $voted ) {
            $class .= ' btn-inverse';
        }
        $command
            ->href(JRoute::_($entity->getURL()."&$action_key=$action_value"))
            ->class('vote-action '.$action_value.' '.$class)
            ->setAttribute('data-trigger','VoteLink')
            ->setAttribute('data-votelink-toggle', $btn_2_id)
            ->setAttribute('data-votelink-object', $entity->id)
            ->id($btn_1_id);
                
        
        //lets add the reverse of the first
        //button if any onle if it's html request 
        if ( $this->getController()->getRequest()->getFormat() != 'html' )
            return;
            
        $action_value   = !$voted ? 'unvote' : 'vote';
        $label          = !$voted ? JText::_('LIB-AN-ACTION-UNVOTE') : JText::_('LIB-AN-ACTION-VOTE');
        
        $class          = 'btn btn-mini';
        if ( !$voted ) {
            $class .= ' btn-inverse';
        }        
        $command = ComBaseControllerToolbarCommand::getInstance($action_value, array('label'=>$label));
        $this->addCommand($command);        
        $command
            ->href(JRoute::_($entity->getURL()."&$action_key=$action_value"))
            ->class('vote-action '.$action_value.' '.$class)
            ->setAttribute('data-trigger','VoteLink')
            ->setAttribute('data-votelink-toggle', $btn_1_id)
            ->setAttribute('data-votelink-object', $entity->id)
            ->setAttribute('data-behavior', 'Hide',',')->setAttribute('data-hide-element','!>')
            ->id($btn_2_id);
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