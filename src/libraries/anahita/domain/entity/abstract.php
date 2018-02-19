<?php

/**
 * Abstract Domain Entity.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class AnDomainEntityAbstract extends KObject implements ArrayAccess, Serializable
{
    /**
     * Static cache to store runtime information of an entity.
     *
     * @return ArrayObject
     */
    protected static function _cache($entity)
    {
        static $cache;

        if (!$cache) {
            $cache = new AnObjectArray();
        }

        if (!isset($cache[$entity->getRepository()])) {
            $cache[$entity->getRepository()] = new ArrayObject();
        }

        return $cache[$entity->getRepository()];
    }

    /**
     * Stores the properties of the entity that have been modified.
     *
     * @var array();
     */
    protected $_modified = array();

    /**
     * Repository.
     *
     * @var AnDomainRepositoryAbstract
     */
    protected $_repository;

    /**
     * Entity properties.
     *
     * @var AnDomainEntityData
     */
    protected $_data;

    /**
     * Flag to determine if an entity has been persisted into the database or not
     * this flag is only set after an entity has been fetched from the database.
     *
     * @var bool
     */
    protected $_persisted = false;

    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        //very crucial to first store this instance in the service container
        //as this instnace will be used to clone other instances
        $config->service_container->set($config->service_identifier, $this);

        parent::__construct($config);

        //set the repository
        $this->_repository = $config->repository;

        //set the master (prototype)
        $config->prototype = $this;

        $config->append(array(
            'auto_generate' => count(KConfig::unbox($config->attributes)) == 0,
        ));

        $this->getService($config->repository, KConfig::unbox($config));

        if (!$this->getEntityDescription()->getIdentityProperty()) {
            throw new AnDomainDescriptionException('Entity '.$this->getIdentifier().' need an identity property');
        }
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $identifier = clone $this->getIdentifier();

        $identifier->path = array('domain','repository');

        register_default(array('identifier' => $identifier, 'prefix' => $this));

        $config->append(array(
            'attributes' => array(),
            'relationships' => array(),
            'repository' => $identifier,
            'entity_identifier' => $this->getIdentifier(),
        ));

        parent::_initialize($config);
    }

    /**
     * Sets the enity data after it has been fetched from the database storage.
     *
     * @param KCommandContext $context Context
     */
    protected function _afterEntityFetch(KCommandContext $context)
    {
        //set the persistd flag
        $this->setPersisted(true);

        //set the raw data
        $this->_data->setRowData($context['data']);

        //force setting the keys
        foreach ($context['keys'] as $key => $value) {
            $this->_data[$key] = $value;
        }
    }

    /**
     * Returns the state of the entity. It returns one of the constants.
     *
     * @return string
     */
    public function getEntityState()
    {
        return $this->getRepository()->getSpace()->getEntityState($this);
    }

    /**
     * Return if the state of the entity is AnDomain::STATE_NEW.
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->getEntityState() == AnDomain::STATE_NEW;
    }

    /**
     * Return if the state of the entity is AnDomain::STATE_MODIFIED.
     *
     * @return bool
     */
    public function isModified()
    {
        return $this->getEntityState() == AnDomain::STATE_MODIFIED;
    }

    /**
     * Return if the state of the entity is AnDomain::STATE_CLEAN.
     *
     * @return bool
     */
    public function isClean()
    {
        return $this->getEntityState() == AnDomain::STATE_CLEAN;
    }

    /**
     * Return if the state of the entity is AnDomain::STATE_DELETED.
     *
     * @return bool
     */
    public function isDeleted()
    {
        return $this->getEntityState() == AnDomain::STATE_DELETED;
    }

    /**
     * Forwards the call to the space validate entities. However only return
     * true/false depending whether the entity has failed validation.
     *
     * @param mixed &$failed Return the failed set
     *
     * @return bool
     */
    public function validate(&$failed = null)
    {
        $result = $this->getRepository()->getSpace()->validateEntities($failed);
        return !$failed->contains($this);
    }

    /**
     * Tries to only save the entity.
     *
     * @return bool
     */
    public function saveEntity()
    {
        $ret = null;

        if ($this->getRepository()->validate($this)) {
            $ret = $this->getRepository()->commit($this);
        }

        return $ret;
    }

    /**
     * Forwards the call to the space commit entities.
     *
     * @param mixed &$failed Return the failed set
     *
     * @return bool
     */
    public function save(&$failed = null)
    {
        return $this->getRepository()->getSpace()->commitEntities($failed);
    }

    /**
     * Return an array of property => name that has been modified.
     *
     * @return array
     */
    public function getModifiedData()
    {
        return new KConfig($this->_modified);
    }

    /**
     * Reset the state of a entity to clean state.
     *
     * @return AnDomainEntityAbstract
     */
    public function reset()
    {
        $this->getRepository()->getSpace()->setEntityState($this, AnDomain::STATE_CLEAN);
        $this->_modified = array();

        return $this;
    }

    /**
     * Set whether an enity is persisted in the database or not.
     *
     * @param bool $persisted Persistance flag
     */
    public function setPersisted($persisted)
    {
        $this->_persisted = $persisted;
    }

    /**
     * Return if the entity is persisted,  it's not a new record and it was not destroyed.
     *
     * @return bool
     */
    public function persisted()
    {
        return $this->_persisted;
    }

    /**
     * Return the entity identity property name.
     *
     * @return string
     */
    public function getIdentityProperty()
    {
        return $this->getEntityDescription()->getIdentityProperty()->getName();
    }

    /**
     * Return an array of data that can be used to identify this entity.
     *
     * @return array
     */
    public function getIdentifyingData()
    {
        $keys = array_keys($this->getEntityDescription()->getIdentifyingProperties());

        $data = array();

        foreach ($keys as $key) {
            if ($this->_data->isMaterialized($key)) {
                $value = $this->get($key);

                if ($value) {
                    $data[$key] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * Returns the value of the entity identity property.
     *
     * @return int
     */
    public function getIdentityId()
    {
        return $this->get($this->getIdentityProperty());
    }

     /**
      * Get the raw value of a property or return the default value passed.
      *
      * @param  string $name    Then name of the property
      * @param  mixed  $default The default value
      *
      * @return mixed
      */
     public function get($name = null, $default = null)
     {
         $description = $this->getEntityDescription();

        //get the property
        $property = $description->getProperty($name);

         if (!$property) {
             return parent::get($name, $default);
         }

        //get the property name
        $name = $property->getName();

         $value = $this->_data->offsetGet($name);

         if ($property->isRelationship() && $property->isOneToMany() && is_null($value)) {
             //since it's an external relationship
            //lets instantitate a dummy relationship
            //this should happen for the one-to-one relationships
            if ($property->isOneToOne()) {
                return;
            }

             $value = $this->_data[$name] = $property->getSet($this);

             return $value;
         }

         return is_null($value) ? $default : $value;
     }

    /**
     * Set the raw value of a property.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set($name, $value = null)
    {
        $property = $this->getEntityDescription()->getProperty($name);

        if (!$property instanceof AnDomainPropertyAbstract) {
            return parent::set($name, $value);
        }

        //if a value is mixin then get its mixer
        if ($value instanceof KMixinAbstract) {
            $value = $value->getMixer();
        }

        $modify = false;
        $name = $property->getName();

        $context = $this->getRepository()->getCommandContext();

        $context['property'] = $property;
        $context['value'] = $value;
        $context['entity'] = $this;

        if ($this->getRepository()->getCommandChain()->run('before.setdata', $context) === false) {
            return $this;
        }

        $value = $context->value;

        if ($property->isSerializable()) {
            $modify = true;
            if ($property->isRelationship() && $property->isManyToOne() && $value) {
                if (!is($value, 'AnDomainEntityAbstract', 'AnDomainEntityProxy')) {
                    throw new AnDomainExceptionType('Value of '.$property->getName().' must be a AnDomainEntityAbstract');
                }

                //if a relationship is a belongs to then make sure the parent
                //is always saved before child
                //example if $topic->author = new author
                //then save the new author first before saving topic
                //only set the dependency
                if ($value->getEntityState() == AnDomain::STATE_NEW) {
                    //save the child before saving the parent
                    $this->getRepository()->getSpace()->setSaveOrder($value, $this);
                }
            }
            //if value is not null do a composite type checking
            if (!is_null($value)) {
                if ($property->isAttribute() && !$property->isScalar()) {
                    if (!is($value, $property->getType())) {
                        throw new AnDomainEntityException('Value of '.$property->getName().' must be a '.$property->getType().'. a '.get_class($value).' is given.');
                    }
                }
            }
        } elseif ($property->isRelationship() && $property->isOnetoOne()) {
            $child = $property->getChildProperty();
            $current = $this->get($property->getName());

            //if there's a current value and it's existence depends on
            //the parent entity then remove the current
            if ($current && $child->isRequired()) {
                $current->delete();
            }

            //if a one-to-one relationship then there must be a child key for the property
            //then must set the inverse
            if ($value) {
                $value->set($child->getName(), $this);
            }

            $this->_data[$property->getName()] = $value;
        } elseif ($property->isRelationship() && ($property->isManyToMany() || $property->isOneToMany())) {
            $current = $this->get($name);

            if ($current instanceof AnDomainDecoratorOnetomany) {
                $values = KConfig::unbox($value);
                //can be an KObjectArray or KObjectSet object
                if ($values instanceof KObject && $values instanceof Iterator) {
                    $current->delete();

                    foreach ($values as $value) {
                        $current->insert($value);
                    }
                }
            }
        }

        //only modify if the current value is differnet than the new value
        $modify = $modify && !is_eql($this->get($name), $value);

        if ($modify) {
            //lets bring them back to their orignal type
            if (!is_null($value) && $property->isAttribute() && $property->isScalar()) {
                settype($value, $property->getType());
            }

            if ($this->getEntityState() != AnDomain::STATE_NEW) {
                //store the original value for future checking
                if (!isset($this->_modified[$name])) {
                    $this->_modified[$name] = array('old' => $this->get($name));
                }

                $this->_modified[$name]['new'] = $value;

                //check if the new value is the same as the old one then remove the
                if (is_eql($this->_modified[$name]['old'], $this->_modified[$name]['new'])) {
                    //if there are no modified then reset the entity
                    unset($this->_modified[$name]);

                    if (count($this->_modified) === 0) {
                        $this->reset();
                    }
                }
            }

            $this->_data[$property->getName()] = $value;
            $this->getRepository()->getSpace()->setEntityState($this, AnDomain::STATE_MODIFIED);

            //only track modifications for the updated entities
            if ($this->getEntityState() !== AnDomain::STATE_MODIFIED) {
                $this->_modified = array();
            }
        }

        return $this;
    }

    /**
     * ReLoad the entity properties from storage. Overriding any changes.
     *
     * @param array $properties An array of properties. If no properties is passed then
     *                          all of the properites are loaded
     */
    public function load($properties = array())
    {
        $keys = $this->getIdentifyingData();

        if (empty($keys)) {
            throw new AnDomainEntityException('Trying to load an entity without any identifying data');
        }

        //prevent from creating two differne entities
        if (!$this->_persisted) {
            //if found another entity
            $entity = $this->getRepository()->find($keys, false);

            if ($entity && $entity !== $this) {
                $this->reset();

                return $entity->load($properties);
            }
        }

        settype($properties, 'array');

        if (empty($properties)) {
            //only load serializbale properties (i.e. attributes, many to one relationships)
            $properties = array();

            foreach ($this->getEntityDescription()->getProperty() as $property) {
                if ($property->isSerializable()) {
                    $properties[] = $property->getName();
                }
            }

            $keys = array_keys($this->getEntityDescription()->getIdentifyingProperties());
            $properties = array_diff($properties, $keys);
        }

        if ($this->_data->load($properties)) {
            //enttiy has been fetched for the first time
            if (!$this->_persisted) {
                $keys = $this->getIdentifyingData();
                $this->_persisted = true;
                $this->getRepository()->getSpace()->insertEntity($this, $keys);
                $this->reset();
            } else {
                //the loaded properties are no longer modified
                foreach ($properties as $property) {
                    unset($this->_modified[$property]);
                }

                //reset the element if there are no modified
                if (count($this->_modified) === 0) {
                    $this->reset();
                }
            }
        } else {
            //if a persisted entity can not be loaded then
            //it must mean the data doesn't exists in the store
            //can we assume then the data has been somehow deleted ?
            //if that's the case then we need to set the state to destroyed
            if ($this->persisted()) {
                $this->getRepository()->getSpace()->setEntityState($this, AnDomain::STATE_DESTROYED);
            } else {
                $this->reset();
            }
        }

        return $this;
    }

    /**
     * This method is used to return an array of entity row data that have either
     * been changed or are new.
     *
     * @return array
     */
    public function getAffectedRowData()
    {
        $data = array();

        $description = $this->getEntityDescription();

        switch ($this->getEntityState()) {
            case AnDomain::STATE_NEW :
                //get all the serializable property/value pairs
                foreach ($description->getProperty() as $name => $property) {
                    if ($property instanceof AnDomainPropertySerializable) {
                        $data[$name] = $name;
                    }
                }
                //@TODO why are we forcing to unset the property
                //unset($data[$description->getIdentityProperty()->getName()]);
                break;
            case AnDomain::STATE_MODIFIED :
                //get all the updated serializable property/value pairs
                $data = array_keys($this->_modified);
                break;
            case  AnDomain::STATE_DELETED :
                break;
            default :
                return $data;
        }

        $tmp = array();

        foreach ($data as $name) {
            $value = $this->get($name);
            $property = $description->getProperty($name);
            $tmp = array_merge($tmp, $property->serialize($value));
        }

        $data = $tmp;

        if ($description->getInheritanceColumn() && $this->getEntityState() == AnDomain::STATE_NEW) {
            $data[(string) $description->getInheritanceColumn()] = (string) $description->getInheritanceColumnValue();
        }

        return $data;
    }

    /**
     * Set the row (raw) data of the entity.
     *
     * @param array $row The row data of an entity
     */
    public function setRowData(array $row)
    {
        $this->_data->setRowData($row);
    }

    /**
     * Return an array of row data. If a value is passed for $column then it returns
     * the value of the column wihtin the row.
     *
     * @param string $column The column name. Optional can be null
     */
    public function getRowData($column = null)
    {
        $data = $this->_data->getRowData();

        if (!is_null($column)) {
            $data = isset($data[$column]) ? $data[$column] : null;
        }

        return $data;
    }

    /**
     * Set the value of a property by checking for custom setter. An array
     * can be passed to set multiple properties.
     *
     * @param string|array $property Property name
     * @param mixd         $value    Property value
     */
    public function setData($property, $value = null)
    {
        if (is_array($property)) {
            $description = $this->getEntityDescription();
            $properties = $property;
            $access = pick($value, AnDomain::ACCESS_PUBLIC);

            foreach ($properties as $key => $value) {
                $property = $description->getProperty($key);
                if ($property && $property->getWriteAccess() >= $access) {
                    //ignore any type related exceptions
                      try {
                          $this->setData($property->getName(), $value);
                      } catch (AnDomainExceptionType $e) {
                          print $e->getMessage();
                          die;
                      }
                } elseif (!$property) {
                    $this->$key = $value;
                }
            }

            return $this;
        } else {
            $name = $property;
            $description = $this->getEntityDescription();
            $property = $description->getProperty($property);

            if (!$property instanceof AnDomainPropertyAbstract) {
                $this->set($name, $value);

                return $this;
            }

            $name = $property->getName();
            $method = 'set'.ucfirst($name);

            if ($this->methodExists($method)) {
                $this->$method($value);
            } else {
                //only set the property if it's not write proteced (write != private )
                if ($property->getWriteAccess() < AnDomain::ACCESS_PROTECTED) {
                    throw new KException(get_class($this).'::$'.$name.' is write protected');
                }

                $this->set($name, $value);
            }

            return $this;
        }
    }

    /**
     * get the value of a property by checking for custom getter. If no property
     * is passed an array of properties is returend.
     *
     * @param string $property Property name
     * @param string $default  Default value
     *
     * @return mixed
     */
    public function getData($property = AnDomain::ACCESS_PUBLIC, $default = null)
    {
        $description = $this->getEntityDescription();

        if (gettype($property) == 'integer') {
            $properties = $this->getEntityDescription()->getProperty();
            $access = (int) $property;
            $data = array();

            foreach ($properties as $name => $property) {
                if ($property->getReadAccess() >= $access) {
                    $data[$name] = $this->getData($name);
                }
            }

            return $data;
        }

        if (!$prop = $description->getProperty($property)) {
            return $this->get($property, $default);
        }

        $method = 'get'.ucfirst($property);

        if ($this->methodExists($method)) {
            $value = $this->$method();
        } else {
            $value = $this->get($property);
        }

        if (is_null($value)) {
            $value = $default;
        }

        return $value;
    }

    /**
     * Set a property value {@link self::setData}.
     */
    public function __set($property, $value)
    {
        if ($this->getEntityDescription()->getProperty($property)) {
            $this->setData($property, $value);
        } else {
            $this->$property = $value;
        }
    }

    /**
     * Get a property value {@link self::setData}.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        $result = null;

        if ($this->getEntityDescription()->getProperty($property)) {
            $result = $this->getData($property, null);
        }

        return $result;
    }

    /**
     * Check if a property has been set.
     *
     * @param string $property
     *
     * @return bool
     */
    public function __isset($property)
    {
        if ($property = $this->getEntityDescription()->getProperty($property)) {
            $name = $property->getName();

            if ($this->_data->offsetExists($name)) {
                //if a property is one to one or many to one make sure the value
                //actually exists in the database
                $value = $this->_data->offsetGet($name);

                if ($value instanceof AnDomainEntityProxy) {
                    if ($property->isRelationship() && $property->isOneToOne()) {
                        if (!$value->getObject()) {
                            $this->getRepository()->getCommandChain()->disable();
                            $this->set($name, null);
                            $this->getRepository()->getCommandChain()->enable();

                            return false;
                        }
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * unset the value of a property.
     *
     * @param string $property
     */
    public function __unset($property)
    {
        if ($property = $this->getEntityDescription()->getProperty($property)) {
            unset($this->_data[$property->getName()]);
        }
    }

    /**
     * Check if the offset exists.
     *
     * Required by interface ArrayAccess
     *
     * @param   int     The offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * Get an item from the data by offset.
     *
     * Required by interface ArrayAccess
     *
     * @param   int     The offset
     *
     * @return mixed The item from the array
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * @see self::__unset
     *
     * Required by interface ArrayAccess
     *
     * @param   int     The offset of the item
     * @param   mixed   The item's value
     *
     * @return object
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);

        return $this;
    }

    /**
     * @see self::__unset
     *
     * @param   int     The offset of the item
     *
     * @return object
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);

        return $this;
    }

    /**
     * To be used instead of the === operator for deep object comparison.
     *
     * @param AnDomainAbstractEntity $entity
     *
     * @return bool
     */
    public function eql($entity)
    {
        if ($entity instanceof AnDomainEntityProxy) {
            $object = $entity->getObject();
        } elseif ($entity instanceof KMixinAbstract) {
            $object = $entity->getMixer();
        } else {
            $object = $entity;
        }

        return $this === $object;
    }

    /**
     * Set the state of the entity to deleted. Not the entity is not persisted but
     * its state only changed to deleted.
     *
     * @return bool
     */
    public function delete()
    {
        $this->getRepository()->getSpace()->setEntityState($this, AnDomain::STATE_DELETED);

        return $this;
    }

    /**
     * Implements magic method. Dynamically mixes a mixin.
     *
     * @param string $method Method name
     * @param array  $args   Array of arugments
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        //If the method hasn't been mixed yet, load all the behaviors
        if (!isset($this->_mixed_methods[$method])) {
            $key = 'behavior.'.$method;

            if (!self::_cache($this)->offsetExists($key)) {
                self::_cache($this)->offsetSet($key, false);

                $behaviors = $this->getRepository()->getBehaviors();

                foreach ($behaviors as $behavior) {
                    if (in_array($method, $behavior->getMixableMethods())) {
                        //only mix the mixin that has $method
                        self::_cache($this)->offsetSet($key, $behavior);
                        break;
                    }
                }
            }

            if ($behavior = self::_cache($this)->offsetGet($key)) {
                $this->mixin($behavior);
            }
        }

        $parts = AnInflector::explode($method);

        if ($parts[0] == 'is') {
            if (isset($this->_mixed_methods[$method])) {
                return true;
            } else {
                return false;
            }
        }

        if (!isset($this->_mixed_methods[$method])) {
            if ($parts[0] == 'get' || $parts[0] == 'set') {
                $property = lcfirst(AnInflector::implode(array_slice($parts, 1)));
                $property = $this->getEntityDescription()->getProperty($property);
                if ($property) {
                    if ($parts[0] == 'get') {
                        return $this->getData($property->getName());
                    } else {
                        return $this->setData($property->getName(), array_shift($args));
                    }
                }
            }
        }

        return parent::__call($method, $args);
    }

    /**
     * Executes a command on entity.
     *
     * @param string                $command The command to execute. It must of form part1.part2
     * @param KCommandContext|array $context The command context
     *
     * @return bool
     */
    public function execute($command, $context)
    {
        if (!$context instanceof  KCommandContext) {
            $context = new KCommandContext($context);
        }

        $parts = explode('.', $command);
        $method = '_'.$parts[0].'Entity'.ucfirst($parts[1]);
        $result = null;

        if (method_exists($this, $method)) {
            $result = $this->$method($context);
        }

        return $result;
    }

    /**
     * Checks if the object or one of it's mixins inherits from a class.
     *
     * @param 	string|object 	The class to check
     *
     * @return bool Returns TRUE if the object inherits from the class
     */
    public function inherits($class)
    {
        if ($this instanceof $class) {
            return true;
        }

        if (!parent::inherits($class)) {
            //check the mixins registered with the entity mapper
            $behaviors = $this->getRepository()->getBehaviors();

            foreach ($behaviors as $behavior) {
                if ($behavior instanceof $class) {
                    return true;
                }
            }

            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the entity repository.
     *
     * @return AnDomainRepositoryAbstract
     */
    public function getRepository()
    {
        if (!$this->_repository instanceof AnDomainRepositoryAbstract) {
            $this->_repository = $this->getService($this->_repository);
        }

        return $this->_repository;
    }

    /**
     * Get the entity description.
     *
     * @return AnDomainRepositoryAbstract
     */
    public function getEntityDescription()
    {
        return $this->getRepository()->getDescription();
    }

    /**
     * Inspects an enttiy. $dump is passed as true. the result is passed to the method var_dump.
     *
     * @param bool $dump Flag to whether dump the data or not
     *
     * @return array;
     */
    public function inspect($dump = true)
    {
        $properties = $this->getEntityDescription()->getProperty();
        $identifier = $this->getIdentifier();

        $data = array('identifier' => $identifier);
        $data['hash'] = $this->getHandle();
        $data['state'] = $this->getEntityState();
        $data['keys'] = implode(',', array_keys($this->getEntityDescription()->getIdentifyingProperties()));
        $data['required'] = array();

        foreach ($properties as $name => $property) {
            $value = isset($this->$name) ? $this->get($name) : null;

            if ($property->isRequired()) {
                $data['required'][] = $name;
            }

            $value = $value ? $property->serialize($value) : array();
            $data['data'][$name] = count($value) < 2 ? array_pop($value) : $value;
        }

        if (count($this->_modified)) {
            foreach ($this->getModifiedData() as $property => $changes) {
                $property = $this->getEntityDescription()->getProperty($property);
                $old = $property->serialize($changes->old);
                $new = $property->serialize($changes->new);
                $data['modified'][$property->getName()] = array('old' => count($old) < 2 ? array_pop($old) : $old, 'new' => count($new) < 2 ? array_pop($new) : $new);
            }
        }

        if (count($this->getRowData())) {
            $data['row'] = $this->getRowData();
        }

        $serialized = $this->getAffectedRowData();

        if (!empty($serialized)) {
            $data['serilized'] = $serialized;
        }

        if ($dump) {
            var_dump($data);
        }

        return $data;
    }

    /**
     * Make a clone of the entity with it's attributes. The unique properties are
     * not copied. If deep copy is selected, then this method tries to replicate
     * all the ony-to-many relationships as well.
     *
     * @param bool $deep Flag to determine to whether to deep copy.
     *
     * @return AnDomainEntityAbstract
     */
    public function cloneEntity($deep = true)
    {
        $copy = $this->getRepository()->getEntity();
        $properties = $this->getEntityDescription()->getProperty();
        $data = array();

        foreach ($properties as $property) {
            if ($property->isUnique()) {
                continue;
            }

            if ($property === $this->getEntityDescription()->getIdentityProperty()) {
                continue;
            }

            $name = $property->getName();

            if ($property->isAttribute()) {
                $copy->set($name, $property->isScalar() ? $this->get($name) : clone $this->get($name));
            } elseif ($property->isRelationship()) {
                //if it's a belongs to then set the value in the
                //copy as  the original
                if ($property->isManyToOne()) {
                    $copy->set($name, $this->get($name));
                }
                //copy the one to one
                elseif ($deep && $property->isOneToOne()) {
                    if (isset($this->$name)) {
                        $copy->set($name, $this->get($name)->cloneEntity($deep));
                    }
                }
                //copy the one to many
                elseif ($deep && $property->isOneToMany() && !$property->isManyToMany()) {
                    $copy->set($name, $this->get($name)->cloneEntity($deep));
                }
            }
        }

        return $copy;
    }

    /**
     * Return an array of methods.
     *
     * (non-PHPdoc)
     *
     * @see KObject::getMethods()
     */
    public function getMethods()
    {
        $behaviors = $this->getRepository()->getBehaviors();

        foreach ($behaviors as $behavior) {
            $this->mixin($behavior);
        }

        return parent::getMethods();
    }

    /**
     * Return an array of raw data from which the object can be
     * recreated.
     *
     * @return array
     */
    public function serialize()
    {
        $row = array();
        $row = $this->_data->getRowData();
        $row = array_merge($row, $this->getAffectedRowData());

        return serialize(array('row' => $row, 'identifier' => (string) $this->getIdentifier()));
    }

    /**
     *
     */
    public function unserialize($data)
    {
        $data = unserialize($data);
        $this->_repository = AnDomain::getRepository($data['identifier']);
        $this->_data = new AnDomainEntityData(new KConfig(array('entity' => $this)));
        $this->_data->setRowData($data['row']);
        $this->__service_container = $this->_repository->getService();
        $this->__service_identifier = $this->_repository->getIdentifier($data['identifier']);
    }

    /**
     * Clones a model - this is for when creating a list of models and we don't want to instantiate them.
     *
     * @return
     */
    public function __clone()
    {
        $this->_data = new AnDomainEntityData(new KConfig(array('entity' => $this)));
    }

    /**
     * Check if method exists between the entities and all its behavior. It caches
     * the result once for the whole repository in order to improve performance.
     *
     * @return bool
     */
    public function methodExists($method)
    {
        $key = 'method.'.$method;

        if (!self::_cache($this)->offsetExists($key)) {
            $result = false;

            if (method_exists($this, $method)) {
                $result = true;
            } elseif (self::_cache($this)->offsetExists('behavior.'.$method)) {
                $result = self::_cache($this)->offsetGet('behavior.'.$method);
            } else {
                $behaviors = $this->getRepository()->getBehaviors();

                foreach ($behaviors as $behavior) {
                    if (in_array($method, $behavior->getMixableMethods($this))) {
                        $result = true;
                        break;
                    }
                }
            }

            self::_cache($this)->offsetSet($key, $result);
        }

        return self::_cache($this)->offsetGet($key);
    }
}
