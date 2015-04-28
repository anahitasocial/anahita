<?php

/** 
 * @category   Anahita
 * @package    Com_Pages
 * @subpackage Filter
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Post filter
 *
 * @category   Anahita
 * @package    Com_Pages
 * @subpackage Filter
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
class ComPagesFilterPost extends KFilterHtml
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
            'tag_list'   => array('p','strike', 'u', 'pre', 'address', 'blockquote', 'b', 'i', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5'),
            'tag_method' => 0
        )); 
	
	    parent::_initialize($config);
	    
	    if ( $config->tag_list )
	        $config['tag_list'] = KConfig::unbox($config->tag_list);
	    
	    if ( $config->tag_method )
	        $config['tag_method'] = KConfig::unbox($config->tag_method);	    
	}	

//end class
}