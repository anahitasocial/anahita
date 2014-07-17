<?php 

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Hashtage_Contentfilter
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Links hashtag terms to hashtag pages
 * 
 * @category   Anahita
 * @package    Hashtag_Contentfilter
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class PlgContentfilterHashtag extends PlgContentfilterAbstract
{
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     *
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'priority'   => KCommand::PRIORITY_LOWEST,
        ));
    
        parent::_initialize($config);
    }
        
	/**
	 * Filter a value  
	 * 
	 * @param string The text to filter
	 * 
	 * @return string
	 */
	public function filter($text)
	{        
		$matches = array();
		
		$text = preg_replace(
			ComHashtagsDomainEntityHashtag::PATTERN_HASHTAG, 
			'<a class="hashtag" href="'.JRoute::_('option=com_hashtags&view=hashtag&alias=').'$1">$0</a>', 
			$text);
		
		return $text;
	}
}