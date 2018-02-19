<?php

/**
 * Service Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseControllerService extends ComBaseControllerResource
{
    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //insert the search term query
        $this->_state->insert('q');
    }

    /**
     * Set the default Node View.
     *
     * @param KCommandContext $context Context parameter
     *
     * @return ComBaseControllerService
     */
    public function setView($view)
    {
        parent::setView($view);

        if (!$this->_view instanceof ComBaseViewAbstract) {
            $name = AnInflector::isPlural($this->view) ? 'nodes' : 'node';
            $defaults[] = 'ComBaseView'.ucfirst($view).ucfirst($this->_view->name);
            $defaults[] = 'ComBaseView'.ucfirst($name).ucfirst($this->_view->name);
            $defaults[] = 'ComBaseView'.ucfirst($this->_view->name);
            register_default(array('identifier' => $this->_view, 'default' => $defaults));
        }

        return $this;
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
        parent::_initialize($config);

        $config->append(array(
            'behaviors' => to_hash('serviceable'),
            'toolbars' => array($this->getIdentifier()->name, 'menubar', 'actorbar'),
            'request' => array(
                'limit' => 20,
                'start' => 0,
                'sort' => 'recent',
                'scope' => '',
            ),
        ));
    }

    /**
     * Generic POST action for a medium. If an entity exists then execute edit
     * else execute add.
     *
     * @param KCommandContext $context Context parameter
     */
    protected function _actionPost(KCommandContext $context)
    {
        $action = $this->getItem() ? 'edit' : 'add';
        $result = $this->execute($action, $context);

        return $result;
    }
}
