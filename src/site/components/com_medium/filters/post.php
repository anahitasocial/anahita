<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Post filter
 *
 * @category   Anahita
 * @package    Com_Medium
 * @subpackage Filter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComMediumFilterPost extends KFilterHtml
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
	        'tag_list'    => array('img', 'a', 'blockquote', 'strong', 'em', 'ul', 'ol', 'li', 'code'),
	        'tag_method'  => 0
	    ));
	
	    parent::_initialize($config);
	    
	    if ( $config->tag_list )
	        $config['tag_list'] = KConfig::unbox($config->tag_list);
	    
	    if ( $config->tag_method )
	        $config['tag_method'] = KConfig::unbox($config->tag_method);	    
	}
		
	/**
	 * Try to convert to plaintext
	 *
	 * @param string $source The source to convert 
	 * 
	 * @return string Plaintext string
	 */
	protected function _decode($source)
	{
		$matches = array();
		
		if ( preg_match_all('#<code(.*?)>(.*?)</code>#si', $source, $matches) )
		{
			$replacements = array_map('htmlentities', $matches[2]);
			
			foreach($replacements as $key => &$match)
			{
				$match = '<code'.$matches[1][$key].'>'.$match.'</code>';				
			}
			
			$source = str_replace($matches[0], $replacements, $source);
							
		}		
		return $source;
		
		// entity decode
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		foreach($trans_tbl as $k => $v) {
			$ttr[$v] = utf8_encode($k);
		}
		$source = strtr($source, $ttr);
		
		// convert decimal
		$source = preg_replace('/&#(\d+);/me', "chr(\\1)", $source); // decimal notation
		
		// convert hex
		$source = preg_replace('/&#x([a-f0-9]+);/mei', "chr(0x\\1)", $source); // hex notation
		return $source;
	}	

//end class
}