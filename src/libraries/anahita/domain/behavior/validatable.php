<?php

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
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 *
 * @uses       AnDomainValidatorAbstract
 */
class AnDomainBehaviorValidatable extends AnDomainBehaviorAbstract
{
    /**
     * Domain Validator.
     * 
     * @var AnDomainValidatorAbstract
     */
    protected $_validator;

    /**
     * Tracks entities errors.
     * 
     * @var AnObjectArray
     */
    protected $_errors;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_validator = $config->validator;
        $this->_errors = $this->getService('anahita:object.array');
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'priority' => AnCommand::PRIORITY_LOWEST,
            'validator' => $this->_repository->getIdentifier()->name,
        ));

        parent::_initialize($config);
    }

    /**
     * Adds an error object for.
     * 
     * @param AnError|array|string $error Error object
     * 
     * @return AnDomainBehaviorValidatable
     */
    public function addError($error)
    {
        if (is_string($error)) {
            $error = array('message' => $error);
        }

        if (is_array($error)) {
            $error = new AnError($error);
        }

        if (! isset($this->_errors[$this->_mixer])) {
            $this->_errors[$this->_mixer] = new AnObjectSet();
        }

        $this->_errors[$this->_mixer]->insert($error);

        return $this;
    }

    /**
     * Return a set of entity errors.
     * 
     * @return AnObjectSet
     */
    public function getErrors()
    {
        if (! isset($this->_errors[$this->_mixer])) {
            $this->_errors[$this->_mixer] = new AnObjectSet();
        }

        return clone $this->_errors[$this->_mixer];
    }

    /**
     * Only validate the entity and return true or false if the entity 
     * passed the validation.
     *
     * @return bool
     */
    public function validateEntity()
    {
        return $this->getValidator()->validateEntity($this->_mixer);
    }

    /**
     * Validates an entity properties values using the passed validations.
     * If no validations are passed, the properties are validated using their
     * default validations.
     * 
     * @param string|array $property    One or more properties to validate
     * @param array        $validations The validations to use 
     * 
     * @return bool
     */
    public function validateData($property, $validations = null)
    {
        $value = $this->_mixer->get($property);
        $ret = $this->_mixer->getValidator()->validateData($this->_mixer, $property, $value, $validations);

        return $ret !== false;
    }

    /**
     * Sanitizes an entity properties values using the passed validations.
     * If no validations are passed, the properties are validated using their
     * default validations.
     * 
     * @param string|array $property    One or more properties to validate
     * @param array        $validations The validations to use 
     */
    public function sanitizeData($property, $validations = null)
    {
        $value = $this->_mixer->get($property);
        $value = $this->_mixer->getValidator()->sanitizeData($this->_mixer, $property, $value, $validations);

        $this->_mixer->getRepository()->getCommandChain()->disable();
        $this->_mixer->set($property, $value);
        $this->_mixer->getRepository()->getCommandChain()->enable();

        return $this->_mixer;
    }

    /**
     * Return the repository validator.
     *
     * @return AnDomainValidatorAbstract
     */
    public function getValidator()
    {
        if (! $this->_validator instanceof AnDomainValidatorAbstract) {
            if (! $this->_validator instanceof AnServiceIdentifier) {
                $this->setValidator($this->_validator);
            }

            $config = array(
                'description' => $this->_repository->getDescription(),
            );

            $this->_validator = $this->getService($this->_validator, $config);
        }

        return $this->_validator;
    }

    /**
     * Method to set the validator.
     *
     * @param mixed $validator Validator object. Can be an AnServiceIdentifier or string     
     *
     * @return AnDomainBehaviorValidatable
     */
    public function setValidator($validator)
    {
        if (!($validator instanceof AnDomainValidatorAbstract)) {
            if (is_string($validator) && strpos($validator, '.') === false) {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('domain','validator');
                $identifier->name = $validator;
            } else {
                $identifier = $this->getIdentifier($validator);
            }

            register_default(array(
                'identifier' => $identifier, 
                'prefix' => $this->_repository->getClone(),
            ));

            $validator = $identifier;
        }

        $this->_validator = $validator;

        return $this;
    }

    /**
     * Called before a property value is set. This method will try to invoke _sanitize[Property Name]
     * if it exist.
     *
     * AnCommandContxt $context Context parmeter. Contains keys property, value
     *
     * @return bool If false is returned the property value will not be set
     */
    protected function _beforeEntitySetdata($context)
    {
        $context['value'] = $this->_repository->getValidator()
        ->sanitizeData($context->entity, $context->property, $context->value);
    }

    /**
     * Reset the entity errors.
     */
    public function resetErrors()
    {
        //reset the errors
        unset($this->_errors[$this->_mixer]);
    }

    /**
     * Validates an entity using the entity validator object.
     *
     * @param AnCommandContext $context
     * 
     * @return bool
     */
    protected function _onEntityValidate($context)
    {
        return $this->_repository->getValidator()->validateEntity($context->entity);
    }
}
