<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Mod_Base
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * HTML Module
 *
 * @category   Anahita
 * @package    Mod_Base
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ModBaseHtml extends ComBaseViewHtml
{    
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

        //Assign module specific options
        $this->params  = $config->params;
        $this->module  = $config->module;
        $this->attribs = $config->attribs;
        $this->viewer  = $config->viewer;
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
        $template = clone $this->getIdentifier();
        $template->name = 'template';
        register_default(array('identifier'=>$template, 'default'=>'ComBaseTemplateDefault'));
       
        $config->append(array(
                'viewer' 	  => get_viewer(),
                'template'    => $template,
                'params'      => null,
                'module'      => null,
                'attribs'     => array()                
        ));
        
        parent::_initialize($config);       
    }

    /**
     * (non-PHPdoc)
     * @see LibBaseViewAbstract::getRoute()
     */
    public function getRoute($route = '', $fqr = false)
    {
    	return $this->getService('application')
    		->getRouter()->build($route, $fqr);    	    
    }
    
    /**
     * Get the name
     *
     * @return 	string 	The name of the object
     */
    public function getName()
    {
        return $this->getIdentifier()->name;
    }    
}