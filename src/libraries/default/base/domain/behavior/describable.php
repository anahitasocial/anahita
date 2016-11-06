<?php

/**
 * Decribable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseDomainBehaviorDescribable extends AnDomainBehaviorAbstract
{
    /**
     * An array of properties that can be searched.
     *
     * @var array
     */
    protected $_searchable_properties = array();

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_searchable_properties = $config->searchable_properties;
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
            'searchable_properties' => array('name', 'body'),
            'attributes' => array(
                'name' => array('format' => 'string'),
                'body' => array('format' => 'string'),
                'alias' => array('format' => 'slug'),
            ),
            'aliases' => array(
                'title' => 'name',
                'description' => 'body',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Override the name setter to set the alias at the same time.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->set('name', $name);
        $this->alias = $name;
    }

    /**
     * Returns the node URL.
     *
     * @return string
     */
    public function getURL()
    {
        if (!isset($this->_mixer->_url)) {
            $this->_mixer->_url = 'option='.$this->component.'&view='.$this->_mixer->getIdentifier()->name;

            if ($this->_mixer->id) {
                $this->_mixer->_url .= '&id='.$this->_mixer->id;
            }

            if ($this->alias) {
                $this->_mixer->_url .= '&alias='.strtolower($this->alias);
            }
        }

        return $this->_mixer->_url;
    }

    /**
     * If a query keyowrd is set it will incorporate it in the search.
     *
     * @param KCommandContext $context
     */
    protected function _beforeRepositoryFetch(KCommandContext $context)
    {
        $query = $context->query;

        if ($query->keyword) {
            $keywords = $query->keyword;

            if ($keywords) {
                if (strpos($keywords, ' OR ')) {
                    $keywords = explode(' OR ', $keywords);
                    $operation = 'OR';
                } else {
                    $keywords = explode(' ', $keywords);
                    $operation = 'AND';
                }

                if (!empty($operation)) {
                    $clause = $query->clause();
                    $search_column = array();

                    foreach ($this->_searchable_properties as $property) {
                        $search_column[] = "IF(@col($property) IS NULL,\"\",@col($property))";
                    }

                    $search_column = implode($search_column, ',');

                    foreach ($keywords as $keyword) {
                        $clause->where('CONCAT('.$search_column.') LIKE @quote(%'.$keyword.'%)', $operation);
                    }
                }
            }
        }
    }
}
