<?php
/**
* @version      $Id: script.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Template
* @subpackage	Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Template filter to parse script tags
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Template
 * @subpackage	Filter
 */
class KTemplateFilterScript extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
	/**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   object  An optional KConfig object with configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority'   => KCommand::PRIORITY_LOW,
        ));

        parent::_initialize($config);
    }

	/**
	 * Find any <script src="" /> or <script></script> elements and render them
	 *
	 * <script inline></script> can be used for inline scripts
	 *
	 * @param string Block of text to parse
	 * @return KTemplateFilterLink
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
		if(preg_match_all('#<script(?!\s+data\-inline\s*)\s+src="([^"]+)"(.*)/>#siU', $text, $matches))
		{
			foreach(array_unique($matches[1]) as $key => $match)
			{
				$attribs = $this->_parseAttributes( $matches[2][$key]);
				$scripts .= $this->_renderScript($match, true, $attribs);
			}

			$text = str_replace($matches[0], '', $text);
		}

		$matches = array();
		// <script></script>
		if(preg_match_all('#<script(?!\s+data\-inline\s*)(.*)>(.*)</script>#siU', $text, $matches))
		{
			foreach($matches[2] as $key => $match)
			{
				$attribs = $this->_parseAttributes( $matches[1][$key]);
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
		$attribs = KHelperArray::toString($attribs);

		if(!$link)
		{
			$html  = '<script type="text/javascript" '.$attribs.'>'."\n";
			$html .= trim($script);
			$html .= '</script>'."\n";
		}
		else $html = '<script type="text/javascript" src="'.$script.'" '.$attribs.'></script>'."\n";

		return $html;
	}
}