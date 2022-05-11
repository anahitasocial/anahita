<?php

/**
 * Notification text template helper class.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class ComNotificationsTemplateHelperText extends LibBaseTemplateHelperText
{
    /**
     * Truncates a text.
     *
     * @param string $text    The text to truncate
     * @param array  $options Truncation options. Can be 'length'=>integer, 'read_more'=>boolean,
     *                        'ending'=>string, 'exact'=>boolean, 'consider_html'=>false
     *
     * @return string
     */
    public function truncate($text, $options = array())
    {
        $options['read_more'] = false;
        $options['length'] = 500;

        return parent::truncate($text, $options);
    }
}
