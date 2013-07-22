<?php

/** 
 * LICENSE: Anahita is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * 
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Template
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * URL filter fixes all the URLs
 *
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Template
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComNotificationsTemplateFilterUrl extends KTemplateFilterAbstract implements KTemplateFilterWrite
{
    /**
    * Site URL
    *
    * @var string
    */
    protected $_siteurl;
        
    /**
    * Initializes the default configuration for the object
    *
    * Called from {@link __construct()} as a first step of object instantiation.
    *
    * @param KConfig $config An optional KConfig object with configuration options.
    *
    * @return void
    */
    public function __construct(KConfig $config)
    {    
        parent::__construct($config);

        $this->_siteurl = new KHttpUrl(new KConfig(array('url'=>$config->siteurl)));
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
    		'siteurl'  => JFactory::getConfig()->getValue('live_site')
        ));
        	
        parent::_initialize($config);
    }    
    
    /**
    * Find any <link /> elements and render them
    *
    * @param string Block of text to parse
    * @return KTemplateFilterLink
    */
    public function write(&$text)
    {
        $matches = array();
        //fix all the URLs
        if ( preg_match_all('/(href|src)="(.*?)"/', $text, $matches) )
        {
            $original = array();
            $replaces = array();
            	
            foreach($matches[2] as $i => $match)
            {
                //if a url starts with http then skip it
                if ( strpos($match, 'http') === 0 || !$match )
                    continue;
                $original[] = $matches[0][$i];
                //absolute path, make sure the
                if ( strpos($match,'/') === 0 ) {
                    $url = clone $this->_siteurl;
                    $url->setPath(null);
                    $url = $url.$match;
                }
                else {
                    $url  = (string)(clone $this->_siteurl).'/';
                    $url .= $match;
                }
                $url         = str_replace('/components/com_notifications','',$url);
                $replaces[]  = str_replace($match, $url, $matches[0][$i]);                               
            }

            $text 	 = str_replace($original, $replaces, $text);
        }        
        
        return $this;        
    }    
}