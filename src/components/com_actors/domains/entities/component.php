<?php

/**
 * Actor component.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComActorsDomainEntityComponent extends ComComponentsDomainEntityComponent
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
            'story_aggregation' => array(
                'cover_edit' => 'target',
                'avatar_edit' => 'target', ),
            'behaviors' => array(
                'scopeable' => array(
                    'class' => 'ComActorsDomainEntityActor',
                    'type' => 'actor', ),
                'hashtagable' => array(
                    'class' => 'ComActorsDomainEntityActor',
                    'type' => 'actor',
                ),
                'geolocatable' => array(
                    'class' => 'ComActorsDomainEntityActor',
                    'type' => 'actor',
                ),
            ),
        ));

        parent::_initialize($config);
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
}
