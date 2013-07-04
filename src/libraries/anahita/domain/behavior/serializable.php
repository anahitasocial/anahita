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
 * Serializer behavior. This behavior allows to serialize an entity into an scalar 
 * array of key/value pairs. The result then can be converted into JSON,XML or 
 * any other format.
 * 
 * NOTE : This is experimental API and will be changed in future releases 
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainBehaviorSerializable extends AnDomainBehaviorAbstract
{
	/**
	 * Serializer object
	 *
	 * @var AnDomainSerializerAbstract
	 */
	protected $_serializer;
		
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
		
		$this->_serializer = $config->serializer;
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
			'serializer' => $config->mixer->getIdentifier()->name
		));
	
		parent::_initialize($config);
	}
    
    /**
     * Return an array of serializable data of the entity in format of associative array
     * 
     * @return array
     */
    public function toSerializableArray()
    {
        return $this->getSerilizer()->toSerializableArray($this->_mixer);      
    }
    
    /**
     * Return a serializer object
     * 
     * @return AnDomainSerializerAbstract
     */
    public function getSerilizer()
    {
    	if ( !$this->_serializer instanceof AnDomainSerializerAbstract )
    	{
    		if ( !$this->_serializer instanceof KServiceIdentifier ) {
    			$this->setSerializer($this->_serializer);
    		}
    		
    		$this->_serializer = $this->getService($this->_serializer);
    	}        
        
        return $this->_serializer;
    }
    
    /**
     * Set the serializer
     * 
     * @param AnDomainSerializerAbstract|string $serializer
     * 
     * @return void
     */
    public function setSerializer($serializer)
    {
    	if ( !$serializer instanceof AnDomainSerializerAbstract )
    	{
    		if(is_string($serializer) && strpos($serializer, '.') === false )
    		{
    			$identifier         = clone $this->_repository->getIdentifier();    			
    			$identifier->path = array('domain','serializer');
    			$identifier->name   = $serializer;
    			register_default(array('identifier'=>$identifier,'prefix'=>$this->_repository->getClone()));
    		}
    		else  $identifier = $this->getIdentifier($serializer);
    	
    		$serializer = $identifier;
    	}
    	
    	$this->_serializer = $serializer;
    }
}