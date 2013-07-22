<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Apps
 * @subpackage Domain_Delegate
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * App Delegate. This class allows for the components to change the behavior
 * of an app entity
 *
 * @category   Anahita
 * @package    Com_Apps
 * @subpackage Domain_Delegate
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComAppsDomainDelegateDefault extends KEventSubscriberAbstract
{
    /**
     * Access Options for an app
     */    
    const ASSIGNMENT_OPTION_DEFAULT      = 1;
    const ASSIGNMENT_OPTION_NOT_OPTIONAL = 2;
    const ASSIGNMENT_OPTION_ALWAYS       = 3;
    const ASSIGNMENT_OPTION_NEVER        = 4;
    
    /**
     * Assignments
     */
    const ASSIGNMENT_OPTIONAL     = 0;  
    const ASSIGNMENT_ALWAYS       = 1;
    const ASSIGNMENT_NEVER        = 2;
        
    /**
     * App Description
     * 
     * @var string
     */
    protected $_description;
    
	/**
	 * Name of the app
	 * 
	 * @return string
	 */
	protected $_name;
	
	/**
	 * Component
	 * 
	 * @return string
	 */
	protected $_component;	
	
	/**
	 * App Features
	 * 
	 * @var array
	 */
	protected $_features = array();
	
	/**
	 * Apps
	 * 
	 * @var ComAppsDomainEntityApp
	 */
	protected $_app;
	
    /**
     * Resources managed by this app
     * 
     * @var array
     */
    protected $_resources;
    
    /**
     * Access Options
     * 
     * @var array
     */
    protected $_assignment_option;
    
    /**
     * An array of default assignments for
     * when the app is installed for the first time
     * 
     * @var array
     */
    protected $_default_assingments;
    
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
	    $config->auto_connect = false;
	    
		parent::__construct($config);
		
		$this->_name        = $config->name;
		$this->_component   = $config->component;
		$this->_app         = $config->app;
		$this->_description = $config->description;
		$this->_features    = KConfig::unbox($config->features);
        $this->_resources   = $config->resources;
        $this->_assignment_option   = $config->assignment_option;
        $this->_default_assingments = $config['default_assignments'];
	}
			
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{
	    //load the application langauge	    
        JFactory::getLanguage()->load($config->component);	    
	    
		$config->append(array(
            'default_assignments' => array(),
		    'features'       => array('gadget', 'composer'),
			'name'	         => ucfirst(str_replace('com_','', $config->component)),
		    'priority'       => $config->app->ordering,
		    'description'    => translate(array(strtoupper(str_replace('_','-',$config->component)).'-APP-DESCRIPTION'), false)
		))
        //if there are no profile features then it can not be customized 
        //by the user then set the access option to not optional        
        ->append(array(
            'assignment_option' => is_array($config['features']) && count($config['features']) > 0 ? self::ASSIGNMENT_OPTION_DEFAULT : self::ASSIGNMENT_OPTION_NOT_OPTIONAL
        ));

		parent::_initialize($config);
	}
	
	/**
	 * Return the name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * On Setting display
	 *
	 * @param  KEvent $event The event parameter
	 *
	 * @return void
	 */
	public function onSettingDisplay(KEvent $event)
	{
	    $actor  = $event->actor;
	    $tabs   = $event->tabs;
	    $assignment  = $this->_app->getAssignment($actor);
	    if ( $assignment == ComAppsDomainEntityApp::ACCESS_GLOBAL || $this->_app->enabled($actor) )
	    {
	        $this->_setSettingTabs($actor, $tabs);
	    }	    
	}	
	
	/**
	 * On Dashboard event
	 *
	 * @param  KEvent $event The event parameter
	 *
	 * @return void
	 */
	public function onProfileDisplay(KEvent $event)
	{
        $actor       = $event->actor;
        $gadgets     = $event->gadgets;
        $composers   = $event->composers;        
        $assignment  = $this->_app->getAssignment($actor);               
        if ( $assignment == ComAppsDomainEntityApp::ACCESS_GLOBAL || $this->_app->enabled($actor) ) 
        {         
            $this->_setGadgets($actor, $gadgets, 'profile');
            $this->_setComposers($actor, $composers, 'profile');
        }
	}
	
	/**
	 * On Dashboard event
	 *
	 * @param  KEvent $event The event parameter
	 * 
	 * @return void
	 */
	public function onDashboardDisplay(KEvent $event)
	{
	    $actor      = $event->actor;
	    $gadgets    = $event->gadgets;
	    $composers  = $event->composers;
	    $this->_setGadgets($actor, $gadgets, 'dashboard');
        $assignment  = $this->_app->getAssignment($actor);
        if ( $assignment == ComAppsDomainEntityApp::ACCESS_GLOBAL || $this->_app->enabled($actor) )
	        $this->_setComposers($actor, $composers, 'dashboard');	    
	}
	
	/**
	 * Set the composers for a profile/dashboard. This method should be implemented by the subclasses
	 *
	 * @param ComActorsDomainEntityActor     $actor     The actor that gadgets is rendering for
	 * @param LibBaseTemplateObjectContainer $composers Gadet objects
	 * @param string                         $mode      The mode. Can be profile or dashboard
	 *
	 * @return void
	 */
	protected function _setGadgets($actor, $gadgets, $mode)
	{
	    
	}
	
	/**
	 * Set the gadgets for a profile/dashboard. This method should be implemented by the subclasses
	 *
	 * @param ComActorsDomainEntityActor     $actor     The actor that gadgets is rendering for
	 * @param LibBaseTemplateObjectContainer $composers Gadet objects
	 * @param string                         $mode      The mode. Can be profile or dashboard
	 *
	 * @return void
	 */
	protected function _setComposers($actor, $composers, $mode)
	{
	
	}

	/**
	 * Set the gadgets for a profile/dashboard. This method should be implemented by the subclasses
	 *
	 * @param ComActorsDomainEntityActor     $actor The actor that gadgets is rendering for
	 * @param LibBaseTemplateObjectContainer $tabs  Tabs objects
	 *
	 * @return void
	 */
	protected function _setSettingTabs($actor, $tabs)
	{
	    
	}	
		
	/**
	 * Return a set of resources and type of operation on each resource
	 * 
	 * @return array
	 */
	public function getResources()
	{		
        if ( !$this->_resources)
        {
            $path      = JPATH_ROOT.DS.'components'.DS.'com_'.$this->getIdentifier()->package.DS.'domains'.DS.'entities';
            $resources = new KConfig();
            if ( file_exists($path) ) 
            {
                $files = JFolder::files($path);
                foreach($files as $file) {
                    $name       = explode('.', basename($file));
                    $name       = $name[0];
                    $identifier = clone $this->getIdentifier();
                    $identifier->path = array('domain','entity');
                    $identifier->name = $name;
                    try {
                        $repos = AnDomain::getRepository($identifier);
                        if ( $repos->entityInherits('ComMediumDomainEntityMedium') ) 
                        {
                            $actions = array('add');
                            //if commentable then allow to set 
                            //comment permissions
                            if ( $repos->hasBehavior('commentable') ) {
                                $actions[] = 'addcomment';
                            }
                            $resources->append(array(
                               $identifier->name => $actions
                            ));
                        }
                    } 
                    catch(Exception $e) {
                   //     print $e->getMessage().'<br />';
                    }
                }
            }
            $this->_resources = $resources;
        }		
	
		return $this->_resources;
	}
	
	/**
	 * Return the app description
	 *
	 * @return string
	 */
	public function getDescription()
	{
	    return $this->_description;
	}
	
	/**
	 * Return an application profile features
	 *
	 * @return array
	 */
	public function getFeatures()
	{
	    return $this->_features;
	}
	
	/**
	 * Check if the application support an actor type. By default all the actor types are supported 
	 * 
	 * @return int
	 */
	public function getAssignmentOption()
	{	    
        return $this->_assignment_option;
	}	
	
    /**
     * return an array of default assignments
     * 
     * @return array
     */	
    public function getDefaultAssignments()
    {
        return $this->_default_assingments;   
    }
    
	/**
	 * Set the summerizers
	 * 
	 * @param KCommandContext $context Context parameter
	 * 
	 * @return void
	 */
	public function setStoryOptions($context)
	{
		
	}
	
	/**
	 * Nullify Records when a set of node ids are delete either by deleting the records or 
	 * setting a column to null
	 *
	 * @param array  $ids     An array of node ids
	 * @param string $table   The table name
	 * @param array  $nullify An array of column to nullify if it matches the any of the ids
	 * @param array  $delete  A column that if matched with any of the deleted node then delete the record. 
	 *                        Usually set to node_id
	 * 
	 * @return void
	 */
	protected function _nullifyRecords($ids, $table, $nullify = array(), $delete = 'node_id')
	{
	    $store  = KService::get('anahita:domain.store.database');
	    
	    if ( $delete )	       
	    {
	        $query = "DELETE FROM #__$table WHERE $delete IN (".$store->quoteValue($ids).")";
	        $store->execute($query);
	    }
	    settype($nullify, 'array');
	    foreach($nullify as $column)
	    {
	        $query  = "UPDATE #__$table SET $column = NULL WHERE $column IN (".$store->quoteValue($ids).")";
	        $store->execute($query);
	    }
	}
}