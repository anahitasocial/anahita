<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
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
class ModBaseView extends ComBaseViewHtml
{
    /**
     * Returns a module view or the default module view
     *
     * @param string $identifier The module identifier
     * @param array  $config     Configuration values
     * 
     * @return ModAppView
     */
    static public function getInstance($identifier, $config = array())
    {
        $identifier = KService::getIdentifier($identifier);
        if ( !$identifier->application )
            $identifier->application = 'site';
        register_default(array('identifier'=>$identifier, 'default'=>'ModBaseView'));
        return KService::get($identifier, $config);
    }
    
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
                'viewer' 		 => get_viewer(),
                'template'    => $template,
                'params'      => null,
                'module'      => null,
                'attribs'     => array()                
        ));
        
        parent::_initialize($config);       
    }

    /**
     * Get the name
     *
     * @return 	string 	The name of the object
     */
    public function getName()
    {
        return $this->getIdentifier()->package;
    }    
}