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
 * Abstract Actor Controller
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class ComActorsControllerAbstract extends ComBaseControllerService
{
    /** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
          
        $this->registerCallback(array('after.delete','after.add'), array($this, 'redirect'));
        
        //set filter state  
        $this->getState()->insert('filter');            
    }
        
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
        parent::_initialize($config);
        
        $config->append(array(
            'behaviors' => to_hash(array(
            	'com://site/search.controller.behavior.searchable',
                'com://site/stories.controller.behavior.publisher',
                'com://site/notifications.controller.behavior.notifier',                                    
                'followable',
                'administrable',
                'ownable',
                'privatable',
                'enablable',
                'subscribable'
            ))
        ));
                
        JFactory::getLanguage()->load('com_actors');
    }
    
    /**
     * Browse Action
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return AnDomainEntitysetDefault
     */
    protected function _actionBrowse(KCommandContext $context)
    {
        $context->append(array(
            'query' => $this->getRepository()->getQuery()
        ));

        $query  = $context->query;        
        
        if($this->q) 
        {
            $query->keyword($this->getService('anahita:filter.term')->sanitize($this->q));
        }  
        
        $key = KInflector::pluralize($this->getIdentifier()->name);
        
        if($this->ids) 
        {
            $ids = KConfig::unbox($this->ids);
            $query->id($ids);
        } 
        else 
        {
            $query->limit( $this->limit, $this->start );
        }
        
        $entities =  $query->toEntitySet();
        
        
        if($this->isOwnable() && $this->actor) 
        {
            $this->_state->append(array(
                'filter' => 'following'
            ));

            if ($this->filter == 'administering' && $this->getRepository()->hasBehavior('administrable'))
            {
                $entities->where('administrators.id', 'IN', array($this->actor->id));
            }
            else if ($this->actor->isFollowable()) 
            {
                $entities->where('followers.id','IN', array($this->actor->id));
            }
        }
        
        $entities->order('created_on', 'desc');
                
        return $this->setList($entities)->getList();
    }
    
    /**
     * Add an actor
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return AnDomainEntityAbstract
     */
    protected function _actionAdd(KCommandContext $context)
    {
        $entity = parent::_actionAdd($context);
        
        if ($entity->isPortraitable() && KRequest::has('files.portrait')) 
        {
            $file = KRequest::get('files.portrait', 'raw'); 
            $entity->setPortrait(array('url'=>$file['tmp_name'], 'mimetype'=>$file['type']));             
        }
        
        return $entity;
    }
    
    /**
     * Edit's an actor data
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return AnDomainEntityAbstract
     */
    protected function _actionEdit(KCommandContext $context)
    {        
        $entity = parent::_actionEdit($context);
                
        if ($entity->isPortraitable() && KRequest::has('files.portrait')) 
        {         
            $file = KRequest::get('files.portrait', 'raw'); 
            if (!empty($file['size'])) 
            {
                $this->getItem()->setPortrait(array('url'=>$file['tmp_name'], 'mimetype'=>$file['type']));
                                
                $story = $this->createStory(array(
                   'name'   => 'avatar_edit',
                   'owner'  => $entity,
                   'target' => $entity
                ));
            } 
            else 
            {
                $this->getItem()->removePortraitImage();
            }
        }
                        
        if($entity->save($context)) 
        {
            dispatch_plugin('profile.onSave', array('actor'=>$entity, 'data'=>$context->data));  
        }
        
        return $entity;
    }    
        
    /**
     * Set the default Actor View
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return ComActorsControllerDefault
     */ 
    public function setView($view)
    {
        parent::setView($view);
        
        if (!$this->_view instanceof ComBaseViewAbstract) 
        {
            $name  = KInflector::isPlural($this->view) ? 'actors' : 'actor';
            $defaults[] = 'ComActorsView'.ucfirst($view).ucfirst($this->_view->name);
            $defaults[] = 'ComActorsView'.ucfirst($name).ucfirst($this->_view->name);
            $defaults[] = 'ComBaseView'.ucfirst($this->_view->name);
            register_default(array('identifier'=>$this->_view, 'default'=>$defaults));
        }
        
        return $this;
    }       
    
    /**
     * Deletes an actor and all of the necessary cleanup. It also dispatches all the apps to 
     * clean up after the deleted actor
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return AnDomainEntityAbstract
     */
    protected function _actionDelete(KCommandContext $context)
    {
        $this->getService('repos://site/components')
        ->fetchSet()
        ->registerEventDispatcher($this->getService('anahita:event.dispatcher'));
                         
        $result  = parent::_actionDelete($context);
        
        $this->getService('anahita:event.dispatcher')
        ->dispatchEvent('onDeleteActor', array('actor_id'=>$this->getItem()));
                
        return $result;
    }
    
    /**
     * Get a toolbar by identifier
     *
     * @return KControllerToolbarAbstract
     */
    public function getToolbar($toolbar, $config = array())
    {
        if(is_string($toolbar))
        {
            //if actorbar or menu alawys default to the base
            if (in_array($toolbar, array('actorbar')))
            {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('controller','toolbar');
                $identifier->name = $toolbar;
                register_default(array('identifier'=>$identifier, 'default'=>'ComActorsControllerToolbar'.ucfirst($toolbar)));
                $toolbar = $identifier;
            }
        }
    
        return parent::getToolbar($toolbar, $config);
    } 
        
    /**
     * Overwrite the setPrivacy action in privatable behavior
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     * 
     * @see   ComActorsDomainBehaviorPrivatable
     */
    protected function _actionSetPrivacy(KCommandContext $context)
    {
        //call the parent privactable behavior
        if($this->hasBehavior('privatable')) 
        {
            $this->getBehavior('privatable')->execute('action.setprivacy', $context);
        }
        
        //now set the follow request
        $data = $context->data;
        
        //if access is not followers
        //then set the allowFollowRequest to false
        if($data->access != 'followers') 
        {
            $data->allowFollowRequest = false;
        }
        
        $this->getItem()->allowFollowRequest = (bool)$data->allowFollowRequest;
    }
    
    /**
     * Set the necessary redirect
     *
     * @param KCommandContext $context
     *
     * @return void
     */
    public function redirect(KCommandContext $context)
    {
        $url = null;
        
        if($context->action == 'delete') 
        {
            //if deleted then go to back to the general page
            $url = 'option=com_'.$this->getIdentifier()->package.'&view='.KInflector::pluralize($this->getIdentifier()->name);
        }
        //after add always go to the setting
        elseif($context->action = 'add') 
        {
            $url = $context->result->getURL().'&get=settings';
        }
        
        if($url) 
        {
            $context->response->setRedirect(JRoute::_($url));
        }
    }
}