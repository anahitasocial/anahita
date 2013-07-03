<?php
/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

jimport('geshi.geshi');

/**
 * Syntax highlighter content filter 
 * 
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 * 
 * @uses       Uses GeSHi to highligh syntax based on the choosen language. 
 */
class PlgContentfilterSyntax extends PlgContentfilterAbstract
{
	/**
	 * GeSHi parser
	 * 
	 * @var GeSHi
	 */
	protected $_parser;
	
	/**
	 * Langauge aliases
	 * 
	 * @var arary
	 */
	protected $_lang_alias = array();
	
    /** 
     * Constructor.
     * 
     * @param mixed $dispatcher A dispatcher
     * @param array $config     An optional KConfig object with configuration options.
     * 
     * @return void
     */
	public function __construct($dispatcher = null,  $config = array())
	{
		$config = new KConfig($config);
		
		parent::__construct($dispatcher, $config);
		
		$this->_parser = new GeSHi();
		$this->_parser->enable_keyword_links(false);

		$this->_lang_alias = new KConfig(array('html'=>'html4strict'));
	}
		
	/**
	 * Return the GeSHi parser
	 * 
	 * @return GeSHi
	 */
	public function getParser()
	{
		return $this->_parser;
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
		
		if ( preg_match_all('#<code([^>]+)?>(.*?)</code>#si', $text, $matches) )
		{
			foreach($matches[0] as $key => $value)
			{				
				$lang    = array();
				if ( preg_match('/lang="(.*?)"/', $matches[1][$key], $lang) ) {
					$lang = $lang[1];					
				} else
					$lang = 'text';
				$lng   = $this->_lang_alias->get($lang, $lang);				
				$code  = html_entity_decode(trim($matches[2][$key]));
				$geshi = $this->_parser;	
				$geshi->set_language($lng);
				$geshi->set_source($code);
			
				$code  = '<div class="an-code-wrapper">';				
				//$code .= '<div><a data-trigger="ViewSource" data-viewsource-element="!div !+ div" class="btn small"><i class="icon-fullscreen"></i>&nbsp;</a></div>';
                $code .= '<div class="an-code"><div><a alt="'.JText::_('LIB-AN-ACTION-VIEW-SOURCE').'" data-trigger="ViewSource" data-viewsource-element="!div" class="pull-right btn" href="#"><i class="icon-fullscreen"></i></a>'.$geshi->parse_code().'</div></div>';
				$code .= '</div>';
				
				$text  = str_replace($matches[0][$key], $code, $text);				
			
			}
		}
		
		return $text;
	}

}