<?php

/**
 * Scope Object.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComComponentsDomainEntityScope extends AnObject
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
     * A flag whether to scope is hashtaggable
     *
     * @var bool
     */
    public $hashtaggable;

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
     * @param AnConfig $config An optional AnConfig object with configuration options.
     */
    public function __construct(AnConfig $config)
    {
        parent::__construct($config);

        $this->identifier   = $config->identifier;
        $this->node_type    = $config->node_type;
        $this->commentable  = $config->commentable;
        $this->type         = $config->type;
        $this->ownable      = $config->ownable;
        $this->hashtaggable  = $config->hashtaggable;
        $this->geolocatable = $config->geolocatable;
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
        if ($config->repository) {
            $config->append(array(
                'identifier' => $config->repository->getDescription()->getInheritanceColumnValue()->getIdentifier(),
                'node_type' => (string) $config->repository->getDescription()->getInheritanceColumnValue(),
                'commentable' => $config->repository->isCommentable(),
                'ownable' => $config->repository->isOwnable(),
                'hashtaggable' => $config->repository->isHashtaggable(),
                'geolocatable' => $config->repository->isGeolocatable()
            ));
        }

        parent::_initialize($config);
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
