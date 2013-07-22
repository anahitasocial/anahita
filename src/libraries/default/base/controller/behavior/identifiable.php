<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Identifiable Behavior
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseControllerBehaviorIdentifiable extends KControllerBehaviorAbstract
{     
    /**
     * Controller Domain Repository
     *
     * @var string
     */
    protected $_repository;
        
    /**
     * Identifiable key. If this key exists in the request then this behavior
     * will fetch the entity using this key
     * 
     * @return string
     */
    protected $_identifiable_key;
    
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
        
        $this->_repository = $config->repository;
        
        $config->append(array(
            'identifiable_key' => $this->getRepository()->getDescription()->getIdentityProperty()->getName()
        ));        
        
        $this->_identifiable_key = $config->identifiable_key;
        
        //add the identifiable_key 
        $this->getState()->insert($this->_identifiable_key, null, true); 
    }
           
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'repository'    => $config->mixer->getIdentifier()->name ,      
            'priority'      => KCommand::PRIORITY_HIGHEST
        ));

        parent::_initialize($config);
    }
    	
	/**
     * Command handler
     * 
     * @param   string      The command name
     * @param   object      The command context
     * @return  boolean     Can return both true or false.  
     */
    public function execute($name, KCommandContext $context) 
    {        
		$parts = explode('.', $name);
		
        //for any before if the item has been fetched
        //then try to fetch it
		if ( $parts[0] == 'before' ) 
        {
			return $this->_mixer->fetchEntity($context);
		}
    }
    
    /**
     * A list of items that are each identifiable
     * 
     * @param mixed $list list of resources 
     * 
     * @return LibBaseControllerBehaviorIdentifiable
     */
    public function setList($list)
    {
        $this->_mixer->getState()->setList($list);
        return $this->_mixer;   
    }
    
    /**
     * Return the controller list of identifiable objects
     * 
     * @return mixed
     */
    public function getList()
    {
        return $this->_mixer->getState()->getList();
    }    
    
    /**
     * Set the controller identitable item
     * 
     * @param mixed $item The identifiable Item
     * 
     * @return LibBaseControllerBehaviorIdentifiable
     */
    public function setItem($item)
    {
       $this->_mixer->getState()->setItem($item);
       return $this->_mixer; 
    }
    
    /**
     * Return the controller identifiable item
     * 
     * @return mixed
     */
    public function getItem()
    {
        return $this->_mixer->getState()->getItem();
    }
    
    /**
     * Set the controller repository
     * 
     * @param string|AnDomainRepositoryAbstract $repository The domain repository
     * 
     * @return LibBaseControllerResource
     */
    public function setRepository($repository)
    {
        if ( !$repository instanceof AnDomainRepositoryAbstract )
        {
            $identifier = $repository;
            
            if ( strpos($repository,'.') === false ) 
            {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('domain', 'entity');
                $identifier->name = $repository;
            }
            
            $repository = $this->getIdentifier($identifier);            
        }
              
        $this->_repository = $repository;
        
        return $this;
    }

    /**
     * Return the controller repository 
     * 
     * @return AnDomainRepositoryAbstract
     */
    public function getRepository()
    {
        if ( !$this->_repository instanceof AnDomainRepositoryAbstract ) 
        {
            if ( !$this->_repository instanceof KServiceIdentifier ) {
                $this->setRepository($this->_repository);    
            }
            
            $this->_repository = AnDomain::getRepository($this->_repository);
        }
        
        return $this->_repository;
    }
        
 	/**
     * Fetches an entity
     *
     * @param KCommandContext $context
     */
    public function fetchEntity(KCommandContext $context)
    {
    	$context->append(array(
    		'identity_scope' => array()
    	));
    	
        $identifiable_key = $this->getIdentifiableKey();
          
        if ( $values = $this->$identifiable_key ) 
        {
            $scope  = KConfig::unbox($context->identity_scope);
            
            $values = KConfig::unbox($values);
            
            $scope[$identifiable_key] = $values;
            
            if ( is_array($values) ) 
                $mode = AnDomain::FETCH_ENTITY_SET;
            else
                $mode = AnDomain::FETCH_ENTITY;
                   
            $entity = $this->getRepository()->fetch($scope, $mode);
            
            if ( empty($entity) || !count($entity)) 
            {
                $context->setError(new KHttpException(
                    'Resource Not Found', KHttpResponse::NOT_FOUND
                ));
                return false;                       
            }
            
            $this->setItem($entity);
            
            return $entity;
        }    	
    }

    /**
     * Sets the identifiable key
     * 
     * @param string $key The identifiable key
     * 
     * @return LibBaseControllerBehaviorIdentifiable
     */
    public function setIdentifiableKey($key)
    {
        $this->_identifiable_key = $key;
    }
    
    /**
     * Return the identifiable key
     * 
     * @return string
     */
    public function getIdentifiableKey()
    {
        return $this->_identifiable_key;
    }
    
    /**
     * Return the object handle
     * 
     * @return string
     */
    public function getHandle()
    {
    	return KMixinAbstract::getHandle();
    }    
}