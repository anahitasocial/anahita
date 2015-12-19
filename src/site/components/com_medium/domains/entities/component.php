<?php

/**
 * Medium component for.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComMediumDomainEntityComponent extends ComComponentsDomainEntityComponent
{
    /**
     * Story aggregation.
     *
     * @var array
     */
    protected $_story_aggregation;

    /**
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        $this->_story_aggregation = $config['story_aggregation'];
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
            'story_aggregation' => array(),
            'behaviors' => array(
                'assignable' => array(),
                'scopeable' => array(
                    'class' => 'ComMediumDomainEntityMedium',
                    'type' => 'post', ),
                'hashtagable' => array(
                    'class' => 'ComMediumDomainEntityMedium',
                    'type' => 'post',
                ),
                'geolocatable' => array(
                    'class' => 'ComMediumDomainEntityMedium',
                    'type' => 'post',
                )
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Return an array of permission object.
     *
     * @return array
     */
    public function getPermissions()
    {
        $registry = $this->getService('application.registry');
        $key = $this->getIdentifier().'-permissions';

        if (!$registry->offsetExists($key)) {
            $registry->offsetSet($key, self::_getDefaultPermissions($this));
        }

        return $registry->offsetGet($key);
    }

    /**
     * Called on when the stories are being aggregated.
     *
     * @param KEvent $event
     *
     * @return bool
     */
    public function onStoryAggregation(KEvent $event)
    {
        if (!empty($this->_story_aggregation)) {
            $event->aggregations->append(array(
                $this->component => $this->_story_aggregation,
            ));
        }
    }

    /**
     * On Setting display.
     *
     * @param KEvent $event The event parameter
     */
    public function onSettingDisplay(KEvent $event)
    {
        $actor = $event->actor;
        $tabs = $event->tabs;
        $this->_setSettingTabs($actor, $tabs);
    }

    /**
     * On Viewer Menu display.
     *
     * @param KEvent $event The event parameter
     */
    public function onMenuDisplay(KEvent $event)
    {
        $actor = $event->actor;
        $menuItems = $event->menuItems;

        if ($this->activeForActor($actor)) {
            $this->_setMenuLinks($actor, $menuItems);
        }
    }

    /**
     * On Dashboard event.
     *
     * @param KEvent $event The event parameter
     */
    public function onProfileDisplay(KEvent $event)
    {
        $actor = $event->actor;
        $gadgets = $event->gadgets;
        $composers = $event->composers;
        $this->_setGadgets($actor, $gadgets, 'profile');
        $this->_setComposers($actor, $composers, 'profile');
    }

    /**
     * On Dashboard event.
     *
     * @param KEvent $event The event parameter
     */
    public function onDashboardDisplay(KEvent $event)
    {
        $actor = $event->actor;
        $gadgets = $event->gadgets;
        $composers = $event->composers;
        $this->_setGadgets($actor, $gadgets, 'dashboard');

        if ($this->activeForActor($actor)) {
            $this->_setComposers($actor, $composers, 'dashboard');
        }
    }

    /**
     * Set the composers for a profile/dashboard. This method should be implemented by the subclasses.
     *
     * @param ComActorsDomainEntityActor     $actor     The actor that gadgets is rendering for
     * @param LibBaseTemplateObjectContainer $composers Gadet objects
     * @param string                         $mode      The mode. Can be profile or dashboard
     */
    protected function _setGadgets($actor, $gadgets, $mode)
    {
    }

    /**
     * Set the gadgets for a profile/dashboard. This method should be implemented by the subclasses.
     *
     * @param ComActorsDomainEntityActor     $actor     The actor that gadgets is rendering for
     * @param LibBaseTemplateObjectContainer $composers Gadet objects
     * @param string                         $mode      The mode. Can be profile or dashboard
     */
    protected function _setComposers($actor, $composers, $mode)
    {
    }

    /**
     * Set the gadgets for a profile/dashboard. This method should be implemented by the subclasses.
     *
     * @param ComActorsDomainEntityActor     $actor The actor that gadgets is rendering for
     * @param LibBaseTemplateObjectContainer $tabs  Tabs objects
     */
    protected function _setSettingTabs($actor, $tabs)
    {
    }

    /**
     * Set the links used to construct the viewer menu. This method should be implemented by the subclasses.
     *
     * @param ComActorsDomainEntityActor     $actor     The actor that gadgets is rendering for
     * @param LibBaseTemplateObjectContainer $menuItems menu item objects
     */
    protected function _setMenuLinks($actor, $menuItems)
    {
    }

    /**
     * Return an array of permissions by using the medium objects.
     *
     * @return array()
     */
    protected static function _getDefaultPermissions($component)
    {
        $identifiers = $component->getEntityIdentifiers('ComMediumDomainEntityMedium');

        $permissions = array();

        foreach ($identifiers as $identifier) {
            try {
                $repos = AnDomain::getRepository($identifier);

                if ($repos->entityInherits('ComMediumDomainEntityMedium')) {
                    $actions = array('add');
                    //if commentable then allow to set
                    //comment permissions
                    if ($repos->hasBehavior('commentable')) {
                        $actions[] = 'addcomment';
                    }

                    $permissions[(string) $identifier] = $actions;
                }
            } catch (Exception $e) {
            }
        }

        return $permissions;
    }
}
