<?php

/**
 * One to many relationship.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnDomainRelationshipOnetomany extends AnDomainRelationshipProperty
{
    /**
     * The collection type identifier to use.
     *
     * @var string
     */
    protected $_entityset;

    /**
     * The cardinality of relationship.
     *
     * @var string|int
     */
    protected $_cardinality;

    /**
     * Delete rule for entities with one to many relationships.
     *
     * @var string can be cascade, nullify, deny
     */
    protected $_delete_rule;

    /**
     * Child Property in the Relationship.
     *
     * @var string
     */
    protected $_child_key;

    /**
     * Configurator.
     *
     * @param KConfig $config Property Configuration
     */
    public function setConfig(KConfig $config)
    {
        parent::setConfig($config);

        $this->_entityset = $config->entityset;
        $this->_cardinality = $config->cardinality;

        if (is_numeric($this->_cardinality)) {
            $this->_cardinality = (int) $this->_cardinality;
        }

        $this->_delete_rule = $config->parent_delete;
        $this->_child_key = $config->child_key;
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
        $child = clone $this->_parent;

        $child->name = AnInflector::singularize($config->name);

        $config->append(array(
            'entityset' => 'anahita:domain.entityset.onetomany',
            'cardinality' => 'many',
            'child_key' => $this->_parent->name,
            'parent_delete' => AnDomain::DELETE_CASCADE,
            'child' => $child,
        ));

        parent::_initialize($config);
    }

    /**
     * Set the delete rule.
     *
     * @return class instance
     */
    public function setDeleteRule($rule)
    {
        $this->_delete_rule = $rule;

        return $this;
    }

    /**
     * Return the delete rule for the child entities.
     *
     * @return int
     */
    public function getDeleteRule()
    {
        return $this->_delete_rule;
    }

    /**
     * Return cardinality.
     *
     * @return mixed
     */
    public function getCardinality()
    {
        return $this->_cardinality;
    }

    /**
     * Set the collection class KServiceIdentifier.
     *
     * @param KServiceIdentifier|string $collection
     *
     * @return class instance
     */
    public function setEntityset($entityset)
    {
        $this->_entityset = $entityset;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see AnDomainPropertyAbstract::isMaterializable()
     */
    public function isMaterializable(array $data)
    {
        return false;
    }

    /**
     * Materialize a relationship for the parent entity.
     *
     * @param AnDomainAbstract $entity
     * @param array            $data
     *
     * @return AnDomainCollectionAggregateAbstract
     */
    public function materialize(array $data, $entity)
    {
        //use a entity proxy to delay loading the related entity
        if ($this->_cardinality == 1) {
            $config = array();
            $config['relationship'] = $this;
            $config['value'] = $entity;
            $config['property'] = $this->_child_key;
            $config['service_identifier'] = $this->getChildRepository()->getDescription()->getEntityIdentifier();

            return new AnDomainEntityProxy(new KConfig($config));
        } else {
            return $this->getSet($entity);
        }
    }

    /**
     * Serialize an entity.
     *
     * @param AnDomainEntityAbstract $entity
     *
     * @return array
     */
    public function serialize($entity)
    {
        return array($this->getName().'.'.$this->_parent_key => $entity->get($this->_parent_key));
    }

    /**
     * Return the child property in the relationship.
     *
     * @return string
     */
    public function getChildKey()
    {
        return $this->_child_key;
    }

    /**
     * Returns the child property.
     *
     * @return AnDomainPropertyAbstract
     */
    public function getChildProperty()
    {
        return $this->getChildRepository()->getDescription()->getProperty($this->_child_key);
    }

    /**
     * Instantiate an aggregated entity set from a root object.
     *
     * @return AnDomainDecoratorManytomany
     */
    public function getSet($root)
    {
        $filters = $this->getQueryFilters();
        $filters['where'] = array($this->_child_key => $root);

        $options = array(
            'repository' => $this->getChildRepository(),
            'query' => $filters,
            'root' => $root,
            'property' => $this->_child_key,
        );

        $set = KService::get('anahita:domain.entityset.decorator.onetomany', $options);

        return $set;
    }
}
