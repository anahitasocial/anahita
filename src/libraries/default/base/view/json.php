<?php

/**
 * JSON View Class.
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
class LibBaseViewJson extends LibBaseViewAbstract
{
    /**
     * The padding for JSONP.
     *
     * @var string
     */
    protected $_padding;

    /**
     * Constructor.
     *
     * @param   object  An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //Padding can explicitly be turned off by setting to FALSE
        if (empty($config->padding) && $config->padding !== false) {
            if (isset($this->callback) && (strlen($this->callback) > 0)) {
                $config->padding = $state->callback;
            }
        }

        $this->_padding = $config->padding;
    }

    /**
     * Initializes the config for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'padding' => '',
            'version' => '1.0',
        ))->append(array(
            'mimetype' => 'application/json; version='.$config->version,
        ));

        parent::_initialize($config);
    }

    /**
     * Return the views output.
     *
     *  @return string 	The output of the view
     */
    public function display()
    {
        $name = $this->getName();

        $data = array();

        //if data is set the just json encode those
        if (count($this->_data)) {
            $this->output = $this->_data;
        } elseif (AnInflector::isPlural($name)) {
            $this->output = $this->_getList();
        } else {
            $this->output = $this->_getItem();
        }

        //if null then return empty string
        $this->output = pick($this->output, '');

        if (!is_string($this->output)) {
            $this->output = json_encode($this->_toArray(KConfig::unbox($this->output)));
        }

        //Handle JSONP
        if (! empty($this->_padding)) {
            $this->output = $this->_padding.'('.$this->output.');';
        }

        return $this->output;
    }

    /**
     * Return the list.
     *
     * @return array
     */
    protected function _getList()
    {
        $data = array();

        if ($items = $this->_state->getList()) {
            $name = AnInflector::singularize($this->getName());

            foreach ($items as $item) {
                $this->_state->setItem($item);
                $name = null;
                $item = $this->_serializeToArray($item, $name);

                if (count($commands = $this->getToolbarCommands('list'))) {
                    $item['commands'] = $commands;
                }

                $data[] = $item;
            }

            $data = array(
                'data' => $data,
            );

            if (is($items, 'AnDomainEntitysetAbstract')) {
                $data['pagination'] = array(
                    'offset' => (int) $items->getOffset(),
                    'limit' => (int) $items->getLimit(),
                    'total' => (int) $items->getTotal(),
                );
            }
        }

        return $data;
    }

    /**
     * Return the list.
     *
     * @return array
     */
    protected function _getItem()
    {
        $item = null;

        if ($item = $this->_state->getItem()) {
            $item = $this->_serializeToArray($item);
            $commands = $this->getToolbarCommands('toolbar');

            if (!empty($commands)) {
                $item['commands'] = $commands;
            }
        }

        return $item;
    }

    /**
     * Serializes an item into an array.
     *
     * @return array
     */
    protected function _serializeToArray($item)
    {
        $result = array();

        if (is($item, 'AnDomainBehaviorSerializable')) {
            $result = $item->toSerializableArray();
        } else {
            $result = (array) $item;
        }

        return $result;
    }

    /**
     * Gets the toolbar commands. This method checks if the view state has
     * a toolbar.
     *
     * @param string $name The name of the commands
     *
     * @return array Return an array of commands
     */
    public function getToolbarCommands($name)
    {
        $result = array();

        if ($this->_state->toolbar instanceof AnControllerToolbarAbstract) {
            $this->_state->toolbar->reset();
            $method = 'add'.ucfirst($name).'Commands';

            if (method_exists($this->_state->toolbar, $method)) {
                $this->_state->toolbar->$method();
            }

            $commands = $this->_state->toolbar->getCommands();

            foreach ($commands as $command) {
                $result[] = $command->getname();
            }
        }

        return $result;
    }

    /**
     * Return an array representation of the data. It iteratates through the data if an element
     * implements toSerializableArray it will call it.
     *
     * @param array $data
     */
    protected function _toArray($data)
    {
        $array = array();

        foreach ($data as $key => $value) {
            if (is($value, 'AnDomainBehaviorSerializable')) {
                $array[$key] = $value->toSerializableArray();
            } elseif (is_array($value)) {
                $array[$key] = $this->_toArray($value);
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }
}
