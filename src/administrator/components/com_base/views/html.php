<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Base View Class
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseViewHtml extends LibBaseViewHtml
{
	/**
	 * Constructor.
	 *
	 * @param 	object 	An optional KConfig object with configuration options
	 */
	public function __construct(KConfig $config)
	{
		parent::__construct($config);
		
        //Add alias filter for editor helper
        $this->getTemplate()->getFilter('alias')->append(array(
            '@editor(' => '$this->renderHelper(\'com://admin/default.template.helper.editor.display\', ')
        );		
	}	
		
    /**
     * Initializes the configuration for the object
     * 
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   array   Configuration settings
     */
    protected function _initialize(KConfig $config)
    {
    	$path[] = dirname($this->getIdentifier()->filepath).'/html';

        $config->append(array(
            'template_filters'  => array('form','module'),      
            'media_url'         => JURI::base().'../media',          
            'layout' 		    => KInflector::isSingular($this->getName()) ? 'form' : 'default',
        	'template_paths'	=> $path
        ));
        
        parent::_initialize($config);
    }
}