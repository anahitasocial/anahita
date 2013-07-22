<?php
/**
 * @version � 1.5.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_BASE') or die();
require_once(JPATH_ADMINISTRATOR.'/templates/rt_missioncontrol_j15/lib/missioncontrol.class.php');
/**
 * @package     missioncontrol
 * @subpackage  admin.elements
 */
class JElementLogoUpload extends JElement {


	function fetchElement($name, $value, &$node, $control_name)
	{
        global $mctrl;
        $mctrl =& MissionControl::getInstance();

        if($mctrl->_getCurrentAdminTemplate() == "rt_missioncontrol_j15") {
            $upload_url = "?process=ajax&amp;model=logoupload";
            $html = '<iframe src="'.$upload_url.'" class="mc-logoupload"></iframe>';
        } else {
            $html = '<b>* Feature only available within MissionControl</b>';
        }



        return $html;

	}

}
?>