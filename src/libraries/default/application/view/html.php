<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Application
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Application HTML view
 *
 * @category   Anahita
 * @package    Lib_Application
 * @subpackage View 
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibApplicationViewHtml extends LibBaseViewTemplate
{
    /**
     * Page content to display
     * 
     * @return KException|string
     */
    public $content;
    
    /**
     * Template Parameters
     * 
     * KConfig
     */
    protected $_params;
    
    /** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
    public function __construct(KConfig $config)
    {
        $config->media_url = 'base://media';
        $config->base_url  = $config->service_container->get('application')->getRouter()->getBaseUrl();
        
        parent::__construct($config);
        
        $this->_params  = $config->params;        
               
        JFactory::getLanguage()->load('tpl_'.$this->getIdentifier()->package);
        
        $this->getTemplate()->getFilter('alias')
            ->append(array('@render(\''=>'$this->renderHelper(\'render.'))
            ->append(array('base://'=>$this->getBaseUrl().'/'), KTemplateFilter::MODE_WRITE);
    }
        
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
        $params = '';
        
        if ( is_readable($file = JPATH_THEMES.'/'.$this->getIdentifier()->package.'/params.ini') ) {
            $params = file_get_contents($file);
        }
                
        $params  = new JParameter($params);
        
        $config->append(array(
            'mimetype'          => 'text/html',
            'params'            => $params->toArray(),                    
            'template_filters'  => array('shorttag','html','alias')
        ));
                
        parent::_initialize($config);
    }
            
    /**
     * Displays the template
     * 
     * @return string
     */
    public function display()
    {
        if ( $this->content instanceof Exception ) 
        {
        	$error   = $this->content;
            
        	$layout  = $error->getCode();
                    		
            if ( !$this->getTemplate()->findPath('errors/'.$layout.'.php') ) {
                $layout = 'default';
            }
            
            $this->content = $this->getTemplate()->loadTemplate('errors/'.$layout, array('error'=>$error))->render();         
            
            if ( JDEBUG ) 
            {
                $traces   = array();
                $traces[] = '<h4>Exception '.get_class($error).' with message "'.$error->getMessage().'"</h4>';
                $traces[] = $error->getFile().':'.$error->getLine();
                foreach($error->getTrace() as $trace) {
                    $str = '';
                    
                    if ( isset($trace['file']) ) 
                       $str = $trace['file'].':'; 
                    
                    if ( isset($trace['line']) ) 
                       $str .= $trace['line']; 
                    
                    if ( empty($str) ) continue;
                    
                    $traces[] = $str;
                }
                
                $this->content .= '<pre>'.implode('<br />', $traces).'</pre>';    
            }
        }
        
        $this->output = $this->getTemplate()->loadTemplate($this->getLayout(), array('output'=>$this->content))->render();
        
        return $this->output;
    }
    
    /**
     * Set the template parameters
     * 
     * @param array $params
     * 
     * @return void
     */
    public function setParams($params)
    {
        $this->_params = new KConfig($params);
    }
    
    /**
     * Get template parameters
     * 
     * @return KConfig
     */
    public function getParams()
    {
        return $this->_params;
    }  
}