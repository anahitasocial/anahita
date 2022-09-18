<?php
/**
 * Notification Setting Controller.
 *
 * @category   Anahita
 *
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2022 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComNotificationsControllerSetting extends ComBaseControllerResource
{
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
            'behaviors' => array('ownable'),
            'toolbars' => array('setting'),
        ));

        parent::_initialize($config);
    }

    protected function _actionGet(AnCommandContext $context)
    {
        $viewer = get_viewer();
        $muteEmail = (bool) get_config_value('notifications.mute_email');
        $setting = $this->getService('repos:notifications.setting')->find(array(
            'person' => $viewer,
            'actor' => $this->actor,
        ))->reset();

        $content = $this->getView()
        ->set('data', array(
            'email_muted_globally' => $muteEmail,
            'send_email' => $setting->sendEmail('posts', 1),
        ))
        ->display(); 
        
        $context->response->setContent($content);
    }

    /**
     * Sets a notification setting.
     *
     * @param AnCommandContext $context Context parameter
     */
    protected function _actionPost(AnCommandContext $context)
    {
        $data = $context->data;
        $viewer = get_viewer();
        $setting = $this->getService('repos:notifications.setting')->findOrAddNew(array(
            'person' => $viewer,
            'actor' => $this->actor,
        ));

        /*
        *   1: NOTIFY_WITH_EMAIL
        *   2: NOTIFY
        */
        // error_log($data->email);

        $setting->setValue('posts', null, $data->email);

        $setting->save();

        error_log($setting->getValue('posts'));
    }

    /**
     * Authorizes a get, only if the viewer is already following the owner.
     *
     * @return bool
     */
    public function canGet()
    {
        return $this->canPost();
    }

    /**
     * Authorizes a post, only if the viewer is already following the owner.
     *
     * @return bool
     */
    public function canPost()
    {
        $viewer = get_viewer();

        $actor = $this->actor;

        if (!$actor) {
            return false;
        }

        if ($viewer->eql($actor)) {
            return false;
        }

        if (!$actor->isFollowable()) {
            return false;
        }

        if (!$actor->isSubscribable()) {
            return false;
        }

        return $viewer->following($actor);
    }
}
