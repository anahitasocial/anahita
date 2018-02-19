<?php

/**
 * Notification text template helper class.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 *
 * @link       http://www.GetAnahita.com
 */
class ComNotificationsTemplateHelperNotifications extends LibBaseTemplateHelperAbstract
{
    /**
     * Group a set of notifications by date.
     *
     * @param array $notifications
     *
     * @return array
     */
    public function group($notifications)
    {
        $dates = array();
        $actor = $this->getTemplate()->getView()->actor;
        $timezone = pick($actor->timezone, 0);
        foreach ($notifications as $notification) {
            $current = AnDomainAttributeDate::getInstance()->addHours($timezone);
            $diff = $current->compare($notification->creationTime->addHours($timezone));

            if ($diff <= AnHelperDate::dayToSeconds('1')) {
                if ($current->day ==  $notification->creationTime->day) {
                    $key = AnTranslator::_('LIB-AN-DATE-TODAY');
                } else {
                    $key = AnTranslator::_('LIB-AN-DATE-DAY');
                }
            } else {
                $key = $this->getTemplate()->renderHelper('date.format', $notification->creationTime, array('format' => '%B %d'));
            }

            if (!isset($dates[$key])) {
                $dates[$key] = array();
            }

            $dates[$key][] = $notification;
        }

        return $dates;
    }
}
