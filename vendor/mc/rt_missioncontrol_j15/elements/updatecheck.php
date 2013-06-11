<?php
/**
 * @version � 1.5.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_BASE') or die();

require_once(JPATH_ADMINISTRATOR.DS.'templates'.DS.'rt_missioncontrol_j15'.DS.'lib'.DS.'missioncontrol.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'templates'.DS.'rt_missioncontrol_j15'.DS.'lib'.DS.'rtmcupdater.class.php');

/**
 * @package     missioncontrol
 * @subpackage  admin.elements
 */
class JElementUpdateCheck extends JElement {


	function fetchElement($name, $value, &$node, $control_name)
	{
        global $mctrl;
        $mctrl =& MissionControl::getInstance();

        if($mctrl->_getCurrentAdminTemplate() == "rt_missioncontrol_j15") {
            $ftp = JClientHelper::getCredentials('ftp');
            if ($ftp['enabled']) {
               $html = '<div class="mc-update-check"><b>* FTP mode currently not supported in Auto-Update</b><br />Please check the MissionControl forum for information on the latest version.</div>'; 
            } else {
               $html = MCUpdater::display(false);
            }


        } else {
            $html = '<b>* Feature only available within MissionControl</b>';
        }


        return $html;
	}


}
?>