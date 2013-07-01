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
 * Contentfilter Chain. 
 * 
 * It filters a text by running the text through a chain of contentfilter commands
 *
 * @category   Anahita
 * @package    Plg_Contentfilter
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class PlgContentfilterChain extends KObject 
{
    /**
     * Return the singleton instnace of PlgContentfilterChain. This method also imports all the 
     * content filter plugins
     * 
     * @return PlgContentfilterChain
     */
    static public function getInstance()
    {
        static $_instance;
        
        if ( !$_instance ) 
        {
            $_instance = new self(new KConfig(array('service_container'=>KService::getInstance())));
            KService::set('plg:contentfilter.chain', $_instance);
            JPluginHelper::importPlugin('contentfilter');
        }
        
        return $_instance;      
    }
    
    /**
     * Command Chain
     * 
     * @var KCommandChain
     */
    protected $_chain;
    
    /** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);
        
        $this->_chain = $this->getService('koowa:command.chain');               
    }
            
    /**
     * Alias for the sanitize method
     * 
     * @param string $text   The text to be filtered
     * @param array  $config An Array of config 
     * 
     * @return string
     */
    public function filter($text, $config = array())
    {
        $context         = $this->_chain->getContext();
        $context->data   = $text;
        $context->config = $config;
        if ( $context->config->filter ) 
        {
             $filter   = (array)KConfig::unbox($context->config->filter);
             $filter[] = 'ptag';
             $context->config->filter = $filter;
        }
        $this->_chain->run('filter', $context);
        return $context->data;
    }
    
    /**
     * Adds a content fitler to the content filter chain
     * 
     * @param PlgContentfilterInterface $filter Filter to be added
     * 
     * @return PlgContentfilterChain
     */
    public function addFilter(PlgContentfilterInterface $filter)
    {
        $this->_chain->enqueue($filter);
        return $this;
    }
    
    /**
     * Removes an exsiting content filter fro the content filter chain 
     * 
     * @param PlgContentfilterInterface $filter Filter to be removed
     * 
     * @return PlgContentfilterChain
     */
    public function removeFilter(PlgContentfilterInterface $filter)
    {
        $this->_chain->dequeue($filter);
        return $this;
    }
}