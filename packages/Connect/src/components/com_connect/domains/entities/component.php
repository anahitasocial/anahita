<?php

/**
 * Connect Component.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComConnectDomainEntityComponent extends ComComponentsDomainEntityComponent
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
                'assignable' => array(),
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
        $services = ComConnectHelperApi::getServices();

        if (count($services)) {
            $tabs->insert('connect', array(
                'label' => AnTranslator::_('COM-CONNECT-PROFILE-EDIT'),
                'controller' => 'com://site/connect.controller.setting', ));
        }
    }

    /**
     * Authorizes echo.
     *
     * @param KCommandContext $context
     *
     * @return false
     */
    public function authorizeEcho(KCommandContext $context)
    {
        $actor = $context->actor;

        if ($actor->isAdministrable() && $actor->authorize('administration')) {
            return true;
        } else {
            return $actor->id == $context->viewer->id;
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
        ->getService('repos:connect.session')
        ->destroy(array('owner.id' => $event->actor_id));
    }
}
