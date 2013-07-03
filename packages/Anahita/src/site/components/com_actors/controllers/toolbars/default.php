<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Default Actor Controller
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsControllerToolbarDefault extends ComBaseControllerToolbarDefault
{   
    /**
     * Method type flag
     * 
     * @var boolean
     */ 
    protected $_use_post = false;
    
    /**
     * If set to true, the element will be updated
     * 
     * @var boolean
     */ 
    protected $_update = true;
    
	/**
     * Called after controller browse
     *
     * @param KEvent $event
     *
     * @return void
     */
    public function onAfterControllerBrowse(KEvent $event)
    {
        $actor  = $this->getController()->actor;
        $filter = $this->getController()->filter;
        
        if ( $this->getController()->canAdd() ) {
            $this->addCommand('new', array('actor'=>$actor));
        }
    }
    
    /**
     * Called before list commands
     * 
     * @return void
     */
    public function addListCommands()
    {
        //the context actor
        $actor1  = $this->getController()->actor;
        
        //the actor entity that the actions are being evaluated against
        $actor2 = $this->getController()->getItem();
                
        //if actor is not available, then all the against are with respect 
        //to the viewer
        $actor1 = pick($actor1, get_viewer());
       
        //if context actor is administrable (i.e. groups) then actions are
        //administaration actions
        if ( $actor1->isAdministrable() )
        {
            if ( $actor1->authorize('administration') )
            {
                $this->_update = false;
                
                //how actor2 would see the actions
                if ( false && $command  = $this->getFollowCommand($actor2, $actor1) ) 
                {
                    $labels   = array();
                    $labels[] = 'COM-ACTORS-SOCIALGRAPH-'.strtoupper($command->action);
                    $labels[] = 'COM-'.strtoupper($this->getIdentifier()->package).'-SOCIALGRAPH-'.strtoupper($command->action);                    
                    $command->label = translate($labels);
                    $this->addCommand($command);   
                }
                
                if ( $command = $this->getBlockCommand($actor1, $actor2) ) 
                {
                    $labels   = array();
                    $labels[] = 'COM-ACTORS-SOCIALGRAPH-'.strtoupper($command->action);
                    $labels[] = 'COM-'.strtoupper($this->getIdentifier()->package).'-SOCIALGRAPH-'.strtoupper($command->action);                    
                    $command->label = translate($labels);
                    $this->addCommand($command);   
                }
            }
        }
        else
        {
            if ( $command  = $this->getFollowCommand($actor1, $actor2) ) 
            {
                $label    = pick($command->label, $command->name);
                $labels   = array();
                $labels[] = 'COM-ACTORS-SOCIALGRAPH-'.strtoupper($label);
                $labels[] = 'COM-'.strtoupper($this->getIdentifier()->package).'-SOCIALGRAPH-'.strtoupper($label);                    
                $command->label = translate($labels);
                $this->addCommand($command);
            }
            
            if ( $command = $this->getBlockCommand($actor1, $actor2) ) {
                $labels   = array();                                
                $labels[] = 'COM-ACTORS-SOCIALGRAPH-'.strtoupper($command->name);
                $labels[] = 'COM-'.strtoupper($this->getIdentifier()->package).'-SOCIALGRAPH-'.strtoupper($command->name);                    
                $command->label = translate($labels);                
                $this->addCommand($command);   
            }
        }
    } 

    /**
     * Called before toolbar commands
     * 
     * @return void
     */
    public function addToolbarCommands()
    {        
        $actor = $this->getController()->getItem();
        
        $this->_use_post = true;
    
        $this->addListCommands();
    
        if ($actor->authorize('administration'))
        {
            $this->addCommand('edit', array('label'=>JText::_('COM-ACTORS-PROFILE-EDIT'), 'entity'=>$actor))
                  ->getCommand('edit')
                  ->href($actor->getURL(false).'&get=settings');
        }
    }
    
    /**
     * Return commands follow/unfollow if $actor1 (context) can perform follow/unfollow actions on $actor2
     *
     * @param ComActorsDomainEntityActor  $actor1  Actor who's performing the action 
     * @param ComPeopleDomainEntityPerson $actor2  Actor that actions is being performed on
     *
     * @return LibBaseTemplateObject
     */   
    public function getFollowCommand($actor1, $actor2)
    {
        //actor1 can only follow $actor2 if and only if
        //$actor1 is leadable and $actor2 is followable           
        if ( !$actor2->isFollowable() || !$actor1->isLeadable() )
            return null;
                         
        if ( $actor1->eql($actor2) ) {
            return null;   
        }
        
        if ( $actor2->authorize('unfollow', array('viewer'=>$actor1)) )
        {
            $command = $this->getCommand('follow', array('receiver'=>$actor2,'actor'=>$actor1,'action'=>'deletefollower'));
            $command->name = 'unfollow';
            return $command;
        }
        elseif ( !$actor1->following($actor2) && $actor2->authorize('follower', array('viewer'=>$actor1)) )
        {
            $command = $this->getCommand('follow', array('receiver'=>$actor2,'actor'=>$actor1,'action'=>'addfollower'));
            $command->name = 'follow';
            return $command;
        }
        elseif ( !$actor1->following($actor2) ) 
        {
            if ( $actor2->requested($actor1) || $actor2->authorize('requester', array('viewer'=>$actor1)) )
            {
                if ( $actor2->requested($actor1) ) 
                {          
                    $command = $this->getCommand('follow', array('receiver'=>$actor2,'actor'=>$actor1,'action'=>'deleterequester'));
                    $command->name  = 'unfollow';
                    $command->label = 'unrequest';
                } 
                else 
                {
                    $command = $this->getCommand('follow', array('receiver'=>$actor2,'actor'=>$actor1,'action'=>'addrequester'));
                    $command->label = 'request';                    
                }
                
                return $command;                
            }                        
        }
    }
        
    /**
     * Return commands block/unblock if $actor1 (context) can perform unblock/block commands on $actor2
     *
     * @param ComActorsDomainEntityActor  $actor1  Actor who's performing the action 
     * @param ComPeopleDomainEntityPerson $actor2  Actor that actions is being performed on
     *
     * @return LibBaseTemplateObject
     */   
    public function getBlockCommand($actor1, $actor2)
    {
        //actor1 can only block $actor2 if and only if
        //$actor1 is followable and $actor2 is leadable this
        //prevents actor2 from following actor2       
        if ( !$actor1->isFollowable() || !$actor2->isLeadable() ) {
            return null;    
        }
        
        if ( $actor1->eql($actor2) ) {
            return null;   
        }
        
        if ( $actor1->blocking($actor2) ) 
        {
            $command = $this->getCommand('block', array('receiver'=>$actor1,'actor'=>$actor2,'action'=>'deleteblocked'));
            $command->name = 'unblock';
            return $command;
        }        
        elseif ( $actor2->authorize('blocker', array('viewer'=>$actor1)) ) 
        {
            $command = $this->getCommand('block', array('receiver'=>$actor1,'actor'=>$actor2,'action'=>'addblocked'));
            $command->name = 'block';
            return $command;                        
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
        $name   = $this->getController()->getIdentifier()->name;
        $labels = array();
        $labels[] = strtoupper('com-'.$this->getIdentifier()->package.'-toolbar-'.$name.'-new');
        $labels[] = 'New';
        $label = translate($labels);
        $url   = 'option=com_'.$this->getIdentifier()->package.'&view='.$name.'&layout=add';
        $command->append(array('label'=>$label))
                ->href($url);
    }

    /**
     * Follow Command
     *
     * @param LibBaseTemplateObject $command The action object
     *
     * @return void
     */
    protected function _commandFollow($command)
    {
        $url  = $command->receiver->getURL();
        
        $command->data(array('action'=>$command->action,'actor'=>$command->actor->id));
        
        if ( !$this->_use_post && $this->getController()->getRequest()->getFormat() != 'json')
            $url .= '&layout=list';
                
        $command->href($url);
        
        if ( !$this->_update )
            $command->setAttribute('data-trigger','Request')->setAttribute('data-request-options','{method:\'post\',remove:\'!.an-record\'}');
        elseif ( !$this->_use_post )
            $command->setAttribute('data-trigger','Request')->setAttribute('data-request-options','{method:\'post\',replace:\'!.an-record\'}');
        else
            $command->setAttribute('data-trigger','Submit');                                   
    }
    
    /**
     * Block Command
     *
     * @param LibBaseTemplateObject $command The action object
     *
     * @return void
     */
    protected function _commandBlock($command)
    {
        $url  = $command->receiver->getURL();
               
        $command->data(array('action'=>$command->action,'actor'=>$command->actor->id));
                
        $command->href($url);
        
        if ( !$this->_use_post )
            $command->setAttribute('data-trigger','Request')->setAttribute('data-request-options','{method:\'post\',remove:\'!.an-record\'}');
        else
            $command->setAttribute('data-trigger','Submit');                                   
    }
}

?>