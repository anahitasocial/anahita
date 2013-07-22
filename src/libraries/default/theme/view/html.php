<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Lib_Themes
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Theme view object
 *
 * @category   Anahita
 * @package    Lib_Theme
 * @subpackage View 
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibThemeViewHtml extends LibBaseViewTemplate
{
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
        parent::__construct($config);
        
        $this->_params  = $config->params;        
               
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
        $config->append(array(
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
        if ( $this->document && $this->document instanceof JDocumentError ) 
        {
            $error   = $this->document->_error;
            
            $layout  = $error->code;
            
            if ( !$this->getTemplate()->findPath('errors/'.$layout.'.php') ) {
                $layout = 'default';
            }
            
            $output = $this->getTemplate()->loadTemplate('errors/'.$layout, array('error'=>$error))->render();
            
            if ( JDEBUG ) {
                $output .= '<pre>'.$this->document->renderBacktrace().'</pre>';    
            }
            
            $this->content = $output;
            
            $this->setLayout('error');
        }
        
        //if raw template then just render the content
        if ( $this->getLayout() == 'raw' ) {
            $this->output = $this->content;    
        }
        else {
            $this->output = $this->getTemplate()->loadTemplate($this->getLayout(), $this->_data)->render();
        }
                   
        return $this->output; 
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