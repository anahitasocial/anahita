<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Attribute
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Attribute. Attributes are immutable (or mutable) vaule object properties of an entity 
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Attribute
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class AnDomainAttributeProperty extends AnDomainPropertyAbstract implements AnDomainPropertySerializable
{
	/**
	 * The property type
	 * 
	 * @var KServiceIdentifier
	 */
	protected $_type;
		
	/**
	 * The defaut value of an attribute
	 * 
	 * @var mixed
	 */
	protected $_default;
	
	/**
	 * The column of a table that the property maps to
	 *
	 * @var AnDomainResourceColumn
	 */
	protected $_column;
	
	/**
	 * Format of the value
	 *
	 * @var string
	 */
	protected $_format;	
	
    /**
	 * Configurator
	 *
	 * @param KConfig $config Property Configuration 
	 * 
	 * @return void
	 */
	public function setConfig(KConfig $config)
	{
		parent::setConfig($config);
				
		$this->_type	= $config->type;
		
		if ( !$this->isScalar() )
		    $this->_type = AnDomainAttribute::getClassname($this->_type);
		
		$this->_default = $config->default;
		$this->_format  = $config->format;		
		$this->_column  = $config->column;
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
	    $config->append(array(
            'type'    => 'string'
	    ));
	
	    parent::_initialize($config);
	}	
	
	/**
	 * Return the column
	 *
	 * @return AnDomainResourceColumn
	 */
	public function getColumn()
	{
	    return $this->_column;
	}
		
	/**
	 * Set default value
	 * 
	 * @param mixed $value The default value 
	 * 
	 * @return AnDomainAttributeProperty
	 */
	public function setDefault($value)
	{
		$this->_default = $value;
		return $this;
	}

	/**
	 * Returns whether the attibute is scsalar or not
	 *
	 * @return boolean
	 */
	public function isScalar()
	{
	    return in_array($this->getType(), array('integer','float','string', 'boolean'));
	}
	
	/**
	 * Return the format
	 *
	 * @return string
	 */
	public function getFormat()
	{
	    return $this->_format;
	}
	
	/**
	 * Return the property type
	 * 
	 * @return string
	 */
	public function getType()
	{
		return $this->_type;
	}
    
    /**
     * Set the attribute type
     * 
     * @return void
     */
    public function setType($type)
    {
        $this->_type = $type;
    }
    
	/**
	 * Clones the default value or create a new one if a type is given
	 * 
	 * @return mixed
	 */
	public function getDefaultValue()
	{
		$default = $this->_default;
		
		if ( $default && !$this->isScalar() )
		{
			$default =  $default instanceof AnDomainAttributeInterface ? clone $default : 
						AnDomainAttribute::getInstance($this->_type);
		}		
		
		return $default;

	}
	
	/**
	 * Return an database storable column/value array
	 *
	 * @param mixed $value The value of the attribute
	 * 
	 * @return array
	 */
	public function serialize($value)
	{
	    if ( $value instanceof AnDomainAttributeInterface )
	        $value = $value->serialize();
	    	    
	    return array((string)$this->_column => $value);
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see AnDomainPropertyAbstract::isMaterializable()
	 */
	public function isMaterializable(array $data)
	{
	    $key  	 = $this->getColumn()->key();
	    return array_key_exists($key, $data);
	}
	
	/**
	 * Materialize the database value into attribute values for an entity
	 *
	 * @param array                  $data   The raw data of the domain entity
	 * @param AnDomainEntityAbstract $entity Domain entity being fetched
	 *
	 * @return string|int|float reutrn a scalar value
	 */
	public function materialize(array $data, $entity)
	{
	    $key  	 = $this->getColumn()->key();
	    $value	 = null;
	    if ( $this->isMaterializable($data) )
	    {	
	        //only set the value type if it's not null
	        if ( $data[$key] !== null ) 
	        {
	            if ( $this->isScalar() )
	            {
	                $value = $data[$key];
	                settype($value, $this->getType());
	            }
	            else 
	            {
	                $value  = AnDomainAttribute::getInstance($this->getType());
	                $value->unserialize($data[$key]);	               
	            }
	        }
	    } else
	    {
	        throw new AnDomainExceptionMapping($this->getName().' Mapping Failed');
	    }
	
	    return $value;
	}	
}