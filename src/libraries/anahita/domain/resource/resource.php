<?php

/** 
 * LICENSE: ##LICENSE##.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Domain Resource.
 * 
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class AnDomainResource extends AnObject
{
    /**
     * Resource Table.
     * 
     * @var string
     */
    protected $_name;

    /**
     * Resource name.
     * 
     * @var string
     */
    protected $_alias;

    /**
     * Return an array of columns for the resource.
     * 
     * @var array
     */
    protected $_columns;

    /**
     * Link type. Can be strong or weak.
     * 
     * @var string
     */
    protected $_link_type;

    /**
     * Condition the connects this resources with another resource.
     * 
     * @var array
     */
    protected $_link;

    /**
     * A resource store.
     * 
     * @var AnDomainStoreInterface
     */
    protected $_store;

    /**
     * Constructor.
     *
     * @param 	object 	An optional AnConfig object with configuration options
     */
    public function __construct(AnConfig $config)
    {
        $this->_name = $config->name;
        $this->_store = $config->store;

        parent::__construct($config);

        $this->_alias = $config->alias;
        $this->_link = $config->link;
        $this->_columns = $config->columns;
        $this->_link_type = $config->link_type;
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
        $name = explode('_', $this->_name);

        $config->append(array(
            'link_type' => 'strong',
            'alias' => AnInflector::singularize($name[count($name) - 1]).'_tbl',
        ));

        parent::_initialize($config);
    }

    /**
     * Return link type.
     *
     * @return string
     */
    public function getLinkType()
    {
        return $this->_link_type;
    }

    /**
     * Return the resource links.
     * 
     * @return array
     */
    public function getLink()
    {
        return $this->_link;
    }

    /**
     * Return the resource alias.
     * 
     * @return string
     */
    public function getAlias()
    {
        return $this->_alias;
    }

    /**
     * Set the resource alias.
     * 
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->_alias = $alias;
    }

    /**
     * Return whether a resource has a column or not.
     * 	
     * @param string $name
     *
     * @return bool
     */
    public function hasColumn($name)
    {
        return isset($this->_columns[$name]);
    }

    /**
     * Return a column.
     * 	
     * @param string $name
     *
     * @return bool
     */
    public function getColumn($name)
    {
        $column = $this->_columns[$name];

        if ($column->resource !== $this) {
            $column = clone $column;
            $column->resource = $this;
            $this->_columns[$name] = $column;
        }

        return $this->_columns[$name];
    }

    /**
     * Return an array of columns for a table.
     * 
     * @see AnDomainResourceInterface::getColumns()
     *
     * @return array
     */
    public function getColumns()
    {
        $columns = array();

        foreach ($this->_columns as $name => $column) {
            $columns[$name] = $this->getColumn($name);
        }

        return $columns;
    }

    /**
     * Return the resource table name.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
}
