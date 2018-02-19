<?php

/**
 * Fixes the blockquotes in the body.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComNotificationsTemplateFilterBlockquote extends LibBaseTemplateFilterAbstract implements LibBaseTemplateFilterRead
{
    /**
     * Fixes blockquotes.
     *
     * @param string Block of text to parse
     *
     * @return LibBaseTemplateFilterLink
     */
    public function read(&$text)
    {
        $text = preg_replace('%<blockquote>(.*?)</blockquote>%', '<blockquote style="margin:0px">\1</blockquote>', $text);

        return $this;
    }
}
