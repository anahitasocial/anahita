<?php 

/**
 * LICENSE: ##LICENSE##
 *
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2015 rmdStudio Inc.
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */

/**
 * Creates a hyperlink from the URLs
 *
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.GetAnahita.com
 */
 
 class PlgContentfilterGist extends PlgContentfilterAbstract
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
            'priority' => KCommand::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }
    
    /**
     * Filter a value. 
     * 
     * @param string The text to filter
     * 
     * @return string
     */ 
    public function filter($text)
    {
       
        $text = preg_replace(
        "/https?\:\/\/gist.github.com\/[\S]+\/[0-9a-z]+[^\W]/", 
        "<div class=\"an-meta\" data-trigger=\"LoadGist\" data-inline data-src=\"\\0.js\">".JText::_('LIB-AN-REMOTE-LOADING')."</div>", 
        $text);
           
        return $text;   
    }       
}    
