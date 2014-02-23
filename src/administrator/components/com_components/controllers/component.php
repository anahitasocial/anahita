<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Components
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * List of assignable components
 *
 * @category   Anahita
 * @package    Com_Components
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComComponentsControllerComponent extends ComBaseControllerService
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
				
		$this->_action_map['edit'] = 'order';
		
		$this->getToolbar('component')->setTitle('Assignable Components');
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
             'behaviors' => array('serviceable'=>array('read_only'=>true))     
        ));
        parent::_initialize($config);
    }

    /**
     * Only bring assignable components
     * 
     * (non-PHPdoc)
     * @see ComBaseControllerService::_actionBrowse()
     */
    protected function _actionBrowse($context)
    {
        $components = parent::_actionBrowse($context);
        $components->assignable(true);
        return $components;
    }
    
    /**
     * orders
     */    
    protected function _actionOrder(KCommandContext $context)
    {    	
    	$components = $this->getRepository()->fetchSet(array('id'=>KConfig::unbox($this->id)));
    	$components->setData(KConfig::unbox($context->data));    	
    	$components->save();    	
    }
    
    /**
     * Sets the assignment
     */
    protected function _actionPost(KCommandContext $context)
    {
    	$item = $this->getService('com://admin/components.domain.set.assignablecomponent')
    		->find(array('id'=>$this->id));
    	if ( $item ) {
    		$item->setAssignmentForIdentifier( KConfig::unbox($context->data->identifiers) );
    		$item->save();	
    	}
    }
    
    /**
     * Show an assignable component
     */
    protected function _actionRead(KCommandContext $context)
    {
        $component = $this->getService('repos://site/components')->find($this->_request->get('id'));        
    	$this->setItem($component);    	
    	$this->actor_identifiers = $this->getService('com://admin/components.domain.set.actoridentifier');
    }
    
    /**
     * Can't delete the component
     * 
     * @return boolean
     */
    public function canDelete()
    {
        return false;    
    }
    
    /**
     * Can't add a new component
     * 
     * @return boolean
     */
    public function canAdd()
    {
        return false;
    }
}