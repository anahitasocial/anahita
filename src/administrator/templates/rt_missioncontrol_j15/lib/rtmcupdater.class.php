<?php
/**
 * @version � 1.5.2 June 9, 2011
 * @author � �RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license � http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_BASE') or die();

class MCUpdater {

    function display($update=false) {

        global $mctrl;

        // see if $mctrl has been initiated yet, if not do so
        if (!$mctrl) {
            require_once(JPATH_ADMINISTRATOR.'/templates/rt_missioncontrol_j15/lib/missioncontrol.class.php');
            $mctrl =& MissionControl::getInstance();
        }
        
        $output = '';
        $update_info = '';

        require_once($mctrl->templatePath.'/lib/updater/rokupdater.class.php');
        if (!$update) $mctrl->addScript('MC.Updater.js');

        $params =& $mctrl->params;


        $updater = new RokUpdater();
        $updater->init($mctrl->updateUrl, $mctrl->updateSlug, $params, $params->get('updater_dl_method'), $params->get('updater_extract_method'));

        // do update stuff
        ob_start();
        
        if ($update) {
            if ($updater->installUpdate()) {
                $update_info = '<b>Success!</b> ';
            } else {
                $update_info = '<b class="mc-error">Update Failed. </b>';
            }
        }

        //get status details
        $details = $updater->updateAvailable();

        $errors = ob_get_clean();

        if ($details === false || $errors) {
                $output .= '<div class="mc-update-check updates-true">';
                $output .=  '<b>There was an error processing your request:</b>';
                $output .=  $errors;
                $output .=  '<p class="mc-update"><a href="#">Force Update</a> <span class="spinner"></span></p>';
                $output .=  '</div>';

        } else {

            if ($details->updates) {
                $output .=  '<div class="mc-update-check updates-true">';
                $output .=  $update_info.$details->name.' <span class="mc-new-version">'.$details->version.'</span> is now available. ';
                $output .=  'You have version <span class="mc-old-version">'.$details->current_version.'</span>. ';
                $output .=  '<p class="mc-update"><a href="#">1-Click Update</a> <span class="spinner"></span></p>';
                $output .=  '</div>';
            } else {
                $output .=  '<div class="mc-update-check updates-false">';
                $output .=  $update_info.$details->name.' <span class="mc-old-version">'.$details->current_version.'</span> is the most current version available. ';
                $output .=  '<p class="mc-update"><a href="#">Force Update</a> <span class="spinner"></span></p>';
                $output .=  '</div>';
            }
        }

        return $output;
    }





}