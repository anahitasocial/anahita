<?php
/**
* @version      $Id: style.php 4628 2012-05-06 19:56:43Z johanjanssens $
* @package      Koowa_Template
* @subpackage	Filter
* @copyright    Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
* @link 		http://www.nooku.org
*/

/**
 * Template filter to parse style tags
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package     Koowa_Template
 * @subpackage	Filter
 */
class KTemplateFilterStyle extends KTemplateFilterAbstract implements KTemplateFilterWrite
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
	 * Find any <style src"" /> or <style></style> elements and render them
	 *
	 * @param string Block of text to parse
	 * @return KTemplateFilterStyle
	 */
	public function write(&$text)
	{
		//Parse the script information
		$styles = $this->_parseStyles($text);

		//Prepend the script information
		$text = $styles.$text;

		return $this;
	}

	/**
	 * Parse the text for style tags
	 *
	 * @param 	string 	Block of text to parse
	 * @return 	string
	 */
	protected function _parseStyles(&$text)
	{
		$styles = '';

		$matches = array();
		if(preg_match_all('#<style\s*src="([^"]+)"(.*)\/>#iU', $text, $matches))
		{
			foreach(array_unique($matches[1]) as $key => $match)
			{
				$attribs = $this->_parseAttributes( $matches[2][$key]);
				$styles .= $this->_renderStyle($match, true, $attribs);
			}

			$text = str_replace($matches[0], '', $text);
		}

		$matches = array();
		if(preg_match_all('#<style(.*)>(.*)<\/style>#siU', $text, $matches))
		{
			foreach($matches[2] as $key => $match)
			{
				$attribs = $this->_parseAttributes( $matches[1][$key]);
				$styles .= $this->_renderStyle($match, false, $attribs);
			}

			$text = str_replace($matches[0], '', $text);
		}

		return $styles;
	}

	/**
	 * Render style information
	 *
	 * @param 	string	The style information
	 * @param 	boolean	True, if the style information is a URL
	 * @param 	array	Associative array of attributes
	 * @return string
	 */
	protected function _renderStyle($style, $link, $attribs = array())
	{
		$attribs = KHelperArray::toString($attribs);

		if(!$link)
		{
			$html  = '<style type="text/css" '.$attribs.'>'."\n";
			$html .= trim($style['data']);
			$html .= '</style>'."\n";
		}
		else $html = '<link type="text/css" rel="stylesheet" href="'.$style.'" '.$attribs.' />'."\n";

		return $html;
	}
}