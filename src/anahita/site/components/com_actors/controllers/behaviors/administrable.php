<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Administrable Behavior
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComActorsControllerBehaviorAdministrable extends KControllerBehaviorAbstract
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
        
        $this->registerCallback(
                array('before.confirmrequester','before.ignorerequester'), 
                array($this, 'fetchRequester')
        );
        
        $this->registerCallback(
                array('before.addadmin','before.removeadmin'), 
                array($this, 'fetchAdmin')
        );
    }
        
	/**
	 * Remove admin
	 * 
	 * @param KCommandContext $context Context parameter
     * 
	 * @return void
	 */
	protected function _actionRemoveadmin(KCommandContext $context)
	{
        $this->getItem()->removeAdministrator($this->admin);          
	}

	/**
	 * Add Admin
	 * 
	 * @param KCommandContext $context Context parameter
     * 
	 * @return void
	 */
	protected function _actionAddadmin(KCommandContext $context)
	{
        $this->getItem()->addAdministrator($this->admin);		    
	}

	/**
	 * Get Candidates
	 * 
	 * @param KCommandContext $context Context parameter
	 * @return void
	 */
	protected function _actionGetcandidates(KCommandContext $context)
	{		
		if ( $context->request->getFormat() != 'html' )
		{
			$data = $context->data;
			$canditates = $this->getItem()->getAdminCanditates();
			$canditates->keyword($this->value)->limit(10);
			$people = array();
		    foreach($canditates as $person) {
				$people[] = array('id'=>$person->id, 'value'=>$person->name);
			}
			$this->getView()->set($people);
			return $people;
		}
	}
		
	/**
	 * Get settings
	 * 
	 * @param KCommandContext $context Context parameter
     * 
	 * @return void
	 */
	protected function _actionGetsettings(KCommandContext $context)
	{
        $entity = $this->getItem();
        			
		$this->getToolbar('actorbar')->setActor($entity);
		
		$this->getToolbar('actorbar')->setTitle(sprintf(JText::_('COM-ACTORS-PROFILE-HEADER-EDIT'), $entity->name));
				
		$dispatcher = $this->getService('anahita:event.dispatcher');
		
		$entity->components->registerEventDispatcher($dispatcher);				
        
        $dispatcher->addEventListener('onSettingDisplay', $this->_mixer);
	}
    
	/**
	 * Add App
	 *
	 * @param KCommandContext $context Context parameter
     * 
     * @return void
	 */
	protected function _actionAddapp(KCommandContext $context)
	{
		$data 	    = $context->data;
		$this->getItem()->components->insert($data->app);
		$this->commit();
	}
	
	/**
	 * Remove App
	 *
	 * @param KCommandContext $context Context parameter
     * 
     * @return void
	 */
	protected function _actionRemoveapp(KCommandContext $context)
	{
		$data 	    = $context->data;		
		$this->getItem()->components->extract($data->app);
		$this->commit();
	} 
    
    /**
     * Confirm a requester
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    protected function _actionConfirmrequester(KCommandContext $context)
    {
        //add the requester as a follower
        //the rest is take care of
        $this->getItem()->addFollower($this->getState()->requester);
    }
    
    /**
     * Ignores a requester
     *
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    protected function _actionIgnorerequester(KCommandContext $context)
    {
        //add the requester as a follower
        //the rest is take care of
        $this->getItem()->removeRequester($this->getState()->requester);
    }
    
    /**
     * Fetches the requester
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    public function fetchRequester(KCommandContext $context)
    {
        $data = $context->data;
        
        if ( $this->getItem() ) {            
            $this->getState()->requester = $this->getItem()->requesters->fetch($data->requester);   
        }
    } 
    
    /**
     * Fetches the requester
     * 
     * @param KCommandContext $context Context parameter
     * 
     * @return void
     */
    public function fetchAdmin(KCommandContext $context)
    {
        $data = $context->data;
        
        if ( $this->getItem() ) 
        {
            $this->getState()->admin = $this->getService('repos://site/people.person')->fetch($data->adminid);   
        } 
    }
}