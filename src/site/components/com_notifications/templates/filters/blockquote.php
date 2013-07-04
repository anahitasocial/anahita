<?php

/** 
 * LICENSE: ##LICENSE##
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
 * Fixes the blockquotes in the body
 *
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Template
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComNotificationsTemplateFilterBlockquote extends KTemplateFilterAbstract implements KTemplateFilterRead
{    
    /**
    * Fixes blockquotes
    *
    * @param string Block of text to parse
    * @return KTemplateFilterLink
    */
    public function read(&$text)
    {
        $text = preg_replace('%<blockquote>(.*?)</blockquote>%','<blockquote style="margin:0px">\1</blockquote>', $text);
        
        return $this;        
    }    
}