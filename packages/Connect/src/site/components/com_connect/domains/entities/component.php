<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Connect Component
 *
 * @category   Anahita
 * @package    Com_Connect
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComConnectDomainEntityComponent extends ComComponentsDomainEntityComponent
{
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
                'behaviors' => array(
                    'assignable'=>array('assignment_option'   => ComComponentsDomainBehaviorAssignable::OPTION_NOT_OPTIONAL),                       
                )
        ));
    
        parent::_initialize($config);
    }  

    /**
     * @{inheritdoc}
     */
    public function onSettingDisplay(KEvent $event)
    {
        $actor = $event->actor;
        $tabs  = $event->tabs;
        $services = ComConnectHelperApi::getServices();
    
        if ( count($services) ) {
            $tabs->insert('connect',array('label'=> JText::_('COM-CONNECT-PROFILE-EDIT'),'controller'=>'com://site/connect.controller.setting'));
        }
    }
    
    /**
     * Authorizes echo
     * 
     * @param KCommandContext $context
     * 
     * @return false
     */
    public function authorizeEcho(KCommandContext $context)
    {
        $actor = $context->actor;
        
        if ( $actor->isAdministrable() && 
                $actor->authorize('administration') )
        {
            return true;
        }
        else {
            return $actor->id == $context->viewer->id;
        }
    }
    
    /**
     * On Destroy Nodes
     *
     * @param  KEvent $event
     * @return void
     */
    public function onDeleteActor(KEvent $event)
    {
        $this->getService('repos:connect.session')
            ->destroy(array('owner.id'=>$event->actor_id));
    }
}