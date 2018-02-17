<?php

/**
 * Represent a set of resources. The first resource is considered the main resource.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class AnDomainResourceSet extends KObject implements IteratorAggregate, Countable
{
    /**
     * Resources.
     *
     * @var array
     */
    protected $_resources;

    /**
     * Space Store.
     *
     * @var AnDomainStoreInterface
     */
    protected $_store;

    /**
     * Links.
     *
     * @var array
     */
    protected $_links;

    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct(null);

        $this->_store = $config->store;

        foreach ($config->resources as $resource) {
            $this->insert($resource);
        }
    }

    /**
     * Adds a resource to the set the set of resources.
     *
     * @param string|array $config
     */
    public function insert($config)
    {
        if (is_string($config)) {
            $config = array('name' => $config);
        }

        $config = new KConfig($config);

        if (empty($config->link) && !empty($this->_resources)) {
            $name = AnInflector::singularize($this->main()->getName());
            $config->link = array('child' => $name.'_id','parent' => 'id');
        }

        $config->append(array(
            'columns' => $this->_store->getColumns($config->name),
        ));

        $resource = new AnDomainResource($config);
        $this->_resources[$resource->getAlias()] = $resource;

        return $this;
    }

    /**
     * Return the resource with name.
     *
     * @param string $name The name of the resource
     *
     * @return AnDomainResource
     */
    public function getResource($name)
    {
        foreach ($this->_resources as $resource) {
            if ($resource->getName() == $name) {
                return $resource;
            }
        }
    }

    /**
     * Return the main resource.
     *
     * @return AnDomainResourceInterface
     */
    public function main()
    {
        $resources = array_values($this->_resources);

        return $resources[0];
    }

    /**
     * Return an array of key/value pair that connects two reosurcs
     * together.
     *
     * @return array
     */
    public function getLinks()
    {
        if (!isset($this->_links)) {
            $this->_links = array();

            foreach ($this->_resources as $resource) {
                $link = $resource->getLink();

                if ($link) {
                    $this->_links[] = new KConfig(array(
                        'child' => $resource->getColumn($link->child),
                        'parent' => $this->main()->getColumn($link->parent),
                        'resource' => $resource,
                    ));
                }
            }
        }

        return $this->_links;
    }

    /**
     * Return a column in a resource using.
     *
     * @param string $name
     *
     * @return AnDomainResourceColumn
     */
    public function getColumn($name)
    {
        if ($name instanceof AnDomainResourceColumn) {
            return $name;
        }

        if (strpos($name, '.') !== false) {
            $parts = explode('.', $name, 2);
            $name = $parts[1];
            foreach ($this->_resources as $resource) {
                if ($resource->getAlias() == $parts[0]) {
                    break;
                }
            }
            $resources = array($resource);
        } else {
            $resources = $this->_resources;
        }

        foreach ($resources as $resource) {
            if ($resource->hasColumn($name)) {
                return $resource->getColumn($name);
            }
        }

        //throw new KException('Column '.$name.' doesn\'t exists');
    }

    /**
     * Return the count of the resources.
     *
     * @return int
     */
    public function count()
    {
        return count($this->_resources);
    }

    /**
     * Return an iterator.
     *
     * @return Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_resources);
    }
}
