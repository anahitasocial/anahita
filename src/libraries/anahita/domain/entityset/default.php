<?php

/**
 * A Queriable entityset. If no data is set then the queriable data
 * will wait until one of the iteration mehtod is called to load
 * the data.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnDomainEntitysetDefault extends AnDomainEntityset
{
    /**
     * The query that represents this entity set.
     *
     * @var AnDomainQuery
     */
    protected $_set_query;

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    protected function _initialize(KConfig $config)
    {
        parent::_initialize($config);
    }

    /**
     * Revert the query back to the original.
     *
     * @return AnDomainQuery
     */
    public function reset()
    {
        $this->_set_query = null;
        $this->_loaded = false;
        $this->_object_set = new ArrayObject();

        return $this;
    }

    /**
     * Return the entityset query. If $clone is passed it will return a clone instance of the entityset
     * query is returned.
     *
     * @param bool $clone         If set to true then it will return a new clone instance of entityset
     * @param bool $disable_chain Disable the chain
     *
     * @return AnDomainQuery
     */
    public function getQuery($clone = false, $disable_chain = false)
    {
        if (!isset($this->_set_query) || $clone) {
            if ($this->_query instanceof AnDomainQuery) {
                $query = clone $this->_query;
            } else {
                $query = $this->_repository->getQuery();
                AnDomainQueryHelper::applyFilters($query, $this->_query);
            }

            //if clone is set, then return the qury object
            if ($clone) {
                if ($disable_chain) {
                    $query->disableChain();
                }

                return $query;
            }
            //if not then set the entity query object
            $this->_set_query = $query;
        }

        return $this->_set_query;
    }

    /**
     * Return the total number of entities that match the entityset query.
     *
     * @return int
     */
    public function getTotal()
    {
        $query = clone $this->getQuery();
        $query->order = null;

        return $query->fetchValue('count(*)');
    }

    /**
     * Returns the set limit.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->getQuery()->limit;
    }

    /**
     * Returns the set offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->getQuery()->offset;
    }

    /**
     * If the missed method is implemented by the query object then delegate the call to the query object.
     *
     * @see KObject::__call()
     */
    public function __call($method, $arguments = array())
    {
        $parts = AnInflector::explode($method);

        if ($parts[0] == 'is' && isset($parts[1])) {
            $behavior = lcfirst(substr($method, 2));

            return $this->_repository->hasBehavior($behavior);
        }

        //forward a call to the query
        if (method_exists($this->getQuery(), $method) || !$this->_repository->entityMethodExists($method)) {
            $result = call_object_method($this->getQuery(), $method, $arguments);
            if ($result instanceof AnDomainQuery) {
                $result = $this;
            }
        } else {
            $result = parent::__call($method, $arguments);
        }

        return $result;
    }

    /**
     * Loads the data into the object set using the query if not already loaded.
     */
    protected function _getData()
    {
        $data = $this->getRepository()->fetch($this->getQuery(), AnDomain::FETCH_ENTITY_LIST);

        return $data;
    }
}
