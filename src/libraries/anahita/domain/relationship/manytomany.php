<?php

/**
 * Many to many relationship.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnDomainRelationshipManytomany extends AnDomainRelationshipOnetomany
{
    /**
     * Target identifier.
     *
     * @var string
     */
    protected $_target;

    /**
     * Target key in the link entity.
     *
     * @var string
     */
    protected $_target_child_key;

    /**
     * Target key in the link entity.
     *
     * @var string
     */
    protected $_target_parent_key;

    /**
     * Junction Alias.
     *
     * @var string
     */
    protected $_junction_alias;

    /**
     * Configurator.
     *
     * @param KConfig $config Property Configuration
     */
    public function setConfig(KConfig $config)
    {
        $identifier = $config->description->getRepository()->getIdentifier();

        $config->child = $config->through;

        parent::setConfig($config);

        $this->_target = KService::getIdentifier($config->target);

        if (!$this->_target->application) {
            $this->_target->application = $identifier->application;
        }

        $this->_target_child_key = $config->target_child_key;

        $this->_target_parent_key = $config->target_parent_key;

        //set the junction alias (the connecting table alias)
        $this->_junction_alias = $config->as;
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
        $identifier = clone $this->_parent;
        $identifier->name = AnInflector::singularize($this->_name);
        $config->append(array(
            'entityset' => 'anahita:domain.entityset.manytomany',
            'target' => $identifier,
            'target_child_key' => AnInflector::variablize($identifier->name),
            'target_parent_key' => 'id',
        ));

        if (!$config->as) {
            //keep the as always the same for the two many to many relationship
            $names = array($config->parent->name, AnInflector::singularize($config->name));
            sort($names);
            $config->as = $names[0].ucfirst(AnInflector::pluralize($names[1]));
        }

        parent::_initialize($config);
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
        return $this->getSet($entity);
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
        return array($this->getName().'.'.$this->_target_parent_key => $entity->get($this->_target_parent_key));
    }

    /**
     * Return the target repository.
     *
     * @return AnDomainRepositoryAbstract
     */
    public function getTargetRepository()
    {
        return AnDomain::getRepository($this->_target);
    }

    /**
     * Returns the child property.
     *
     * @return AnDomainPropertyAbstract
     */
    public function getTargetChildProperty()
    {
        return $this->getChildRepository()->getDescription()->getProperty($this->_target_child_key);
    }

    /**
     * Returns the target parent property.
     *
     * @return AnDomainPropertyAbstract
     */
    public function getTargetParentProperty()
    {
        $this->getTargetRepository()->getDescription()->getProperty($this->_target_parent_key);
    }

    /**
     * Returns the child property.
     *
     * @return AnDomainPropertyAbstract
     */
    public function getTargetChildKey()
    {
        return $this->_target_child_key;
    }

    /**
     * Return the target identifier.
     *
     * @return KServiceIdentifier
     */
    public function getTarget()
    {
        return $this->_target;
    }

    /**
     * Returns the parent property.
     *
     * @return AnDomainPropertyAbstract
     */
    public function getTargetParentKey()
    {
        return $this->_target_parent_key;
    }

    /**
     * Return an alias for the junction model.
     *
     * @return string
     */
    public function getJunctionAlias()
    {
        return $this->_junction_alias;
    }

    /**
     * Instantiate an aggregated entity set from a root object.
     *
     * @return AnDomainDecoratorManytomany
     */
    public function getSet($root)
    {
        $child = ucfirst(AnInflector::pluralize($this->getChild()->name));
        $parent = $this->getJunctionAlias().'.'.$this->_child_key;
        $filters = $this->getQueryFilters();
        $filters['where'] = array($parent => $root);
        $options = array(
            'repository' => $this->getTargetRepository(),
            'query' => $filters,
            'root' => $root,
            'property' => $this->getChildKey(),
            'target_property' => $this->getTargetChildKey(),
            'child' => $this->getChildRepository(),
        );

        $set = KService::get('anahita:domain.entityset.decorator.manytomany', $options);

        return $set;
    }
}
