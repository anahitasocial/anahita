<?php

/**
 * Domain Query Class.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnDomainQuery extends KObject implements KCommandInterface
{
    /**
     * Query Operation.
     */
    const QUERY_SELECT_DEFAULT = 2;
    const QUERY_SELECT = 4;
    const QUERY_UPDATE = 8;
    const QUERY_DELETE = 16;

    /**
     * Creates a new query object from a set of conditions.
     *
     * @param mixed $repository A repository
     * @param mixed $conditions A set of conditions
     *
     * @return AnDomainQuery
     */
    public static function getInstance($repository, $conditions)
    {
        if (!$conditions instanceof self) {
            if ($repository instanceof self) {
                $query = clone $repository;
                $repository = $query->getRepository();
            } else {
                $query = $repository->getQuery();
            }

            $identity_property = $repository->getDescription()->getIdentityProperty();

            if (!empty($conditions)) {
                if (is_numeric($conditions)) {
                    $conditions = array($conditions);
                }

                //if conditions is number of an array of numbers
                if (is_numeric($conditions) ||
                        (is_array($conditions) && is_numeric(key($conditions)))) {
                    $conditions = array($identity_property->getName() => $conditions);
                }

                $query->where($conditions);
            }
        } else {
            //clone the query to avoid changing the passed query
            $query = clone $conditions;
        }

        return $query;
    }

    /**
     * Links to other query.
     *
     * @var array
     */
    public $link = array();

    /**
     * The quer binds.
     *
     * @var array
     */
    public $binds = array();

    /**
     * Query Operation. SELECT, COUNT, UPDATE or Custom.
     *
     * @var int
     */
    public $operation;

    /**
     * Distinct operation.
     *
     * @var bool
     */
    public $distinct = false;

    /**
     * The columns.
     *
     * @var array
     */
    public $columns = array();

    /**
     * The from element.
     *
     * @var array
     */
    public $from = array();

    /**
     * The join element.
     *
     * @var array
     */
    public $join = array();

    /**
     * The where element.
     *
     * @var array
     */
    public $where = array();

    /**
     * The group element.
     *
     * @var array
     */
    public $group = array();

    /**
     * The having element.
     *
     * @var array
     */
    public $having = array();

    /**
     * The order element.
     *
     * @var string
     */
    public $order = array();

    /**
     * The limit element.
     *
     * @var int
     */
    public $limit = null;

    /**
     * The limit offset element.
     *
     * @var int
     */
    public $offset = null;

    /**
     * Resource prefix.
     *
     * @var string
     */
    protected $_prefix;

    /**
     * state.
     *
     * @var KConfig
     */
    protected $_state;

    /**
     * Repository.
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
        parent::__construct($config);

        $this->_repository = $config->repository;

        $this->operation = array('type' => self::QUERY_SELECT_DEFAULT, 'value' => '');

        $this->_prefix = $config->resource_prefix;
        $this->_state = $config->state;

        if ($config['query_options'] instanceof Closure) {
            $config['query_options']($this);
        } else {
            AnDomainQueryHelper::applyFilters($this, $config['query_options']);
        }
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'repository' => $this->getIdentifier()->name,
            'resource_prefix' => '#__',
            'query_options' => array(),
            'state' => array(
                'instance_of' => array(),
                'disable_chain' => false,
            ),
        ));
    }

    /**
     * If a $key is an array then a query will be created from the array.
     *
     * @see KDatabaseQuery::where()
     */
    public function where($key, $constraint = null, $value = null, $condition = 'AND')
    {
        if (in_array($constraint, array('AND', 'OR'))) {
            $condition = $constraint;
            $constraint = null;
        } elseif (is_array($key)) {
            if (!is_numeric(key($key))) {
                foreach ($key as $k => $v) {
                    $this->where($k, '=', $v, 'AND');
                }

                return $this;
            }
        }

        $where = array();
        $where['property'] = $key;

        if (isset($constraint)) {
            $constraint = strtoupper($constraint);
            $condition = strtoupper($condition);

            $list = $value instanceof KObjectSet || is_array($value);

            //fix the contstraint
            if ($list || $value instanceof self) {
                if ($constraint == '=') {
                    $constraint = 'IN';
                } elseif ($constraint == '<>') {
                    $constraint = 'NOT IN';
                }
            }

            $where['constraint'] = $constraint;
            $where['value'] = $value;
        }

        $where['condition'] = count($this->where) ? $condition : '';

        $this->where[] = $where;

        return $this;
    }

    /**
     * Return a subclause.
     *
     * @param string $condition The glue condition
     *
     * @return AnDomainQuery
     */
    public function clause($condition = 'AND')
    {
        if (!count($this->where)) {
            $condition = null;
        }
        $clause = new AnDomainQueryClause($this, $condition);

        return $clause;
    }

    /**
     * Return the resource prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Converts the query to a update query.
     *
     * @param array|string $values The values to update
     *
     * @return AnDomainQuery
     */
    public function update($values)
    {
        $this->operation = array('type' => self::QUERY_UPDATE, 'value' => $values);

        return $this;
    }

    /**
     * Converts the query to a delete query.
     *
     * @return AnDomainQuery
     */
    public function delete()
    {
        $this->operation = array('type' => self::QUERY_DELETE, 'value' => '');

        return $this;
    }

    /**
     * Built a select query.
     *
     * @param array|string $columns A string or an array of column names
     *
     * @return AnDomainQuery
     */
    public function select($columns)
    {
        settype($columns, 'array');
        $this->columns = array_unique(array_merge($this->columns, $columns));
        $this->operation = array('type' => self::QUERY_SELECT_DEFAULT, 'value' => '');

        return $this;
    }

    /**
     * Build a customized operation.
     *
     * @param array $columns Columns names
     *
     * @return AnDomainQuery
     */
    public function columns($columns)
    {
        $this->operation = array('type' => self::QUERY_SELECT, 'value' => $columns);

        return $this;
    }

    /**
     * Links a query to anotehr query by joining the main query resoruce.
     *
     * @param mixed        $query     Query object
     * @param string|array $condition Array condition
     * @param array        $options   Options
     *
     * @return AnDomainQuery
     */
    public function link($query, $condition = array(), $options = array())
    {
        if (is_string($query)) {
            if (strpos($query, '.') === false) {
                $name = $query;

                if ($property = $this->getRepository()->getDescription()->getProperty($name)) {
                    $name = $property->getName();
                }

                AnDomainQueryHelper::addRelationship($this, $name);
                $link = $this->getLink($name);
                $config = new KConfig($condition);
                $config->append(array(
                        'type' => $link->type,
                        'bind_type' => $link->bind_type,
                        'conditions' => array(),
                ));
                $link->offsetSet('type', $config->type)->offsetSet('bind_type', $config->bind_type);
                foreach ($config->conditions as $key => $value) {
                    if (is_string($value)) {
                        $value = AnDomainQueryBuilder::getInstance()->parseMethods($this, $value);
                    } elseif ($value instanceof AnDomainResourceColumn) {
                        $value = clone $value;
                    }
                    $link->conditions[$key] = $value;
                }

                return $this;
            } else {
                $query = AnDomain::getRepository($query)->getQuery();
            }
        } elseif ($query instanceof AnDomainRepositoryAbstract) {
            $query = $query->getQuery();
        }

        settype($condition, 'array');

        $options = new KConfig($options);

        $options->append(array(
            'type' => 'strong',
            'resource' => $query->getRepository()->getResources()->main(),
            'as' => $query->getRepository()->getDescription()->getEntityIdentifier()->name,
        ));

        if (!isset($this->link[$options->as])) {
            $options->resource->setAlias(AnInflector::underscore($options->as));

            $destination = $query->getRepository()->getDescription()->getInheritanceColumn();

            $link = array(
                'query' => $query,
                'resource' => $options->resource,
                'resource_name' => $options->resource->getAlias(),
                'type' => $options->type,
                'conditions' => array(),
                'bind_type' => $destination ? clone $destination : null,
            );

            foreach ($condition as $key => $value) {
                $link['conditions'][$key] = $value instanceof AnDomainResourceColumn ? clone $value : $value;
            }

            $this->link[$options->as] = new KConfig($link);

            $this->distinct = true;
        }

        return $this;
    }

    /**
     * Returns a query link.
     *
     * @param string $link The link name
     *
     * @return array
     */
    public function getLink($link)
    {
        return isset($this->link[$link]) ? $this->link[$link] : null;
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
        if (!$this->_repository instanceof AnDomainRepositoryAbstract) {
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
     * Return an identiable value for the query.
     *
     * @return mixed|array
     */
    public function getKey()
    {
        $key = false;

        if (count($this->where) == 1) {
            $where = array_values($this->where);
            $where = array_pop($where);

            if (isset($where['property'])) {
                $property = $where['property'];
                $keys = $this->getRepository()->getDescription()->getIdentifyingProperties();
                if (isset($keys[$property]) && isset($where['constraint']) && $where['constraint'] == '=' && !is_array($where['value'])) {
                    $key[$property] = $where['value'];
                }
            }
        }

        return $key;
    }

    /**
     * Built the from clause of the query.
     *
     * @param array|string $resources A string or array of resource names
     *
     * @return AnDomainQuery
     */
    public function from($resources)
    {
        settype($resources, 'array');

        //Prepent the resource prefix
        foreach ($resources as &$resource) {
            if (strpos($resource, $this->_prefix) !== 0) {
                $resource = $this->_prefix.$resource;
            }
        }

        $this->from = array_unique(array_merge($this->from, $resources));

        return $this;
    }

    /**
     * Built the group clause of the query.
     *
     * @param array|string $columns A string or array of ordering columns
     *
     * @return AnDomainQuery
     */
    public function group($columns)
    {
        settype($columns, 'array'); //force to an array

        $this->group = array_unique(array_merge($this->group, $columns));

        return $this;
    }

    /**
     * Built the having clause of the query.
     *
     * @param array|string $columns A string or array of ordering columns
     *
     * @return AnDomainQuery
     */
    public function having($columns)
    {
        settype($columns, 'array'); //force to an array

        $this->having = array_unique(array_merge($this->having, $columns));

        return $this;
    }

    /**
     * Build the order clause of the query.
     *
     * @param array|string $columns   A string or array of ordering columns
     * @param string       $direction Either DESC or ASC
     *
     * @return AnDomainQuery
     */
    public function order($columns, $direction = 'ASC')
    {
        settype($columns, 'array'); //force to an array

        foreach ($columns as $column) {
            $this->order[] = array(
                    'column' => $column,
                    'direction' => $direction,
            );
        }

        return $this;
    }

    /**
     * Built the limit element of the query.
     *
     * @param int $limit  Number of items to fetch.
     * @param int $offset Offset to start fetching at.
     *
     * @return AnDomainQuery
     */
    public function limit($limit, $offset = 0)
    {
        $this->limit = (int) $limit;
        $this->offset = (int) $offset;

        return $this;
    }

    /**
     * Built the join clause of the query.
     *
     * @param string       $type      The type of join; empty for a plain JOIN, or "LEFT", "INNER", etc.
     * @param string       $resource  The table name to join to.
     * @param string|array $condition Join on this condition.
     *
     * @return AnDomainQuery
     */
    public function join($type, $resource, $condition)
    {
        settype($condition, 'array');

        if (strpos($resource, $this->_prefix) !== 0) {
            $resource = $this->_prefix.$resource;
        }

        $this->join[] = array(
            'type' => strtoupper($type),
            'resource' => $resource,
            'condition' => $condition,
        );

        return $this;
    }

    /**
     * Binds an array of params.
     *
     * @return AnDomainQuery
     */
    public function bind($key, $value = null)
    {
        $data = $key;

        if (!is_array($key)) {
            $data = array($key => $value);
        }

        foreach ($data as $key => $value) {
            $this->binds[$key] = $value;
        }

        return $this;
    }

    /**
     * If set, the repository will disable the chain for this query before fetch. This prevents
     * a query from being tampered with.
     *
     * @return AnDomainQuery
     */
    public function disableChain()
    {
        $this->disable_chain = true;

        return $this;
    }

    /**
     * If a proeprty is used then it will be used as condition. For example $query->id(10) create
     * $query->where('id','=',10).
     *
     * If a method is format of fetch|select[Function Name]Value(s)(property). This will be translated in to
     * fetchValue('{Function Name}(property)'). For example fetchCount => fetchValue('COUNT({property}')
     *
     * If not of the above then AnInflector::underscore($method) will be inserted as a state
     */
    public function __call($method, $arguments = array())
    {
        $match = array();

        //only do condition chaining if it's a real property
        //don't check the parent property
        if ($this->getRepository()->getDescription()->getProperty($method)) {
            $constraint = isset($arguments[1]) ? $arguments[1] : '=';
            $condition = isset($arguments[2]) ? $arguments[2] : 'AND';
            $this->where($method, $constraint, $arguments[0], $condition);
        }
        //if a method is format of fetch|select[Function Name](property). This will be translated in to
        //fetch('{Function Name}(property)'). For example fetchCount => fetchValue('COUNT({property}')
        elseif (preg_match('/(fetch|select)(\w+)/', $method, $match)) {
            deprecated('use '.$match[1].'(FUNC) instead');
            $column = isset($arguments[0]) ? $arguments[0] : '*';

            $property = $this->getRepository()->getDescription()->getProperty($column);

            if ($property) {
                $column = '@col('.$column.')';
            }

            if ($match[1] == 'select') {
                $method = 'columns';
            } elseif (AnInflector::isPlural($match[2])) {
                $method = 'fetchValues';
            } else {
                $method = 'fetchValue';
            }

            $function = strtoupper($match[2]).'('.$column.')';

            return $this->$method($function);
        } elseif (count($arguments) > 0) {
            $this->__set(AnInflector::underscore($method), $arguments[0]);
        } else {
            throw new BadMethodCallException('Call to undefined method :'.$method);
        }

        return $this;
    }

    /**
     * Get a value from the query state.
     *
     * @param string $name State name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_state->$name;
    }

    /**
     * Set a state of the query.
     *
     * @param string $name  Name of the state
     * @param string $value Value of the state
     */
    public function __set($name, $value)
    {
        $state = KConfig::unbox($this->_state->$name);

        if (is_array($state) && is_array($value)) {
            settype($value, 'array');
            $state = array_merge($state, $value);
        } else {
            $state = $value;
        }

        $this->_state->$name = $state;
    }

    /**
     * Calls the query repositry fetch method and pass the query.
     *
     * @param mixed $condition The condition of the fetch. This condition will not affect the query
     *
     * @return AnDomainEntityAbstract
     */
    public function fetch($condition = array())
    {
        $query = self::getInstance($this, $condition);

        return $this->getRepository()->fetch($query);
    }

    /**
     * Calls the query repositry fetch method with the collection mode and pass the query.
     *
     * @param mixed $condition The condition of the fetch. This condition will not affect the query
     *
     * @return AnDomainEntitysetDefault
     */
    public function fetchSet($condition = array())
    {
        $query = self::getInstance($this, $condition);

        return $this->getRepository()->fetch($query, AnDomain::FETCH_ENTITY_SET);
    }

    /**
     * Calls the query repositry fetch method with the value mode and pass the query.
     *
     * @param array $columns   An array of columns whose value to get
     * @param mixed $condition The condition of the fetch. This condition will not affect the query
     *
     * @return AnDomainEntityAbstract
     */
    public function fetchValue($column, $condition = array())
    {
        $query = self::getInstance($this, $condition);

        $query->columns($column);

        return $this->getRepository()->fetch($query, AnDomain::FETCH_VALUE);
    }

    /**
     * Calls the query repositry fetch method with the value mode and pass the query.
     *
     * @param array $columns   An array of columns whose value to get
     * @param mixed $condition The condition of the fetch. This condition will not affect the query
     *
     * @return AnDomainEntityAbstract
     */
    public function fetchValues($column, $condition = array())
    {
        $query = self::getInstance($this, $condition);

        $query->columns($column);

        return $this->getRepository()->fetch($query, AnDomain::FETCH_VALUE_LIST);
    }

    /**
     * Return the row data.
     *
     * @return array
     */
    public function fetchRow($condition = array())
    {
        $query = self::getInstance($this, $condition);

        return $this->getRepository()->fetch($query, AnDomain::FETCH_ROW);
    }

    /**
     * Return the row data.
     *
     * @return array
     */
    public function fetchRows($condition = array())
    {
        $query = self::getInstance($this, $condition);

        return $this->getRepository()->fetch($query, AnDomain::FETCH_ROW_LIST);
    }

    /**
     * Forwards the call to the repository destory.
     *
     * @see AnDomainRepositoryAbstract::destory()
     */
    public function destroy()
    {
        return $this->getRepository()->destroy($this);
    }

    /**
     * Returns a entity set whose data has not been fetched yet.
     *
     * @return AnDomainEntitysetDefault
     */
    public function toEntitySet()
    {
        return KService::get($this->getRepository()->getEntitySet(), array('query' => clone $this, 'repository' => $this->getRepository()));
    }

    /**
     * Retun AnDomain operation based on the query operation.
     *
     * @return int
     */
    public function getOperation()
    {
        switch ($this->operation['type']) {
            case self::QUERY_UPDATE :
                return AnDomain::OPERATION_UPDATE;
            case self::QUERY_DELETE :
                return AnDomain::OPERATION_DELETE;
            default :
                return AnDomain::OPERATION_FETCH;
        }
    }

    /**
     * Return the query in a string format.
     *
     * @return string
     */
    final public function __toString()
    {
        try {
            $query = clone $this;
            //if the chain is not disabled then
            //allows the registered command chains
            //to modify the query based on its state
            if (!$query->disable_chain) {
                $chain = clone $this->getRepository()->getCommandChain();
                $chain->enqueue($query);
                $context = $this->getRepository()->getCommandContext();
                $context->caller = $this;
                $context->query = $query;
                switch ($this->operation['type']) {
                    case self::QUERY_UPDATE :
                       $command = 'update';break;
                    case self::QUERY_DELETE :
                       $command = 'delete';break;
                    default :
                       $command = 'select';break;
                }
                $chain->run('before.'.$command, $context);
                $context->result = $query->build();
                $chain->run('after.'.$command, $context);

                return $context->result;
            } else {
                return $query->build();
            }
        } catch (Exception $e) {
            trigger_error($e->getMessage());
        }
    }

    /**
     * Builds the query and return the string.
     *
     * @return string
     */
    public function build()
    {
        return AnDomainQueryBuilder::getInstance()->build($this);
    }

    /**
     * (non-PHPdoc).
     *
     * @see KCommandInterface::execute()
     */
    public function execute($command, KCommandContext $context)
    {
        $identifier = $context->caller->getIdentifier();
        $type = $identifier->path;
        $type = array_pop($type);
        $parts = explode('.', $command);
        $method = '_'.($parts[0]).ucfirst($type).ucfirst($parts[1]);
        if (method_exists($this, $method)) {
            return $this->$method($context);
        }
    }

    /**
     * Return an array of serializable properties.
     *
     * @return array
     */
    public function __sleep()
    {
        $vars = explode(' ', '__service_identifier _state link binds operation distinct columns from join where group having order limit offset _prefix');

        return $vars;
    }

    /**
     * The priority of the query.
     *
     * @return number
     */
    public function getPriority()
    {
        return -PHP_INT_MAX;
    }

    /**
     * Reset the query state if it's cloned.
     */
    public function __clone()
    {
        $this->_state = clone $this->_state;
    }
}
