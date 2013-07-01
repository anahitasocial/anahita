<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Groups
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

function com_install($intaller) 
{
    //doesn't exists create a menu item
	if ( !$intaller->get('component_exists') ) 
    {        
        if ( dbexists('SELECT * FROM #__menu_types WHERE menutype LIKE "viewer"') ) 
        {
            $component_id = dbfetch("SELECT id from #__components WHERE `option` LIKE 'com_groups'", KDatabase::FETCH_FIELD);
            dbexec("INSERT INTO `#__menu` VALUES(NULL, 'viewer', 'Groups', 'groups', 'index.php?option=com_groups&view=groups&oid=viewer&filter=following', 'component', 1, 0, $component_id, 1, 3, 0, '0000-00-00 00:00:00', 0, 0, 1, 0, 'page_title=\nshow_page_title=1\npageclass_sfx=\nmenu_image=-1\nsecure=0\n\n', 0, 0, 0)");   
        }
    }
}