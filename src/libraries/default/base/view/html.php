<?php

/**
 * HTML View Class.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseViewHtml extends LibBaseViewTemplate
{
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
            'template_filters' => array('script', 'style'),
            'mimetype' => 'text/html',
        ));

        parent::_initialize($config);
    }

    /**
     * Set a view properties.
     *
     * @param   string  The property name.
     * @param   mixed   The property value.
     */
    public function __set($property, $value)
    {
        $name = $this->getName();

        if ($property == 'item' || $property == 'items') {
            $name = $property;
        }

        if ($property == $name) {
            if (AnInflector::isPlural($name)) {
                $this->_state->setList($value);
            } else {
                $this->_state->setItem($value);
            }
        }

        return parent::__set($property, $value);
    }

    /**
     * Sets the.
     *
     * @return string
     */
    public function display()
    {
        //Get the view name
        $name = $this->getName();

        //set the state data to the view
        $this->_data = array_merge($this->_state->toArray(), $this->_data);

        //Assign the data of the model to the view
        if ($items = $this->_state->getList()) {
            $this->_data[ AnInflector::pluralize($name) ] = $items;
            $this->_data['items'] = $items;
        }

        if ($item = $this->_state->getItem()) {
            $this->_data[ AnInflector::singularize($name) ] = $item;
            $this->_data['item'] = $item;
        }

        return parent::display();
    }
}
