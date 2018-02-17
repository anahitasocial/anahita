<?php

/**
 * Abstract Domain Entityset.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
abstract class AnDomainEntitysetAbstract extends AnObjectSet
{
    /**
     * Repository.
     *
     * @var AnDomainRepositoryAbstract
     */
    protected $_repository;

    /**
     * The query that will load the set. If an array is
     * passed it will be used as query conditions.
     *
     * @var AnDomainQuery|array
     */
    protected $_query;

    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_repository = $config->repository;

        //@TODO maybe we need a data loader interface instead
        //if the query is set then
        //reset the data
        $this->_query = $config->query;

        //if we are performing lazy load
        if ($config->data === null) {
            $this->_object_set = null;
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
        $config->append(array(
            'repository' => $this->getIdentifier()->name,
        ));

        parent::_initialize($config);
    }

    /**
     * If the method has a format is[A-Z] then it's a behavior name.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments = array())
    {
        $parts = AnInflector::explode($method);

        if ($parts[0] == 'is' && isset($parts[1])) {
            $behavior = lcfirst(substr($method, 2));

            return !is_null($this->_repository->getBehavior($behavior));
        }

        return parent::__call($method, $arguments);
    }

    /**
     * Finds an entity within the entityset the matches the criteria. If $set
     * is passed then it finds a set.
     *
     * @param array|string $needle
     * @param bool         $set
     *
     * @return AnDomainEntityAbstract|null
     */
    public function find($needle, $set = false)
    {
        if ($needle instanceof KObjectHandlable) {
            return parent::find($needle);
        }

        $entities = array();

        foreach ($this as $entity) {
            foreach ($needle as $key => $value) {
                $v = AnHelperArray::getValue($entity, $key);
                if (is($value, 'AnDomainEntityAbstract') ||
                     is($value, 'AnDomainEntityProxy')) {
                    $is_equal = $value->eql($v);
                } else {
                    $is_equal = $value == $v;
                }
                if (!$is_equal) {
                    break;
                }
            }
            if ($is_equal) {
                if ($set) {
                    $entities[] = $entity;
                } else {
                    return $entity;
                }
            }
        }

        if (!$set) {
            return;
        }

        return new AnDomainEntityset(new KConfig(array(
            'data' => $entities,
            'repository' => $this->_repository,
          )));
    }

    /**
     * Inspects the entityset.
     *
     * @param bool $dump
     *
     * @return array;
     */
    public function inspect($dump = true)
    {
        $data = array();

        foreach ($this as $entity) {
            $data[] = $entity->inspect(false);
        }
        if ($dump) {
            var_dump($data);
        } else {
            return $data;
        }
    }

    /**
     * Return the entityset repository.
     *
     * @return AnDomainAbstractRepository
     */
    public function getRepository()
    {
        if (!$this->_repository instanceof AnDomainRepositoryAbstract) {
            if (!$this->_repository instanceof KServiceIdentifier) {
                $this->setRepository($this->_repository);
            }

            $this->_repository = $this->getService($this->_repository);
        }

        return $this->_repository;
    }

    /**
     * Set the repository using identifier or a repository object.
     *
     * @param mixed $repository
     */
    public function setRepository($repository)
    {
        if (!$repository instanceof AnDomainRepositoryAbstract) {
            if (is_string($repository) && strpos($repository, '.') === false) {
                $identifier = clone $this->getIdentifier();
                $identifier->type = 'repos';
                $identifier->path = array();
                $identifier->name = $repository;
            } else {
                $identifier = $this->getIdentifier($repository);
            }

            $repository = $identifier;
        }

        $this->_repository = $repository;
    }

    /**
     * Return the entityset an an array of entities.
     *
     * @return array
     */
    public function toArray()
    {
        $this->_loadData();

        $array = array();

        foreach ($this as $entity) {
            $array[] = $entity;
        }

        return $array;
    }

    /**
     * (non-PHPdoc).
     *
     * @see KObjectSet::contains()
     */
    public function contains(KObjectHandlable $object)
    {
        $this->_loadData();

        return parent::contains($object);
    }

    /**
     * (non-PHPdoc).
     *
     * @see KObjectSet::extract()
     */
    public function extract(KObjectHandlable $object)
    {
        $this->_loadData();

        return parent::extract($object);
    }

    /**
     * (non-PHPdoc).
     *
     * @see KObjectSet::insert()
     */
    public function insert(KObjectHandlable $object)
    {
        $this->_loadData();
        parent::insert($object);
    }

    /**
     * (non-PHPdoc).
     *
     * @see KObjectSet::merge()
     */
    public function merge(KObjectSet $set)
    {
        $this->_loadData();
        parent::merge($set);
    }

    /**
     * (non-PHPdoc).
     *
     * @see KObjectSet::top()
     */
    public function top()
    {
        $this->_loadData();

        return parent::top();
    }

    /**
     * Retrieve an array of column values and return an array of
     * objects, scarlar or a single boolean value.
     *
     * @param  	string 	The column name.
     *
     * @return mixed
     */
    public function __get($column)
    {
        $this->_loadData();

        $return = null;

        $description = $this->getRepository()->getDescription();

        if ($property = $description->getProperty($column)) {
            if ($property->isAttribute()) {
                if ($property->isScalar()) {
                    $return = $property->getType() == 'boolean' ? 'boolean' : 'array';
                }
            }
        }

        return $this->_forward('attribute', $column, array(), $return);
    }

    /**
     * Set a property for each individual entity.
     *
     * @param string $column
     * @param mixed  $value
     *
     * @return AnDomainEntitysetDefault
     */
    public function __set($column, $value)
    {
        $this->_loadData();

        return parent::__set($column, $value);
    }

    /**
     * Count Data.
     *
     * @param booelan $load If the flag is set to on. If the qurey is set, it will
     *                      perform a count query instead of loading all the objects
     *
     * @return int
     */
    public function count($load = true)
    {
        //if query is set, and the data is not loaded
        //lets use the query to get the count
        if (isset($this->_query) && !$this->isLoaded() && !$load) {
            $query = AnDomainQuery::getInstance($this->getRepository(), $this->_query);

            return $query->fetchValue('count(*)');
        } else {
            $this->_loadData();
            $result = parent::count();
        }

        return $result;
    }

    /**
     * Rewind the Iterator to the first element.
     */
    public function rewind()
    {
        $this->_loadData();

        return parent::rewind();
    }

    /**
     * Checks if current position is valid.
     *
     * @return bool
     */
    public function valid()
    {
        $this->_loadData();

        return parent::valid();
    }

    /**
     * Return the key of the current element.
     *
     * @return scalar
     */
    public function key()
    {
        $this->_loadData();

        return parent::key();
    }

    /**
     * Return the current element.
     *
     * @return mixed
     */
    public function current()
    {
        $this->_loadData();

        return parent::current();
    }

    /**
     * Move forward to next element.
     */
    public function next()
    {
        $this->_loadData();

        return parent::next();
    }

    /**
     * Load the entities before getting an entity of offset.
     *
     * @return
     *
     * @param $offset Object
     */
    public function offsetGet($offset)
    {
        $this->_loadData();

        return parent::offsetGet($offset);
    }

    /**
     * Returns the iterator.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        $this->_loadData();

        return parent::getIterator();
    }

    /**
     * Returns whether a the set has been loaded or not.
     *
     * @return bool
     */
    public function isLoaded()
    {
        return isset($this->_object_set);
    }

    /**
     * If the data is null, then load the data.
     */
    final protected function _loadData()
    {
        if (!$this->isLoaded()) {
            $this->_object_set = new ArrayObject();
            foreach ($this->_getData() as $object) {
                $this->insert($object);
            }
        }
    }

    /**
     * (non-PHPdoc).
     *
     * @see KObjectSet::serialize()
     */
    public function serialize()
    {
        $this->_loadData();

        return parent::serialize();
    }

    /**
     * Returns the data.
     *
     * @return array
     */
    protected function _getData()
    {
        $data = array();

        if (isset($this->_query)) {
            $data = $this->getRepository()->fetch($this->_query, AnDomain::FETCH_ENTITY_LIST);
        }

        return $data;
    }

    /**
     * Deletes all the entities by loading them and marking them for
     * deletion.
     */
    public function delete()
    {
        foreach ($this as $entity) {
            $entity->delete();
        }

        return $this;
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
     * (non-PHPdoc).
     *
     * @see KObjectSet::__clone()
     */
    public function __clone()
    {
        $this->_loadData();
        parent::__clone();
    }
}
