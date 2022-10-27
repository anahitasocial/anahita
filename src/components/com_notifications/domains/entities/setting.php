<?php

/**
 * LICENSE: ##LICENSE##.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @version    SVN: $Id$
 *
 * @link       http://www.Anahita.io
 */

/**
 * Notification Setting Entity.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComNotificationsDomainEntitySetting extends ComBaseDomainEntityEdge
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
            'behaviors' => array(
                'dictionariable',
            ),
            'aliases' => array(
                'person' => 'nodeA',
                'actor' => 'nodeB',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Set a notification value for a notitification type.
     *
     * @param string $type       The type of the notification.
     * @param mixed  $value      The value of the notification setting.
     * @param bool   $send_email Boolean flag to whether email the notifications to the user or not
     *
     * @return ComNotificationsDomainEntitySetting
     */
    public function setValue($type, $value)
    {
        $filter = $this->getService('anahita:filter.cmd');
        $type = $filter->sanitize($type);
        $value = $filter->sanitize($value);

        settype($value, 'boolean');

        if (!in_range($value, 0, ComNotificationsConstant::NOTIFY)) {
            $value = ComNotificationsConstant::NOTIFY;
        }

        $this->__call('setValue', array($type, array('send_email' => $value)));

        return $this;
    }

    /**
     * Gets the value of the setting. If the notificaiton type has not been set
     * then NULL value is returned.
     *
     * @param string $type    The type of the notification
     * @param mixed  $default The default value to return If the type is NULL
     *
     * @return mixed The return value for the type.
     */
    public function getValue($type, $default = null)
    {
        $ret = $this->__call('getValue', array($type, $default));
        
        if (!isset($ret['value'])) {
            return $default;
        }

        return $ret['value'];
    }

    /**
     * Returns whether the setting should send an email for a notification or not for a
     * notification type.
     *
     * @param string $type    The type of the notification
     * @param mixed  $default The default value to return If the type is NULL
     *
     * @return bool The boolean flag to whether send an email or not
     */
    public function sendEmail($type, $default = false)
    {
        $ret = $this->__call('getValue', array($type));

        if (!isset($ret['send_email'])) {
            return (boolean) $default;
        }

        return (boolean) $ret['send_email'];
    }
}
