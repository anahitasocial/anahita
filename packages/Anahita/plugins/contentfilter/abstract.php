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

/**
 * Abstract Contentfilter Command. 
 *
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class PlgContentfilterAbstract extends KCommand implements PlgContentfilterInterface
{
	/**
	 * Array of stripped tags
	 * 
	 * @var array
	 */
	protected $_stripped_tags = array();
	
	/**
	 * The filter name
	 * 
	 * @var string
	 */
	protected $_name;
	
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
		
		parent::__construct($config);
		
		$this->_name = $config->name;
		
		KService::get('plg:contentfilter.chain')->addFilter($this);
	}	
		
    /**
     * Command handler
     * 
     * @param string          $name    The command name
     * @param KCommandContext $context The command context
     * 
     * @return boolean     Can return both true or false.  
     */
	final public function execute($name, KCommandContext $context) 
	{
	    if ( $context->config->filter )
	    {
	        $filters = (array)KConfig::unbox($context->config->filter);
            if ( !in_array($this->_name, $filters) )
                   return $context->data;
	    }
	    if ( $context->config->exclude )
	    {
	        $exclude = (array)KConfig::unbox($context->config->exclude);
	        if ( in_array($this->_name, $exclude) )
	            return $context->data;
	    }
		$context->data = $this->filter($context->data, $context->config);
	}
	
	/**
	 * Strip the tags from a block of text
	 * 
	 * @param string $text Text to string the tags from
	 * 
	 * @return void
	 */
	protected function _stripTags(&$text)
	{
		$regexp		  = '@<(\w+)\b.*?>.*?</\1>@si';
	 	$matches 	  = array();
	 	$replacements = array();
		if (  preg_match_all($regexp, $text, $matches) ) {
			$matches = $matches[0];
			foreach($matches as $i => $tag) $replacements[] = $i.'-'.md5($tag);
			$text 	 = str_replace($matches, $replacements, $text);
		}
		
		$this->_stripped_tags = array($replacements, $matches);
	}
	
	/**
	 * Put the stipped tags back in their oringal place
	 * 
	 * @param string $text Replace the tag back into the text
	 * 
	 * @return void
	 */
	protected function _replaceTags(&$text)
	{
		if ( !empty($this->_stripped_tags) )
			$text 	 = str_replace($this->_stripped_tags[0], $this->_stripped_tags[1], $text);
	}	
}