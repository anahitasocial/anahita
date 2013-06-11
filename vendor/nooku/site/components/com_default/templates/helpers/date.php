<?php
/**
 * @version     $Id: date.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Date Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateHelperDate extends KTemplateHelperDate
{
    /**
     * Returns formated date according to current local and adds time offset
     *
     * @param   string  A date in ISO 8601 format or a unix time stamp
     * @param   string  format optional format for strftime
     * @returns string  formated date
     * @see     strftime
     */
    public function format($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'format' => JText::_('DATE_FORMAT_LC1'),
            'gmt_offset' => JFactory::getConfig()->getValue('config.offset') * 3600
        ));

        return parent::format($config);
    }

    /**
     * Returns human readable date.
     *
     * @param  array   An optional array with configuration options.
     * @return string  Formatted date.
     */
    public function humanize($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'gmt_offset' => 0
        ));

       return parent::humanize($config);
    }
}
