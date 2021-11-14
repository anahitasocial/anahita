<?php

/**
 * Ownable Behavior. It feches an owner wherenever there's an oid.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComBaseControllerBehaviorOwnable extends AnControllerBehaviorAbstract
{
    /**
     * Default owner.
     *
     * @var mixed
     */
    protected $_default;

    /**
     * Identifiable key. If this key exists in the request then this behavior
     * will fetch the actor entity using this key.
     *
     * @return string
     */
    protected $_identifiable_key;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_default = isset($config['default']) ? $config['default'] : null;

        //set the default actor
        $this->setActor($this->_default);

        //set the identifiable key. By default its set to oid
        $this->_identifiable_key = $config->identifiable_key;

        //$this->_default ? $this->_default->id : null
        $this->getState()->insert($this->_identifiable_key);
    }

    /**
     * Initializes the default configuration for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional AnConfig object with configuration options.
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'identifiable_key' => 'oid',
            'default' => null,
            'priority' => AnCommand::PRIORITY_HIGHEST,
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler.
     *
     * @param   string      The command name
     * @param   object      The command context
     *
     * @return bool Can return both true or false.
     */
    public function execute($name, AnCommandContext $context)
    {
        $parts = explode('.', $name);

        if ($parts[0] == 'before') {
            $this->_fetchOwner($context);
        }

        parent::execute($name, $context);
    }

    /**
     * If the context->data actor is not already set them set the owner to the data
     * before controller add.
     *
     * @param AnCommandContext $context
     *
     * @return bool
     */
    protected function _beforeControllerAdd(AnCommandContext $context)
    {
        if (!$context->data['owner'] instanceof ComActorsDomainEntityActor) {
            if ($this->getRepository()->hasBehavior('ownable')) {
                $context->data['owner'] = $this->actor;
            }
        }
    }

    /**
     * Set the actor conect.
     *
     * @param ComActorsDomainEntiyActor $actor Set the actor context
     *
     * @return ComBaseControllerBehaviorOwnable
     */
    public function setActor($actor)
    {
        $this->_mixer->actor = $actor;

        return $this;
    }

    /**
     * Return the actor context.
     *
     * @return ComActorsDomainEntiyActor
     */
    public function getActor()
    {
        return $this->_mixer->actor;
    }

    /**
     * Fetches an entity.
     *
     * @param AnCommandContext $context
     *
     * @return ComActorsDomainEntityActor
     */
    protected function _fetchOwner(AnCommandContext $context)
    {
        $actor = pick($this->getActor(), $this->_default);
        $value = $this->{$this->getIdentifiableKey()};

        if ($value) {
            if ($value == 'viewer') {
                $actor = get_viewer();
            } elseif (!is_numeric($value)) {
                $actor = $this->getService('repos:people.person')->fetch(array('username' => $value));
            } else {
                $actor = $this->getService('repos:actors.actor')->fetch((int) $value);
            }

            //guest actor can never be a context actor
            if (is_person($actor) && $actor->guest()) {
                $actor = null;
            }

            //set the data owner to actor.
            $context->data['owner'] = $actor;

            if (!$actor) {
                throw new LibBaseControllerExceptionNotFound('Owner Not Found');
            }
        }

        $this->setActor($actor);
    }

    /**
     * Sets the identifiable key.
     *
     * @param string $key The identifiable key
     *
     * @return LibBaseControllerBehaviorIdentifiable
     */
    public function setIdentifiableKey($key)
    {
        $this->_identifiable_key = $key;
    }

    /**
     * Return the identifiable key.
     *
     * @return string
     */
    public function getIdentifiableKey()
    {
        return $this->_identifiable_key;
    }

    /**
     * Return the object handle.
     *
     * @return string
     */
    public function getHandle()
    {
        return AnMixinAbstract::getHandle();
    }
}
