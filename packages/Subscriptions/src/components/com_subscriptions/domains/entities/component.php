<?php

/**
 * Connect Component.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComSubscriptionsDomainEntityComponent extends ComComponentsDomainEntityComponent
{
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
            'behaviors' => array(
                'assignable' => array(
                    'assignment_option' => ComComponentsDomainBehaviorAssignable::OPTION_NOT_OPTIONAL,
                    'actor_identifiers' => array('com:people.domain.entity.person'),
                ),
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * {@inheritdoc}
     */
    public function onSettingDisplay(KEvent $event)
    {
        $actor = $event->actor;
        $tabs = $event->tabs;

        if (!$actor->admin()) {
            $tabs->insert('subscription', array(
                'label' => AnTranslator::_('COM-SUBSCRIPTIONS-PROFILE-EDIT'),
                'controller' => 'com://site/subscriptions.controller.setting',
            ));
        }
    }

    /**
     * On Destroy Nodes.
     *
     * @param KEvent $event
     */
    public function onDeleteActor(KEvent $event)
    {
        $this
        ->getService('repos:subscriptions.order')
        ->destroy(array('transaction_tbl.actor_id' => $event->actor_id));
    }
}
