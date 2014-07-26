<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Hashtags
 * @subpackage Template_Helper
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2014 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

jimport('joomla.application.router');

/**
 * Hashtags Helper
 * 
 * Provides methods to for rendering hashtags in a hashtagable
 *
 * @category   Anahita
 * @package    Com_Actors
 * @subpackage Template_Helper
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComHashtagsTemplateHelper extends KTemplateHelperAbstract implements KServiceInstantiatable
{
	/**
     * Force creation of a singleton
     *
     * @param KConfigInterface 	$config    An optional KConfig object with configuration options
     * @param KServiceInterface	$container A KServiceInterface object
     *
     * @return KServiceInstantiatable
     */
    public static function getInstance(KConfigInterface $config, KServiceInterface $container)
    {
        if (!$container->has($config->service_identifier))
        {
            $classname = $config->service_identifier->classname;
            $instance  = new $classname($config);
            $container->set($config->service_identifier, $instance);
        }
    
        return $container->get($config->service_identifier);
    }
    
	/**
	 * Filter a value  
	 * 
	 * @param string The text to filter
	 * 
	 * @return string
	 */
	public function parse($text)
	{        
		$matches = array();
		
		$text = preg_replace(
			ComHashtagsDomainEntityHashtag::PATTERN_HASHTAG, 
			'<a class="hashtag" href="'.JRoute::_('option=com_hashtags&view=hashtag&alias=').'$2">$0</a>', 
			$text);
		
		return $text;
	}
}