<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Application
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
 * @package    Com_Application
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComApplicationViewHtml extends LibApplicationViewHtml
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
        $identifier = clone $this->getIdentifier();        
        $identifier->path = array();
        $paths[] = JPATH_THEMES.'/base/'.$this->getFormat();
        $paths[] = dirname($identifier->filepath).'/'.$this->getFormat();
        $config->append(array(
            'template_paths' => $paths,                      
        ));
               
        parent::_initialize($config);
    }
    
    /**
     * (non-PHPdoc)
     * @see LibBaseViewAbstract::getRoute()
     */
    public function getRoute($route = '', $fqr = true)
    {
    	if(strpos($route, 'index.php?') === false )
    		$route .= 'index.php?'.$route;
    	
    	return $this->getService('application')->getRouter()->build($route);
    }
}