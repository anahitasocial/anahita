<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 *
 * @link       http://www.Anahita.io
 */

/**
 * Notification Setting Controller.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
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

        $setting->setValue('posts', null, $data->email);

        $setting->save();
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
