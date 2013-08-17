<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Html
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */
 
/**
 * Content Controller
 *
 * @category   Anahita
 * @package    Com_Html
 * @subpackage Controller
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComHtmlControllerContent extends ComBaseControllerResource
{
    /**
     * Content base path
     * 
     * @var string
     */
    protected $_base_path;
    
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

        $this->_base_path = $config->base_path;
        
        $this->setView($config->view);
        $this->getService()->set('com:html.controller', $this);
        
        if ( $this->_base_path )
        {
            if ( ! preg_match('%^(\w:)?[/\\\\]%', $this->_base_path)) {
                $this->_base_path = realpath(JPATH_ROOT.'/'.$this->_base_path);
            }
                        
            $this->getService()->setConfig($this->_view, array(
                    'template_paths' => $this->_base_path
            ));            
        }
    }
        
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KConfig $config)
    {                
        $config->append(array(
			'base_path' => get_config_value('com_html.content_path'),
            'request'   => array('layout'=>'default')              
        ));
        
        parent::_initialize($config);
    }    
}
