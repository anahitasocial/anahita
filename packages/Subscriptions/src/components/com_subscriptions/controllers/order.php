<?php

/**
 * Package Controller.
 *
 * @category		Controller
 */
class ComSubscriptionsControllerOrder extends ComBaseControllerService
{
    /**
     * Constructor.
     *
     * @param   object  An optional AnConfig object with configuration options
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);
        $this->registerCallback('before.get', array($this, 'fetchActor'));
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'ownable',
                'serviceable' => array(
                    'read_only' => true
                    ),
                ),
        ));

        parent::_initialize($config);
    }

    /**
     * Service Browse.
     *
     * @param AnCommandContext $context
     */
    protected function _actionBrowse($context)
    {
        $entities = parent::_actionBrowse($context);

        if (isset($this->actor->id)) {
            $entities->actorId($this->actor->id);
        }

        return $entities;
    }

    /**
     *  Sets controller's actor if a single entity is loaded.
     */
    public function fetchActor()
    {
        if ($entity = $this->getItem()) {
            $id = $entity->actorId;
            $actor = $this->getService('repos:people.person')->find(array('id' => $id));
            $this->actor = $actor;
        }
    }
}
