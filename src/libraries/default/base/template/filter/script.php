<?php
/**
 * @category   Anahita
 *
 * @author	   Johan Janssens <johan@nooku.org>
 * @author     Rastin Mehr <rastin@anahita.io>
 * @copyright  Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @copyright  Copyright (C) 2018 rmd Studio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.Anahita.io
 */
class LibBaseTemplateFilterScript extends LibBaseTemplateFilterAbstract implements LibBaseTemplateFilterWrite
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional AnConfig object with configuration options
     * @return void
     */
    protected function _initialize(AnConfig $config)
    {
        $config->append(array(
            'priority' => AnCommand::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }

    /**
     * Find any <script src="" /> or <script></script> elements and render them
     *
     * <script inline></script> can be used for inline scripts
     *
     * @param string Block of text to parse
     * @return LibBaseTemplateFilterLink
     */
    public function write(&$text)
    {
        //Parse the script information
        $scripts = $this->_parseScripts($text);

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
    protected function _parseScripts(&$text)
    {
        $scripts = '';

        $matches = array();
        // <script src="" />
        if (preg_match_all('#<script(?!\s+data\-inline\s*)\s+src="([^"]+)"(.*)/>#siU', $text, $matches)) {
            foreach (array_unique($matches[1]) as $key => $match) {
                $attribs = $this->_parseAttributes($matches[2][$key]);
                $scripts .= $this->_renderScript($match, true, $attribs);
            }

            $text = str_replace($matches[0], '', $text);
        }

        $matches = array();
        // <script></script>
        if (preg_match_all('#<script(?!\s+data\-inline\s*)(.*)>(.*)</script>#siU', $text, $matches)) {
            foreach ($matches[2] as $key => $match) {
                $attribs = $this->_parseAttributes($matches[1][$key]);
                $scripts .= $this->_renderScript($match, false, $attribs);
            }

            $text = str_replace($matches[0], '', $text);
        }

        return $scripts;
    }

    /**
     * Render script information
     *
     * @param string	The script information
     * @param boolean	True, if the script information is a URL.
     * @param array		Associative array of attributes
     * @return string
     */
    protected function _renderScript($script, $link, $attribs = array())
    {
        $attribs = AnHelperArray::toString($attribs);

        if (!$link) {
            $html  = '<script type="text/javascript" '.$attribs.'>'."\n";
            $html .= trim($script);
            $html .= '</script>'."\n";
        } else {
            $html = '<script type="text/javascript" src="'.$script.'" '.$attribs.'></script>'."\n";
        }

        return $html;
    }
}
