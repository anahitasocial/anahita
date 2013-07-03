<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Domain Behaviors.
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class AnDomainBehaviorAbstract extends KBehaviorAbstract
{
    /**
     * A reference to the repository
     * 
     * @var AnDomainRepositoryAbstract
     */
    protected $_repository;
    
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
	    $this->_repository = $config->mixer;
	    
	     parent::__construct($config);	    
	     
	     if ( !empty($config->aliases) )
	         foreach($config->aliases as $alias => $property)
	             $this->_mixer->getDescription()->setAlias($property, $alias);
	     	     
	     $this->_mixer->getDescription()->setAttribute( KConfig::unbox($config->attributes) );
	     $this->_mixer->getDescription()->setRelationship( KConfig::unbox($config->relationships) );
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
            'attributes'      => array(),
            'relationships'   => array()
	    ));
	
	    parent::_initialize($config);
	}
	
	/**
	 * @{inheritdoc}
	 */
	public function getMixableMethods(KObject $mixer = null)
	{
	     $methods = parent::getMixableMethods($mixer);
         return array_diff($methods, array('getRepository'));
	}

	/**
	 * Behavior Repository
	 *
	 * @return AnDomainRepositoryAbstract
	 */
	public function getRepository()
	{
	    return $this->_repository;
	}
	
	/**
	 * Command handler
	 * 
	 * This function transmlated the command name to a command handler function of 
	 * the format '_beforeX[Command]' or '_afterX[Command]. Command handler
	 * functions should be declared protected.
	 * 
	 * @param 	string  	The command name
	 * @param 	object   	The command context
	 * @return 	boolean		Can return both true or false.  
	 */
	public function execute( $name, KCommandContext $context) 
	{
        $identifier = $context->caller->getIdentifier();
        
	    if($context->entity instanceof AnDomainEntityAbstract) 
        {
	        $this->setMixer($context->entity);
            $identifier = $context->entity->getIdentifier();
	    }
        
        $type = $identifier->path;
        $type = array_pop($type);        
	    
        $parts  = explode('.', $name);
        $method = '_'.($parts[0]).ucfirst($type).ucfirst($parts[1]);
        
        if(method_exists($this, $method)) {
            return $this->$method($context);
        }
        
	    return true;
	}
}
