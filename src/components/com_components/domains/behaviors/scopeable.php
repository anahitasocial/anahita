<?php

/**
 * Scopeable behavior.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComComponentsDomainBehaviorScopeable extends LibBaseDomainBehaviorEnableable
{
    /**
     * Search Scope.
     *
     * @var array
     */
    protected $_scope_identifier = array();

    /**
     * Scope t.
     *
     * @var string
     */
    protected $_scope_type;

    /**
     * Constructor.
     *
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->_scope_identifier = $config->class;
        $this->_scope_type = $config->type;
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
            'type' => null,
            'class' => null,
        ));

        parent::_initialize($config);
    }

    /**
     * Cacthes the before get.
     *
     * @param AnEvent $event
     */
    public function onBeforeFetch(AnEvent $event)
    {
        $event->scope->append($this->_mixer->getScopes());
    }

    /**
     * Return the nodes scopes.
     *
     * @return array
     */
    public function getScopes()
    {
        $scopes = array();
        $repositories = $this->getEntityRepositories($this->_scope_identifier);

        foreach ($repositories as $repository) {
            $scopes[] = array('repository' => $repository,'type' => $this->_scope_type);
        }

        return $scopes;
    }
}
