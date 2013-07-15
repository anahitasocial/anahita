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
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Notification text template helper class.
 *
 * @category   Anahita
 * @package    Com_Notifications
 * @subpackage Template
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComNotificationsTemplateHelperText extends LibBaseTemplateHelperText
{
    /**
     * Truncates a text
     *
     * @param string $text    The text to truncate
     * @param array  $options Truncation options. Can be 'length'=>integer, 'read_more'=>boolean, 
     * 'ending'=>string, 'exact'=>boolean, 'consider_html'=>false
     * 
     * @return string
     */
    public function truncate($text, $options = array())
    {
        $options['read_more'] = false;
        $options['length']    = 500;
        return parent::truncate($text, $options);
    }    
}