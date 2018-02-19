<?php

/**
 * Onetomany set decorator decorates an entityset with one to many aggregated relationship.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnDomainEntitysetDecoratorOnetomany extends AnObjectDecorator
{
    /**
     * Force creation of a singleton.
     *
     * @param KConfigInterface  $config    An optional KConfig object with configuration options
     * @param KServiceInterface $container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier)) {
            $classname = $config->service_identifier->classname;
            $instance = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }

        return $container->get($config->service_identifier);
    }

    /**
     * The aggregate root.
     *
     * @var AnDomainEntityAbstract
     */
    protected $_root;

    /**
     * Child property in the many set.
     *
     * @var string
     */
    protected $_property;

    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        $this->_root = $config->root;

        $this->_property = $config->property;

        $config->object = $config->service_container
        ->get($config->repository->getEntityset(), $config->toArray());

        parent::__construct($config);
    }

    /**
     * Return an entity of the aggregated type and set the initial
     * property.
     *
     * @param array $data
     * @param array $config Extra configuation for instantiating the object
     *
     * @return AnDomainEntityAbstract
     */
    public function findOrAddNew($data = array(), $config = array())
    {
        $entity = $this->find($data);

        if (!$entity) {
            $entity = $this->addNew($data, $config);
        }

        return $entity;
    }

    /**
     * Find an entity with the passed condition.
     *
     * @param array $conditions
     *
     * @return AnDomainEntityAbstract
     */
    public function find($conditions)
    {
        $conditions[$this->_property] = $this->_root;

        $found = $this->getRepository()->find($conditions);

        return $found;
    }

    /**
     * Return an entity of the aggregated type and set the initial
     * property.
     *
     * @param array $data
     * @param array $config Extra configuation for instantiating the object
     *
     * @return AnDomainEntityAbstract
     */
    public function addNew($data = array(), $config = array())
    {
        $config = new KConfig($config);
        $config['data'] = $data;
        $entity = $this->getRepository()->getEntity($config);
        $this->insert($entity);

        return $entity;
    }

    /**
     * Insert an entity to the aggregation.
     *
     * @see KObjectSet::insert()
     */
    public function insert($entity)
    {
        $entity->set($this->_property, $this->getRoot());

        //only add the entity into the entityset if it has
        //already been loaded
        //otherwise don't
        if ($this->getObject()->isLoaded()) {
            $this->getObject()->insert($entity);
        }

        return $entity;
    }

    /**
     * Removes an object from the aggregation.
     *
     * @see KObjectSet::extract()
     */
    public function extract($entity)
    {
        //if entity is required then delete the entity
        $property = $this->getRepository()
                         ->getDescription()
                         ->getProperty($this->_property);

        if ($property->isRequired()) {
            $entity->delete();
        } else {
            $entity->set($this->_property, null);
        }
    }

    /**
     * Return the aggregate root.
     *
     * @return AnDomainEntityAbstract
     */
    public function getRoot()
    {
        return $this->_root;
    }

    /**
     * Overloaded call function to handle behaviors and forward all
     * calls to to the object regardless.
     *
     * @param  string   The function name
     * @param  array    The function arguments
     *
     * @return mixed The result of the function
     */
    public function __call($method, $arguments)
    {
        $object = $this->getObject();
        $parts = AnInflector::explode($method);

        if ($parts[0] == 'is' && isset($parts[1])) {
            $behavior = lcfirst(substr($method, 2));

            return !is_null($this->getRepository()->getBehavior($behavior));
        } else {
            return call_object_method($object, $method, $arguments);
        }
    }

    /**
     * Check if the object exists in the queue.
     *
     * Required by interface ArrayAccess
     *
     * @param KObjectHandlable $object
     *
     * @return bool Returns TRUE if the object exists in the storage, and FALSE otherwise
     *
     * @throws InvalidArgumentException if the object doesn't implement KObjectHandlable
     */
    public function offsetExists($object)
    {
        if (!$object instanceof KObjectHandlable) {
            throw new InvalidArgumentException('Object needs to implement KObjectHandlable');
        }

        return $this->contains($object);
    }

    /**
     * Returns the object from the set.
     *
     * Required by interface ArrayAccess
     *
     * @param KObjectHandlable $object
     *
     * @return KObjectHandlable
     *
     * @throws InvalidArgumentException if the object doesn't implement KObjectHandlable
     */
    public function offsetGet($object)
    {
        if (!$object instanceof KObjectHandlable) {
            throw new InvalidArgumentException('Object needs to implement KObjectHandlable');
        }

        return $this->getObject()->offsetGet($object);
    }

    /**
     * Store an object in the set.
     *
     * Required by interface ArrayAccess
     *
     * @param KObjectHandlable $object
     * @param mixed            $data   The data to associate with the object [UNUSED]
     *
     * @return \KObjectSet
     *
     * @throws InvalidArgumentException if the object doesn't implement KObjectHandlable
     */
    public function offsetSet($object, $data)
    {
        if (!$object instanceof KObjectHandlable) {
            throw new InvalidArgumentException('Object needs to implement KObjectHandlable');
        }

        $this->insert($object);

        return $this;
    }

    /**
     * Removes an object from the set.
     *
     * Required by interface ArrayAccess
     *
     * @param KObjectHandlable $object
     *
     * @return \KObjectSet
     *
     * @throws InvalidArgumentException if the object doesn't implement the KObjectHandlable interface
     */
    public function offsetUnset($object)
    {
        if (!$object instanceof KObjectHandlable) {
            throw new InvalidArgumentException('Object needs to implement KObjectHandlable');
        }

        $this->extract($object);

        return $this;
    }

    /**
     * Destory a set of entities without instantiating them.
     *
     * If no condition is passed then all the entities within this set
     * is destroyed
     *
     * @param array $conditions
     *
     * @return
     */
    public function destroy($conditions = array())
    {
        $conditions[$this->_property] = $this->_root;
        $this->getRepository()->destroy($conditions);
    }
}
