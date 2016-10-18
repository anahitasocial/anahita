<?php

/**
 * Scope Object.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComComponentsDomainEntityScope extends KObject
{
    /**
     * The entity type
     *
     * @var string
     */
    public $node_type;

    /**
     * Scope type. Can be posts, actors or others
     *
     * @var string
     */
    public $type;

    /**
     * The entity identifier
     *
     * @var KIdentifier
     */
    public $identifier;

    /**
     * A flag whether to scope is commetnable
     *
     * @var bool
     */
    public $commentable;

    /**
     * A flag whether to scope is ownable
     *
     * @var bool
     */
    public $ownable;

    /**
     * A flag whether to scope is hashtagable
     *
     * @var bool
     */
    public $hashtagable;

    /**
     * A flag whether to scope is geolocatable
     *
     * @var bool
     */
    public $geolocatable;

    /**
     * Returns how many result count there are per scope.
     *
     * @var int
     */
    public $result_count;

    /**
     * Constructor.
     *
     * If a repository is passed, the scope can guess some of the values
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->identifier   = $config->identifier;
        $this->node_type    = $config->node_type;
        $this->commentable  = $config->commentable;
        $this->type         = $config->type;
        $this->ownable      = $config->ownable;
        $this->hashtagable  = $config->hashtagable;
        $this->geolocatable = $config->geolocatable;

        $this->getService('anahita:language')->load('com_'.$this->identifier->package);
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
        if ($config->repository) {
            $config->append(array(
                'identifier' => $config->repository->getDescription()->getInheritanceColumnValue()->getIdentifier(),
                'node_type' => (string) $config->repository->getDescription()->getInheritanceColumnValue(),
                'commentable' => $config->repository->isCommentable(),
                'ownable' => $config->repository->isOwnable(),
                'hashtagable' => $config->repository->isHashtagable(),
                'geolocatable' => $config->repository->isGeolocatable()
            ));
        }

        parent::_initialize($config);
    }

    /**
     * wakes up.
     */
    public function __wakeup()
    {
        $this->getService('anahita:language')->load('com_'.$this->identifier->package);
    }

    /**
     * The package and name portion of the identifier concatinated together using a dot.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->identifier->package.'.'.$this->identifier->name;
    }
}
