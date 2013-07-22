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
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * App entity 
 *
 * @category   Anahita
 * @package    Com_Apps
 * @subpackage Domain_Entity
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComAppsDomainEntityApp extends ComBaseDomainEntityNode
{
	/**
	 * Application Access Constants
	 */
	const ACCESS_OPTIONAL	  = 0;	
	const ACCESS_GLOBAL		  = 1;
	const ACCESS_NEVER		  = 2;
		
	/**
	 * Application Icon URL
	 * 
	 * @var string
	 */
	protected $_icon_url;	
	
	/**
	 * Application name. by default it's the same as the application identifer package
	 * 
	 * @var string
	 */
	protected $_name;
	
	/**
	 * Path to the config XML file
	 * 
	 * @var string
	 */
	protected $_config_file;
	
	/**
	 * Info
	 * 
	 * @var array
	 */
	protected $_info;
	
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
            'attributes'    => array(
                'always'  => array('column'=>'is_default')
            ),
			'relationships' => array(
				'installs'	 => array('child'=>'enable'),
				'actortypes' => array('through'=>'assignment','as'=>'assignments')
			),
			'behaviors'	 => array(				
				'authorizer',
				'orderable'	,
				'enableable'
			)
		));
		
		parent::_initialize($config);
	}
						
	/**
	 * Return application information from its manifest.xml file
	 * 
	 * @param string $key The key information from the manifest file 
	 * 
	 * @return string
	 */	
	public function getInfo($key)
	{
		if ( !isset($this->_info) )
		{
			jimport('joomla.filesystem.folder');
			
			$info	   = array();
			
			$admin_dir = JPATH_ADMINISTRATOR .DS. 'components'.DS.$this->component;
			$site_dir  = JPATH_SITE .DS. 'components'.DS.$this->component;
			
			if ( JFolder::exists($admin_dir) ) {
				$folder = $admin_dir;
				$files = JFolder::files($admin_dir, '.xml$');	
			} else if ( JFolder::exists($site_dir) ) {
				$folder = $site_dir;
				$files  = JFolder::files($site_dir, '.xml$');	
			} else {
				$files = array();
			}
			foreach ($files as $file)
			{
				if ($data = JApplicationHelper::parseXMLInstallFile($folder.DS.$file))
					foreach($data as $k => $v) {
						$info[$k] = $v;
					}
			}
			
			$this->_info = $info;
		}
		
		return isset($this->_info[$key]) ? $this->_info[$key] : null;
	}	
	
	/**
	 * Return the app delegate
	 * 
	 * @return ComAppsDomainDelegateDefault 
	 */
	public function getDelegate()
	{
		if ( !isset($this->_config) ) {
			$parts = explode('_', $this->component);
			$identifier = 'com://site/'.$parts[1].'.delegate';
			register_default(array('identifier'=>$identifier, 'default'=>'ComAppsDomainDelegateDefault'));
			$this->_config = $this->getService($identifier, array('component'=>$this->component,'app'=>$this));			
		}
		return $this->_config;
	}
		
	/**
	 * Assigns an app to a list of actor identifiers
	 *
	 * @param array $actors Array of identifiers
	 * 
	 * @return void
	 */
	public function assignTo($actors)
	{				
		foreach($actors as $actor => $access) 
		{
			$actortype  = $this->actortypes->findOrCreate(array('name'=>$actor));
			
			$assignment = $this->actortypes->find($actortype)
				->setData('access', $access);
			;
			
			if ( $access == 0 ) {
				$assignment->delete();
			}
		}
	}
	
	/**
	 * Get the app assignment to an actor
	 *
	 * @param KServiceIdentifier $actor
	 * 
	 * @return void
	 */
	public function getAssignment($actor)
	{	    
		$identifier = is($actor, 'KServiceIdentifier', 'string') ? (string) $actor : (string)$actor->description()->getInheritanceColumnValue()->getIdentifier();		
		$assignment = $this->assignments->find(array('actortype.name'=>$identifier));
		$assignment = $assignment ? $assignment->access : self::ACCESS_OPTIONAL;
		
        //if it's always app then access is global
        if ( $this->always === true )
            $assignment = self::ACCESS_GLOBAL;
        elseif ( $this->always === false )
            $assignment = self::ACCESS_NEVER;
            
		//if assignent is optional and the access option can not be
		//options then return always 
		elseif ( $assignment == self::ACCESS_OPTIONAL && $this->getAssignmentOption() === ComAppsDomainDelegateDefault::ASSIGNMENT_OPTION_NOT_OPTIONAL )
		{
		    $assignment = self::ACCESS_GLOBAL;
		}
		
		return $assignment;
	}
	
	/**
	 * Enables an app for an actor
	 *
	 * @param ComActorsDomainEntityActor $actor Actor object
	 * 
	 * @return void
	 */
	public function addToProfile($actor)
	{
		$install = $this->getService('repos:apps.enable')
			->findOrCreate(array('actor'=>$actor, 'app'=>$this));

		return $install;		
	}
	
  	/**
     * Check if an app is enabled for an actor 
     *
     * @param ComActorsDomainEntityActor $actor The actor for which to see if an app is enabled or not
     * 
     * @return boolean
     */
    public function enabled($actor)
    {
        if ( !isset($actor->__apps) ) 
        {
            $components = $this->getService('repos:apps.enable')
                        ->getQuery()
                        ->actor($actor)
                        ->fetchValues('app.component');
            
            $actor->__apps = $components;
        }
        
        return in_array($this->component, $actor->__apps);              
    }
	
	/**
	 * Removes an app from a profile
	 *
	 * @param ComActorsDomainEntityActor $actor Actor object
	 * 
	 * @return void
	 */
	public function removeFromProfile($actor)
	{
		$this->getService('repos:apps.enable')
			->destroy(array('actor'=>$actor, 'app'=>$this));		
	}		
	
	/**
	 * Return path to application config XML
	 * 
	 * @return string
	 */
	public function getConfigFile()
	{	
		if ( file_exists($this->_config_file) )
			return $this->_config_file;
			
		return null;
	}
		
	/**
	 * Registers event dispatcher
	 *
	 * @param KEventDispatcher $dispatcher Event dispatche
	 *
	 * @return void
	 */
	public function registerEventDispatcher(KEventDispatcher $dispatcher)
	{
	    $dispatcher->addEventSubscriber($this->getDelegate());
	}
	
    /**
     * Forwards the authorization to the delegate
     * 
     * @param string $name   The authrorization name
     * @param array  $config An array of options
     * 
     * @return boolean
     */
    public function authorize($name, $config = array())
    {
        $ret = $this->__call('authorize', array($name, $config));
        
        if ( $ret !== false )
        {
            $method = 'authorize'.ucfirst($name);
            
            if ( method_exists($this->getDelegate(), $method) ) 
            {
                $context = new KCommandContext($config);
                $context->append(array('viewer'=>get_viewer(),'entity'=>$this));
                $ret = $this->getDelegate()->$method($context);
            }
        }
        
        return $ret;
    }
    
	/**
	 * Forward a call to the delegate object 
	 * 
	 * @param string $method The method to forward
	 * @param array  $args   Array of arguments
	 * 
	 * @return mixed
	 */
	public function __call($method, $args = array())
	{
		try {
			return parent::__call($method, $args);
		} catch(Exception $e) {
			return call_object_method($this->getDelegate(), $method, $args);
		}
	}
}