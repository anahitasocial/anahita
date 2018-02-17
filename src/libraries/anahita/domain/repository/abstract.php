<?php

/**
 * A repository acts as the in-memory class for the domain obejcts. can be extended by subclasses.
 * An entity uses a default repository unless specified otherwise.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class AnDomainRepositoryAbstract extends KCommand
{
    /**
     * Entity Description.
     *
     * @var AnDomainDescriptionAbstract
     */
    protected $_description;

    /**
     * Collection Identifier.
     *
     * @var KServiceIdentifier
     */
    protected $_entityset;

    /**
     * Entity Behaviors.
     *
     * @var array
     */
    protected $_behaviors = array();

    /**
     * Return the repository validator.
     *
     * @var string|KServiceIdentifier|AnDomainValidatorAbstract
     */
    protected $_validator;

    /**
     * An entity prototype from which all other entites are created from.
     *
     * @var AnDomainEntityAbstract
     */
    protected $_prototype;

    /**
     * Repository Store [Database].
     *
     * @var AnDomainStoreInterface
     */
    protected $_store;

    /**
     * Repository space.
     *
     * @var AnDomainSpaceAbstract
     */
    protected $_space;

    /**
     * Query Identifier.
     *
     * @var KServiceIdentifier
     */
    protected $_query;

    /**
     * Resources.
     *
     * @var KObjectQeueue
     */
    protected $_resources;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        KService::set($config->service_identifier, $this);

        parent::__construct($config);

        $this->_entityset = $config->entityset;
        $this->_prototype = $config->prototype;
        $this->_store = $config->store;
        $this->_space = $config->space;
        $this->_resources = $config->resources;
        $this->_description = new AnDomainDescriptionDefault($config);
        //$this->_description		 = $this->getService($config->description, KConfig::unbox($config));

        //now set the attributes and relationships
        $this->_description->setAttribute(KConfig::unbox($config->attributes));
        $this->_description->setRelationship(KConfig::unbox($config->relationships));

        $this->_query = $this->getService($config->query, $config->toArray());

        // Mixin the behavior interface
        $config->mixer = $this;

        $this->mixin(new KMixinCommand($config));
        $this->mixin(new KMixinBehavior($config));

        //insert the reposiry with highest priority
        $this->getCommandChain()->enqueue($this, -PHP_INT_MAX);
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
        if (empty($config->resources)) {
            $resource = $this->getIdentifier()->package.'_'.AnInflector::pluralize($this->getIdentifier()->name);

            $config->append(array(
                'resources' => array($resource),
            ));
        }

        $entityset = clone $this->getIdentifier();
        $entityset->path = array('domain','entityset');
        register_default(array(
            'identifier' => $entityset,
            'prefix' => $config->prototype,
          ));

        $description = clone $this->getIdentifier();
        $description->path = array('domain','description');
        register_default(array(
            'identifier' => $description,
            'prefix' => $config->prototype, ));

        $query = clone $this->getIdentifier();
        $query->path = array('domain','query');
        register_default(array(
            'identifier' => $query,
            'prefix' => $config->prototype, ));

        $config->append(array(
            'query' => $query,
            'space' => $this->getService('anahita:domain.space'),
            'store' => $this->getService('anahita:domain.store.database'),
            'entityset' => $entityset,
            'description' => $description,
            'command_chain' => $this->getService('koowa:command.chain'),
            'event_dispatcher' => $this->getService('koowa:event.dispatcher'),
            'dispatch_events' => true,
            'enable_callbacks' => true,
            'behaviors' => array(
                'validatable',
                'cachable',
                'serializable',
            ),
        ));

        //set the resources
        $resources = array_reverse((array) KConfig::unbox($config->resources));
        $config['resources'] = new AnDomainResourceSet(new KConfig(array('store' => $config->store, 'resources' => $resources)));
        $config['repository'] = $this;

        parent::_initialize($config);
    }

    /**
     * Command handler.
     *
     * @param string          $name    The command name
     * @param KCommandContext $context The command context
     *
     * @return bool Can return both true or false.
     */
    final public function execute($command, KCommandContext $context)
    {
        $identifier = $context->caller->getIdentifier();

        if ($context->entity instanceof AnDomainEntityAbstract) {
            $identifier = $context->entity->getIdentifier();
        }

        $type = $identifier->path;
        $type = array_pop($type);

        $parts = explode('.', $command);
        $method = '_'.($parts[0]).ucfirst($type).ucfirst($parts[1]);

        $result = null;

        if (method_exists($this, $method)) {
            $result = $this->$method($context);
        }

        if ($context->entity) {
            $result = $context->entity->execute($command, $context);
        }

        return $result;
    }

    /**
     * Validates an entity.
     *
     * @param AnDomainEntityAbstract $entity The entity to be validatd
     *
     * @return bool
     */
    public function validate($entity)
    {
        //reset the error message
        $context = $this->getCommandContext();
        $context->entity = $entity;
        if ($entity->isValidatable()) {
            $entity->resetErrors();
        }
        $result = $this->getCommandChain()->run('on.validate', $context);

        return $result !== false;
    }

    /**
     * Commits an entity into the data store (database).
     *
     * @param AnDomainEntityAbstract $entity The entity to be committed
     *
     * @return bool Return whether a commit has been succesfull or not
     */
    public function commit($entity)
    {
        switch ($entity->getEntityState()) {
            case AnDomain::STATE_NEW:
                $operation = AnDomain::OPERATION_INSERT;
                $command = 'insert';
                break;
            case AnDomain::STATE_MODIFIED :
                //get all the updated serializable property/value pairs
                $operation = AnDomain::OPERATION_UPDATE;
                $command = 'update';
                break;
            case  AnDomain::STATE_DELETED :
                $operation = AnDomain::OPERATION_DELETE;
                $command = 'delete';
                break;
            default :
                return;
        }

        $context = $this->getCommandContext();
        $context->operation = $operation;
        $context->entity = $entity;
        $context->data = array();
        $store = $this->getStore();

        if ($context->result = $this->getCommandChain()->run('before.'.$command, $context) !== false) {
            $context->data = $entity->getAffectedRowData();

            switch ($operation) {
                case(AnDomain::OPERATION_INSERT) :
                    if (!count($context->data)) {
                        throw new AnDomainRepositoryException('Attempting to store an entity with empty data');
                    }

                    $context->result = $store->insert($this, $context->data);
                    break;

                case(AnDomain::OPERATION_UPDATE) :
                case(AnDomain::OPERATION_DELETE) :

                    $keys = $this->_description->getIdentityProperty()->serialize($entity->getIdentityId());
                    $keys = array($this->_description->getIdentityProperty()->getName() => $entity->getIdentityId());

                    if ($operation & AnDomain::OPERATION_UPDATE) {
                        $context->result = count($context->data) ? $this->update($context->data, $keys) : true;
                    } else {
                        $context->result = $this->destroy($keys);
                    }
            }

            $this->getCommandChain()->run('after.'.$command, $context);
        }

        return    $context->result !== false;
    }

    /**
     * Destroy all the entities from a repository withouth instantiating them.
     *
     * This method disables the chain in order to ensure the query is not modified by the
     * behaviors for unexpected results
     *
     * If a boolean value true is passed as condition then all the records within the repository is
     * updated
     *
     * @param array|AnDomainQuery|bool A condition object. Can be an array or domain query object
     *
     * @return bool
     */
    public function destroy($conditions)
    {
        $result = false;
        if (!empty($conditions)) {
            $this->getCommandChain()->disable();
            $result = $this->getStore()->delete($this, $conditions);
            $this->getCommandChain()->enable();
        }

        return $result;
    }

    /**
     * Updates a set of entities without instantiating them.
     *
     * This method disables the chain in order to ensure the query is not modified by the
     * behaviors for unexpected results
     *
     * If a boolean value true is passed as condition then all the records within the repository is
     * updated
     *
     * @param array|string             $values     The update values. Can be an array of key/value pairs or just an update string
     * @param array|AnDomainQuery|bool $conditions An array of conditions or a domain query or a boolean vaule.
     */
    public function update($values, $conditions)
    {
        $result = false;
        if (!empty($conditions)) {
            $this->getCommandChain()->disable();
            $result = $this->getStore()->update($this, $conditions, $values);
            $this->getCommandChain()->enable();
        }

        return $result;
    }

    /**
     * Fetch an entity. The condition can be a query object, an associative array or an id
     * of an entity.
     *
     * @param mixed $condition The condition for fetching data
     * @param int   $mode      The mode of fetching data. Can be single entity, entity set, value, etc
     *
     * @return mixed
     */
    public function fetch($condition = null, $mode = AnDomain::FETCH_ENTITY)
    {
        $query = AnDomainQuery::getInstance($this, $condition);
        $context = $this->getCommandContext();
        $context->operation = AnDomain::OPERATION_FETCH;
        $context->query = $query;
        $context->mode = $mode;
        $query->fetch_mode = $mode;

        if ($mode & AnDomain::FETCH_ITEM) {
            $context->query->limit(1);
        }

        $disable_chain = $query->disable_chain;

        if ($disable_chain) {
            $this->getCommandChain()->disable();
        }

        if ($this->getCommandChain()->run('before.fetch', $context) !== false) {
            $result = $context->result ? $context['result'] : $this->_fetchResult($context->query, $mode);
            $context->result = $result;
            switch ($mode) {
                case AnDomain::FETCH_ENTITY     :
                    $context->data = $result ? $this->_createEntity($result) : $result;
                    break;
                case AnDomain::FETCH_ENTITY_SET  :
                case AnDomain::FETCH_ENTITY_LIST :
                    $list = array();
                    foreach ($result as $data) {
                        $list[] = $this->_createEntity($data);
                    }
                    if ($mode == AnDomain::FETCH_ENTITY_SET) {
                        $list = $this->getService($this->_entityset, array('repository' => $this, 'query' => $context->query, 'data' => $list));
                    }
                    $context->data = $list;
                    break;
                default :
                    $context->data = $result;
            }

            $this->getCommandChain()->run('after.fetch', $context);
        }

        if ($disable_chain) {
            $this->getCommandChain()->enable();
        }

        return KConfig::unbox($context->data);
    }

    /**
     * Fetch a set of entities. The condition can be a query object, an associative array or an  array of ids.
     *
     * @param mixed $condition The condition for fetching data
     *
     * @return AnDomainEntityset
     */
    public function fetchSet($condition = null)
    {
        return $this->fetch($condition, AnDomain::FETCH_ENTITY_SET);
    }

    /**
     * Return a new entity initialized with the new properties.
     *
     * @param array $config An array of configuration for the entity
     *
     * @return AnDomainEntityAbstract
     */
    public function getEntity($config = array())
    {
        if ($this->getDescription()->isAbstract()) {
            throw new AnDomainRepositoryException('Can not instantiate an abstract entity '.$this->getDescription()->getEntityIdentifier());
        }

        $context = $this->getCommandContext();
        $context->append(array('data' => array()));
        $context->append($config);

        //force data to be stored by real name
        foreach ($context->data as $key => $value) {
            $property = $this->getDescription()->getProperty($key);
            if ($property) {
                unset($context->data[$key]);
                $context->data[$property->getName()] = $value;
            }
        }

        $entity = clone $this->getClone();
        $context->entity = $entity;
        $this->getCommandChain()->run('after.instantiate', $context);

        //set the entity data
        $data = KConfig::unbox($context['data']);
        $entity->setData($data, AnDomain::ACCESS_PROTECTED);

        return $entity;
    }

    /**
     * Return the entityset Identifier.
     *
     * @return KServiceIdentifier
     */
    public function getEntitySet()
    {
        return $this->_entityset;
    }

    /**
     * Return the repository persistent store.
     *
     * @return AnDomainStoreInterface
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * Return the repository space.
     *
     * @return AnDomainStoreInterface
     */
    public function getSpace()
    {
        return $this->_space;
    }

    /**
     * Return the entity description.
     *
     * @return AnDomainDescriptionAbstract
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Return the repostiroy query object.
     *
     * @param bool  $disable_chain If set to to true then the chain is disabled for
     *                             the query instance
     * @param array $condition     A default condition to set for the query
     *
     * @return AnDomainQuery
     */
    public function getQuery($disable_chain = false, $condition = array())
    {
        $query = clone $this->_query;

        if ($disable_chain) {
            $query->disableChain();
        }

        if (!empty($condition)) {
            $query->where($condition);
        }

        return $query;
    }

    /**
     * Get a behavior by identifier.
     *
     * @param mixed $behavior Behavior name
     * @param array $config   An array of options to configure the behavior with
     *
     * @see KMixinBehavior::getBehavior()
     *
     * @return AnDomainBehaviorAbstract
     */
    public function getBehavior($behavior, $config = array())
    {
        if (is_string($behavior)) {
            if (strpos($behavior, '.') === false) {
                $identifier = clone $this->getIdentifier();
                $identifier->path = array('domain','behavior');
                $identifier->name = $behavior;
                register_default(array('identifier' => $identifier, 'prefix' => $this->_prototype));
                $behavior = $identifier;
            }
        }

        return parent::__call('getBehavior', array($behavior, $config));
    }

    /**
     * Extracts an entity from the repository and the space and deletes an entity if
     * it's persisted.
     *
     * @param AnDomainEntityAbstract $entity The entity to extract
     */
    public function extract($entity)
    {
        if ($entity->persisted()) {
            $entity->delete()->save();
        }

        $this->_space->extractEntity($entity);
    }

    /**
     * Find an existing entity with the passed properties. If not found it returns null.
     * If the record is not found then try to fetch the entity from the storage.
     *
     * @param scalar|array $needle The needle to look for
     * @param bool         $fetch  Boolean flag to whether to fetch entity or not if not found
     *
     * @return AnDomainEntityAbstract|null
     */
    public function find($needle, $fetch = true)
    {
        if (empty($needle)) {
            throw new InvalidArgumentException('No condition pased to the repository::find');
        }

        if (!is_array($needle)) {
            $needle = array($this->_description->getIdentityProperty()->getName() => $needle);
        }

        $found = null;
        //there's a key in the needle then it must be a unique entity
        if (count(array_intersect_key($this->_description->getIdentifyingProperties(), $needle)) > 0) {
            $found = $this->_space->findEntity($this->_description, $needle);
        } else {
            $entities = $this->getEntities();
            $found = $entities->find($needle);
        }

        if (!$found && $fetch) {
            $this->getCommandChain()->disable();
            $query = $this->getQuery()->where($needle);
            $found = $this->fetch($query);
            $this->getCommandChain()->enable();
        }

        return $found;
    }

    /**
     * Return the entities of this repository.
     *
     * @return array
     */
    public function getEntities()
    {
        return $this->_space->getEntities($this);
    }

    /**
     * Tries to find an entity from the $data, if not then creates an entity and
     * set the properties.
     *
     * @param array $data   Data to look or create the entity if not found
     * @param array $config An array of configuration to initialize the new entity instance
     *
     * @return AnDomainEntityAbstract|null
     */
    public function findOrAddNew($data, $config = array())
    {
        $entity = $this->find($data);

        //if not found an entity
        //or found one but it' either delete or destroyed
        //create a new entity
        if (!$entity ||
                 ($entity->getEntityState() & AnDomain::STATE_DELETED ||
                  $entity->getEntityState() &  AnDomain::STATE_DESTROYED)) {
            $config = new KConfig($config);
            $config->append(array(
                'data' => $data,
            ));
            $entity = $this->getEntity($config);
        }

        return $entity;
    }

    /**
     * Return an array of resources.
     *
     * @return AnDomainResourceSet
     */
    public function getResources()
    {
        return $this->_resources;
    }

    /**
     * Check if an entity inhertis an interface.
     *
     * @param mixed $interface The name of an interface or class
     *
     * @return bool
     */
    public function entityInherits($interface)
    {
        if (!is_string($interface)) {
            $interface = get_class($interface);
        }

        return $this->_prototype->inherits($interface);
    }

    /**
     * Check if an entity method exist.
     *
     * @param string $method The name of a method
     *
     * @return bool
     */
    public function entityMethodExists($method)
    {
        return $this->_prototype->methodExists($method);
    }

    /**
     * Return a clone of a prototype.
     *
     * @return AnDomainEntitysetAbstract
     */
    public function getClone()
    {
        return $this->_prototype;
    }

    /**
     * Materialize raw data into an entity. It {@uses self::_instantiate} to instantiate the entity.
     *
     * @param array $data The data to create an entity from
     *
     * @return AnDomainEntityAbstract
     */
    protected function _createEntity(array $data)
    {
        //create an instance with its unique keys first
        $keys = array();
        $description = $this->_description;

        $keys = $description->getIdentifyingValues($data);

        if (empty($keys)) {
            throw new AnDomainRepositoryException('Trying to create an entity witout any identiftying keys');
        }

        //if an entity found with them same key already
        //instantiated then return that entity to avoid having
        //duplicate objects
        $entity = $this->find($keys, false);

        if ($entity) {
            return $entity;
        }

        $inheritance_column = $description->getInheritanceColumn();

        if ($inheritance_column &&
                isset($data[$inheritance_column->key()])) {
            $identifier = $data[$inheritance_column->key()];
            $identifier = substr($identifier, strrpos($identifier, ',') + 1);
        } else {
            $identifier = $this->_prototype->getIdentifier();
        }

        $entity = $this->_instantiateEntity($identifier, $data);

        //insert the identity
        $this->_space->insertEntity($entity, $keys);

        $context = new KCommandContext();
        $context->data = $data;
        $context->keys = $keys;

        $entity->execute('after.fetch', $context); //call after fetch

        $entity->reset(); //reset the entity
        return $entity;
    }

    /**
     * Searches a repository to see if it behave as.
     *
     * If a method has the form of is[Behavior Name] it check if the repository behave
     *
     * @param string $method The mising method
     * @param array  $args   Method arguments
     *
     * @return bool|mixed
     */
    public function __call($method, $args)
    {
        // If the method is of the form is[Bahavior] handle it.
        $parts = AnInflector::explode($method);

        if ($parts[0] == 'is' && isset($parts[1])) {
            if ($this->hasBehavior(strtolower($parts[1]))) {
                return true;
            }

            return false;
        }

        return parent::__call($method, $args);
    }

    /**
     * This method is used as a wrapper to fetch a result set from store. Since the fetch method
     * is very hard to override, this method is meant as a way to change a query right before it
     * hits the database without affecting the domain query state.
     *
     * @param AnDomainQuery $query Domain Query
     * @param int           $mode  The mode of the query
     *
     * @return mixed Return the result
     */
    protected function _fetchResult($query, $mode)
    {
        return $this->getStore()->fetch($query, $mode);
    }

    /**
     * If a query is unique then repository tries to search existing entities.
     *
     * @param KCommandContext $context Context
     *
     * @return bool
     */
    protected function _beforeRepositoryFetch(KCommandContext $context)
    {
        //check if the query is retrieving a unique entity
        //if yes check if we already have the entity in the
        //repository or not
        $key = $context->query->getKey();
        if ($key) {
            $context->data = $this->find($key, false);
            if ($context->data) {
                return false;
            }
        }
    }

    /**
     * Instantiate a new entity based on the passed data This method is called from _create.
     *
     * @param string $identifier The identifier of the entity to instantiate
     * @param array  $data       The raw data
     *
     * @return AnDomainEntityAbstract
     */
    protected function _instantiateEntity($identifier, $data)
    {
        //since the identifier doesn't have an
        //application set, it gets the application of the parent
        //repository
        $identifier = KService::getIdentifier($identifier);
        $identifier->application = $this->getIdentifier()->application;

        return clone AnDomain::getRepository($identifier)->getClone();
    }

    /**
     * After an enttiy has been instantiated, all of its states are reset.
     *
     * @param KCommandContext $context Context
     */
    protected function _afterEntityInstantiate(KCommandContext $context)
    {
        $entity = $context->entity;

        $this->getSpace()->setEntityState($entity, AnDomain::STATE_NEW);

        //set the entity default valuees
        $attributes = $this->getDescription()->getAttributes();
        foreach ($attributes as $attribute) {
            $entity->set($attribute->getName(), $attribute->getDefaultValue());
        }

        //materialize required one-to-one relationship
        $relationships = $this->getDescription()->getRelationships();
        foreach ($relationships as $relation) {
            if ($relation->isOneToOne() && $relation->isRequired()) {
                $property = $relation->getName();
                $$entity->set($property, $relation->getChildRepository()->getEntity());
            }
        }
    }

    /**
     * After an enttiy has been inserted into the repository.
     *
     * @param KCommandContext $context Context
     */
    protected function _afterEntityInsert(KCommandContext $context)
    {
        $entity = $context->entity;
        $id = $this->getDescription()->getIdentityProperty()->getName();

        //insert the entity into the space
        $this->getSpace()->insertEntity($entity, array($id => $context['result']));

        //set the identity proeprty value
        $entity->set($id, $context->result);

        $relationships = $this->getDescription()->getRelationships();
        //reset all the one to many and many to many relationships
        foreach ($relationships as $relation) {
            if ($relation->isOneToMany() && !$relation->isOneToOne()) {
                unset($entity->{$relation->getName()});
            }
        }

        $this->getSpace()->setEntityState($entity, AnDomain::STATE_INSERTED);

        //entity is now persisted
        $entity->setPersisted(true);
    }

    /**
     * Set the state after an entity has been updated.
     *
     * @param KCommandContext $context Context
     */
    protected function _afterEntityUpdate(KCommandContext $context)
    {
        $this->getSpace()->setEntityState($context->entity, AnDomain::STATE_UPDATED);
    }

    /**
     * Set the state after an entity has been deleted.
     *
     * @param KCommandContext $context Context
     */
    protected function _afterEntityDelete(KCommandContext $context)
    {
        $this->getSpace()->setEntityState($context->entity, AnDomain::STATE_DESTROYED);
    }
}
