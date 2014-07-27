<?php 

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Converts #hashtag terms to links
 * 
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class PlgContentfilterHashtag extends PlgContentfilterAbstract
{       
	/**
	 * Filter a value  
	 * 
	 * @param string The text to filter
	 * 
	 * @return string
	 */
	public function filter($text)
	{
		$this->_stripTags($text);
        
		$matches = array();
		
		$text = preg_replace(
			ComHashtagsDomainEntityHashtag::PATTERN_HASHTAG, 
			'<a class="hashtag" href="'.JRoute::_('option=com_hashtags&view=hashtag&alias=').'$2">$0</a>', 
			$text);
			
		$this->_replaceTags($text);	
		
		return $text;
	}
}