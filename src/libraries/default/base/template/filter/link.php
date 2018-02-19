<?php
/**
 * @category   Anahita
 *
 * @author	   Johan Janssens <johan@nooku.org>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright  Copyright (C) 2018 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class LibBaseTemplateFilterLink extends LibBaseTemplateFilterAbstract implements LibBaseTemplateFilterWrite
{
    /**
     * Find any <link /> elements and render them
     *
     * @param string Block of text to parse
     * @return LibBaseTemplateFilterLink
     */
    public function write(&$text)
    {
        //Parse the script information
        $scripts = $this->_parseLinks($text);

        //Prepend the script information
        $text = $scripts.$text;

        return $this;
    }

    /**
     * Parse the text for script tags
     *
     * @param string Block of text to parse
     * @return string
     */
    protected function _parseLinks(&$text)
    {
        $scripts = '';

        $matches = array();
        if (preg_match_all('#<link\ href="([^"]+)"(.*)\/>#iU', $text, $matches)) {
            foreach (array_unique($matches[1]) as $key => $match) {
                $attribs = $this->_parseAttributes($matches[2][$key]);
                $scripts .= $this->_renderScript($match, $attribs);
            }

            $text = str_replace($matches[0], '', $text);
        }

        return $scripts;
    }

    /**
     * Render script information
     *
     * @param string	The script information
     * @param array		Associative array of attributes
     * @return string
     */
    protected function _renderLink($link, $attribs = array())
    {
        $attribs = KHelperArray::toString($attribs);

        $html = '<link href="'.$link.'" '.$attribs.'/>'."\n";
        return $html;
    }
}
