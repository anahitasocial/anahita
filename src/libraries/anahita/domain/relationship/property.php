<?php

/**
 * Abstract Relationship Property.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
abstract class AnDomainRelationshipProperty extends AnDomainPropertyAbstract
{
    /**
     * Parent Entity.
     *
     * @var KIdentfier
     */
    protected $_parent;

    /**
     * Parent Property in the Relationship.
     *
     * @var string
     */
    protected $_parent_key;

    /**
     * Child Entity.
     *
     * @var KIdentfier
     */
    protected $_child;

    /**
     * Query Filters. An array of key/value pairs that are applied to
     * the relationship query.
     *
     * @var array
     */
    protected $_query_filters;

    /**
     * Configurator.
     *
     * @param AnConfig $config Property Configuration
     */
    public function setConfig(AnConfig $config)
    {
        $identifier = $config->description->getRepository()->getIdentifier();

        if ($config->parent) {
            $this->_parent = AnService::getIdentifier($config->parent);

            //adopt the child application
            if (! $this->_parent->application) {
                $this->_parent->application = $identifier->application;
            }
        }

        parent::setConfig($config);

        if ($config->child) {
            if (strpos($config->child, '.') === false) {
                $identifier = clone $this->_parent;
                $identifier->name = $config->child;
                $config->child = $identifier;
            }
            
            $this->_child = AnService::getIdentifier($config->child);
            
            //adopt the parent application
            if (! $this->_child->application) {
                $this->_child->application = $identifier->application;
            }
        }

        $this->_parent_key = $config->parent_key;

        if (is_array($config->query)) {
            $config->query = new AnConfig($config->query);
        }

        $this->_query_filters = $config->query;
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'parent_key' => 'id',
            'query' => array(),
        ));

        parent::_initialize($config);
    }

    /**
     * Return the parent property in the relationship.
     *
     * @return string
     */
    public function getParentKey()
    {
        return $this->_parent_key;
    }

    /**
     * Returns the child property.
     *
     * @return AnDomainPropertyAbstract
     */
    public function getParentProperty()
    {
        return $this->getParentRepository()
            ->getDescription()
            ->getProperty($this->_parent_key);
    }

    /**
     * Return the parent entity identifier.
     *
     * @return string
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Return a clone of the query filters.
     *
     * @return array
     */
    public function getQueryFilters()
    {
        return clone $this->_query_filters;
    }

    /**
     * Return the child entity identifier.
     *
     * @return string
     */
    public function getChild()
    {
        return $this->_child;
    }

    /**
     * Return the entity repository.
     *
     * @return AnDomainRepositoryAbstract
     */
    public function getChildRepository()
    {
        return AnDomain::getRepository($this->_child);
    }

    /**
     * Return the entity repository.
     *
     * @return AnDomainRepositoryAbstract
     */
    public function getParentRepository()
    {
        return AnDomain::getRepository($this->_parent);
    }

    /**
     * Returns if is one-to-one relationship.
     *
     * @return bool
     */
    public function isOneToOne()
    {
        return $this instanceof AnDomainRelationshipOnetoone;
    }

    /**
     * Returns if is one-to-many relationship.
     *
     * @return bool
     */
    public function isOneToMany()
    {
        return $this instanceof AnDomainRelationshipOnetomany;
    }

    /**
     * Returns if is many-to-many relationship.
     *
     * @return bool
     */
    public function isManyToMany()
    {
        return $this instanceof AnDomainRelationshipManytomany;
    }

    /**
     * Returns if is many-to-one relationship.
     *
     * @return bool
     */
    public function isManyToOne()
    {
        return $this instanceof AnDomainRelationshipManytoone;
    }
}
