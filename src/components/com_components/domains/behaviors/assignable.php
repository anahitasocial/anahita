<?php

/**
 * Assignable Behavior.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComComponentsDomainBehaviorAssignable extends LibBaseDomainBehaviorEnableable
{
    /**
     * Assignment option.
     *
     * @var int
     */
    protected $_assignment_option;

    /*
     * can only assigned to these actors
     *
     */
    protected $_actor_identifiers;

    /**
     * Profile features.
     *
     * @var int
     */
    protected $_profile_features;

    /**
     * Description of the component when enabled on a profile.
     *
     * @var string
     */
    protected $_profile_description;

    /**
     * Name of the component when enabled on a profile.
     *
     * @var string
     */
    protected $_profile_name;

    /**
     * Assign Options.
     */
    const OPTION_OPTIONAL = 1; //will result into assignments always/optional/never
    const OPTION_NOT_OPTIONAL = 2; //will result into assignment always/never

    /**
     * Access.
     */
    const ACCESS_OPTIONAL = 0;
    const ACCESS_ALWAYS = 1;
    const ACCESS_NEVER = 2;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_assignment_option = $config->assignment_option;
        $this->_actor_identifiers = $config->actor_identifiers;
        $this->_profile_features = $config->profile_features;
        $this->_profile_name = $config->profile_name;
        $this->_profile_description = $config->profile_description;
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
            'profile_name' => ucfirst($this->getIdentifier()->package),
            'profile_description' => AnTranslator::_('COM-'.$this->getIdentifier()->package.'-APP-DESCRIPTION'),
            'profile_features' => array(),
            'assignment_option' => self::OPTION_OPTIONAL,
            'actor_identifiers' => array(),
            'relationships' => array(
                'assignments' => array(
                   'child' => 'com:components.domain.entity.assignment',
                   'child_key' => 'componentEntity',
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Set the assignment for an identifier.
     */
    public function setAssignmentForIdentifier($identifier, $access = null)
    {
        if (is_array($identifier)) {
            foreach ($identifier as $identifier => $access) {
                $this->setAssignmentForIdentifier($identifier, $access);
            }
        } else {
            //not a real identifier just the
            //the name part.
            //we need to infer the whole identifier
            //com:[pluralized name].domain.entity.[name]

            if (strpos($identifier, '.') === false) {
                $identifier = 'com:'.AnInflector::pluralize($identifier).'.domain.entity.'.$identifier;
            }

            $assignment = $this->assignments->findOrAddNew(array('actortype' => $identifier));

            $assignment->access = $access;
        }
    }

    /**
     * Return the assignment option.
     *
     * @return int
     */
    public function getAssignmentForIdentifier($identifier)
    {
        $assignment = $this->assignments->find(array('actortype' => (string) $identifier));
        $access = ($assignment) ? $assignment->access : self::ACCESS_NEVER;

        return (int) $access;
    }

    /**
     * Return whether the component is active for actor. A component is active, if
     * it has been assigned as awlays for the actor type or it has been enabled for the
     * actor.
     *
     * @param ComActorsDomainEntityActor $actor The actor object
     *
     * @return bool
     */
    public function activeForActor($actor)
    {
        $actortype = $actor->getEntityDescription()->getInheritanceColumnValue()->getIdentifier();

        $access = $this->getAssignmentForIdentifier($actortype);

        $return = false;

        if ($access == self::ACCESS_ALWAYS) {
            $return = true;
        } elseif ($access == self::ACCESS_OPTIONAL) {
            $return = $this->enabledForActor($actor);
        }

        return $return;
    }

    /**
     * Return whether the component is enabled for actor.
     *
     * @param ComActorsDomainEntityActor $actor The actor object
     *
     * @return bool
     */
    public function enabledForActor($actor)
    {
        return !is_null($this->assignments->find(array('actor' => $actor)));
    }

    /**
     * Set the assignment option.
     *
     * @param int $option
     */
    public function setAssignmentOption($option)
    {
        $this->_assignment_option = $option;
    }

    /**
     * Return the assignment option.
     *
     * @return int
     */
    public function getAssignmentOption()
    {
        return $this->_assignment_option;
    }

     /**
      * Return an array of allowed actors for this component to associate with.
      *
      * @return array
      */
     public function getActorIdentifiers()
     {
         return $this->_actor_identifiers;
     }

    /**
     * Name of the app when assigned to a profile.
     *
     * @return string
     */
    public function getProfileName()
    {
        return $this->_profile_name;
    }

    /**
     * Retunr an empty array.
     *
     * @return array
     */
    public function getPermissions()
    {
        return array();
    }

    /**
     * Description of the app when assigned to a profile.
     *
     * @return string
     */
    public function getProfileDescription()
    {
        return $this->_profile_description;
    }

    /**
     * Return an array of features that would be available when enabled for a profile.
     *
     * @return array
     */
    public function getProfileFeatures()
    {
        return $this->_profile_features;
    }
}
