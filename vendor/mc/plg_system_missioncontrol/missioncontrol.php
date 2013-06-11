<?php
/**
 * @version   1.5.2 June 9, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('_JEXEC') or die('Restricted index access');

jimport('joomla.plugin.plugin');

class plgSystemMissionControl extends JPlugin
{

    function plgSystemMissionControl(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }


    function onAfterRoute()
    {
        global $mainframe;

        $option = JRequest::getString('option');

        $output = "<?php \n";

        $template = '';


        $tid = JRequest::getString('id');

        if ($mainframe->isAdmin() && $mainframe->getTemplate() == "rt_missioncontrol_j15")
        {
            $template = "rt_missioncontrol_j15";

            $params = $this->getTemplateParams('rt_missioncontrol_j15');
            $blacklist = $params->get('blacklist');
            if (!empty($blacklist) && !is_array($blacklist)) $blacklist = array($blacklist);
            if (!empty($blacklist))
            {
                if ($params->get('patching', 0) == 1)
                {
                    plgSystemMissionControl::checkPatched();
                }
                else
                {
                    plgSystemMissionControl::revertPatched();
                }

                if (in_array($option, $blacklist))
                {
                    $mainframe->getTemplate('khepri');
                }
            }
        }


        // is user in admin area?
        if ($mainframe->isAdmin() && $tid == 'rt_missioncontrol_j15')
        {
            // in admin area

            if ($template == "rt_missioncontrol_j15"
                && JRequest::getString('option', '', 'post') == 'com_templates'
                && (JRequest::getString('task', '', 'post') == 'apply' || JRequest::getString('task', '', 'post') == 'save')
            )
            {


                $params = JRequest::getVar('params', '', 'post');


                foreach ($params as $key => $value)
                {

                    if (strpos($key, '_color') > 0)
                    {
                        $output .= '$' . $key . '="' . $value . '";';
                    }

                }

                $path = JPATH_ADMINISTRATOR . DS . 'templates' . DS . $template . DS . 'css' . DS . 'color-vars.php';

                jimport('joomla.filesystem.file');
                JFile::write($path, $output);

                return;
            }
        }


    }

    function checkPatched()
    {
        jimport('joomla.filesystem.file');

        if (!JFile::exists('includes/application.php.orig'))
        {
            if (@JFile::copy('includes/application.php', 'includes/application.php.orig')){
                $admin_app = JFile::read('includes/application.php');

                if (strpos($admin_app, 'getTemplate()'))
                {

                    $admin_app = str_replace('getTemplate()', 'getTemplate($temptemplate=null)', $admin_app);
                    $admin_app = str_replace('static $template;', 'static $template;
            if ($temptemplate) $template=$temptemplate;', $admin_app);

                    JFile::write('includes/application.php', $admin_app);
                }
            }
        }

    }

    function revertPatched()
    {
        jimport('joomla.filesystem.file');
        if (JFile::exists('includes/application.php.orig'))
        {
            @JFile::move('includes/application.php.orig', 'includes/application.php');
        }
    }


    function getTemplateParams($template)
    {
        $params = null;
        // Parse the template INI file if it exists for parameters and insert
        // them into the template.
        if (is_readable(JPATH_ADMINISTRATOR . '/templates/' . $template . '/params.ini'))
        {
            $content = file_get_contents(JPATH_ADMINISTRATOR . '/templates/' . $template . '/params.ini');
            $params = new JParameter($content);
        }

        return $params;
    }


}
