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
 * Validatable Behavior. 
 * 
 * Some Examples
 * 
 * <code>
 *  $entity->validateData('someProperty',array('format'=>'string','length'=>100));
 *  $entity->validateData('someProperty',array('uniqueness')); //checks if someProperty is unqique
 *  //we can also sanitize values using the same approach
 *  //sanitize the alias property as a slug
 *  $entity->sanitizeData('alias', array('format'=>'slug'));  
 * </code> 
 *
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 * @uses       AnDomainValidatorAbstract
 */
class AnDomainBehaviorValidatable extends AnDomainBehaviorAbstract
{
    /**
     * Domain Validator
     * 
     * @var AnDomainValidatorAbstract
     */
    protected $_validator;
    
    /**
     * Tracks entities errors
     * 
     * @var AnObjectArray
     */
    protected $_errors;
        
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
        
        $this->_validator = $config->validator;
        
        $this->_errors    = $this->getService('anahita:object.array');
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
            'priority'   => KCommand::PRIORITY_LOWEST,
            'validator'  => $this->_repository->getIdentifier()->name
        ));
    
        parent::_initialize($config);
    }
    
    /**
     * Adds an error object for 
     * 
     * @param AnError|array|string $error Error object
     * 
     * @return AnDomainBehaviorValidatable
     */
    public function addError($error)
    {
        if ( is_string($error) ) {
            $error = array('message'=>$error);
        }
        
        if ( is_array($error) ) {
            $error = new AnError($error);
        }
        
        if ( !isset($this->_errors[$this->_mixer]) ) {
            $this->_errors[$this->_mixer] = new KObjectSet();
        }
        
        $this->_errors[$this->_mixer]->insert($error);
        
        return $this;
    } 
    
    /**
     * Return a set of entity errors
     * 
     * @return AnObjectSet
     */
    public function getErrors()
    {                
        if ( !isset($this->_errors[$this->_mixer]) ) {
            $this->_errors[$this->_mixer] = new AnObjectSet();
        }
        
        return clone $this->_errors[$this->_mixer];
    }
    
    /**
     * Only validate the entity and return true or false if the entity 
     * passed the validation
     *
     * @return boolean
     */    
    public function validateEntity()
    {
    	return $this->getValidator()->validateEntity($this->_mixer);
    }
    
    /**
     * Validates an entity properties values using the passed validations.
     * If no validations are passed, the properties are validated using their
     * default validations
     * 
     * @param string|array $property    One or more properties to validate
     * @param array        $validations The validations to use 
     * 
     * @return boolean
     */
    public function validateData($property, $validations = null)
    {
       $value = $this->_mixer->get($property);
       $ret   = $this->_mixer->getValidator()
                        ->validateData($this->_mixer, $property, $value, $validations);
                        
       return $ret !== false;
    }    
    
    /**
     * Sanitizes an entity properties values using the passed validations.
     * If no validations are passed, the properties are validated using their
     * default validations
     * 
     * @param string|array $property    One or more properties to validate
     * @param array        $validations The validations to use 
     * 
     * @return void
     */
    public function sanitizeData($property, $validations = null)
    {
        $value = $this->_mixer->get($property);
        $value = $this->_mixer->getValidator()
                        ->sanitizeData($this->_mixer, $property, $value, $validations);
                
        $this->_mixer->getRepository()->getCommandChain()->disable();
        $this->_mixer->set($property, $value);
        $this->_mixer->getRepository()->getCommandChain()->enable();
        return $this->_mixer;
    }
    
    /**
     * Return the repository validator
     *
     * @return AnDomainValidatorAbstract
     */
    public function getValidator()
    {
        if ( !$this->_validator instanceof AnDomainValidatorAbstract )
        {
            if ( !$this->_validator instanceof KServiceIdentifier ) {
                $this->setValidator($this->_validator);
            }
            
            $config = array(
                'description' => $this->_repository->getDescription()       
            );
            
            $this->_validator = $this->getService($this->_validator, $config);
        }
        
        return $this->_validator;
    }

    /**
     * Method to set the validator
     *
     * @param mixed $validator Validator object. Can be an KServiceIdentifier or string     
     *
     * @return  AnDomainBehaviorValidatable
     */
    public function setValidator($validator)
    {
        if(!($validator instanceof AnDomainValidatorAbstract))
        {
            if(is_string($validator) && strpos($validator, '.') === false )
            {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('domain','validator');
                $identifier->name = $validator;
            }
            else $identifier = $this->getIdentifier($validator);            
            
            register_default(array('identifier'=>$identifier,'prefix'=>$this->_repository->getClone()));
            
            $validator = $identifier;
        }
    
        $this->_validator = $validator;
    
        return $this;
    }
            
    /**
     * Called before a property value is set. This method will try to invoke _sanitize[Property Name]
     * if it exist.
     *
     * KCommandContxt $context Context parmeter. Contains keys property, value
     *
     * @return boolean If false is returned the property value will not be set
     */
    protected function _beforeEntitySetdata($context)
    {
       $context['value'] = $this->_repository->getValidator()
                        ->sanitizeData($context->entity, $context->property, $context->value);
    }
    
    /**
     * Reset the entity errors
     * 
     * @return void
     */
    public function resetErrors()
    {
        //reset the errors
        unset($this->_errors[$this->_mixer]);
    }
    
    /**
     * Validates an entity using the entity validator object
     *
     * @param KCommandContext $context
     * 
     * @return boolean
     */
    protected function _onEntityValidate($context)
    {
        return $this->_repository->getValidator()->validateEntity($context->entity);        
    }
}
