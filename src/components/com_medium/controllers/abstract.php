<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Abstract Medium Controller
 *
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class ComMediumControllerAbstract extends ComBaseControllerService
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
		
		$this->registerCallback(array('after.add'), array($this, 'createStory'));
	
        //add the anahita:event.command        
        $this->getCommandChain()
            ->enqueue( $this->getService('anahita:command.event'), KCommand::PRIORITY_LOWEST);
            
        //add medium related states
        $this->getState()
                ->insert('filter')->insert('grid')->insert('order');
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
	    $config->append(array(
            'state' => array(
                'viewer' => get_viewer(),
             ),
             'request'  => array(
                'filter'    => null,
                'order'     => null
            ),
	        'behaviors' => array(
                'composable',
                'publisher',
                'commentable',                
                'votable',
                'privatable',
                'subscribable'                                
        )));
        
        //anything within the medium app
        //is within the context of an owner
        //we need to set the default owner to the viewer        
        $config->append(array(
            'behaviors' => array(
                'ownable' => array('default'=>get_viewer()),  
        )));
        
	    parent::_initialize($config);
	}
		
	/** 
	 * Browse Action
	 * 
	 * @param KCommandContext $context Context Parameter
	 * 					
	 * @return AnDomainQuery
	 */
	protected function _actionBrowse($context)
	{		                  		
		$entities = parent::_actionBrowse($context);		

        if( $this->filter == 'leaders' )
        {
           $leaderIds = array();
           $leaderIds[] = $this->viewer->id;
           $leaderIds = array_merge($leaderIds, $this->viewer->getLeaderIds()->toArray());
           $entities->where( 'owner.id','IN', $leaderIds );
        }
		elseif( $this->getRepository()->hasBehavior('ownable')  )
			$entities->where('owner', '=', $this->actor);
	    
        return $entities;
	}
		
	/** 
	 * Delete Action
	 * 
	 * @param KCommandContext $context Context Parameter
	 * 
	 * @return void
	 */
	protected function _actionDelete(KCommandContext $context)
	{		
		$redirect_url = array('view'=>KInflector::pluralize($this->getIdentifier()->name), 'oid'=>$this->getItem()->owner->id);		
		parent::_actionDelete($context);
		$this->setRedirect($redirect_url);
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
	
	    if ( !$this->_view instanceof ComBaseViewAbstract )
	    {
	        $name  = KInflector::isPlural($this->view) ? 'media' : 'medium';
	        $defaults[] = 'ComMediumView'.ucfirst($view).ucfirst($this->_view->name);
            $defaults[] = 'ComMediumView'.ucfirst($name).ucfirst($this->_view->name);
            $defaults[] = 'ComBaseView'.ucfirst($this->_view->name);
            
	        register_default(array('identifier'=>$this->_view, 'default'=>$defaults)); 
	    }
	
	    return $this;
	}	
}