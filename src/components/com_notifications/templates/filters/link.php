<?php

/**
 * Fixes the styles of the links.
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
class ComNotificationsTemplateFilterLink extends LibBaseTemplateFilterAbstract implements LibBaseTemplateFilterWrite
{
    /**
     * Fixes blockquotes.
     *
     * @param string Block of text to parse
     *
     * @return LibBaseTemplateFilterLink
     */
    public function write(&$text)
    {
        $matches = array();

        if (preg_match_all('/<a(.*?)>/', $text, $matches)) {
            foreach ($matches[1] as $index => $match) {
                $attribs = $this->_parseAttributes($match);
                $attribs['style'] = 'color:#076da0;text-decoration:none';
                $attribs = KHelperArray::toString($attribs);
                $text = str_replace($matches[0][$index], '<a '.$attribs.' >', $text);
            }
        }

        return $this;
    }
}
