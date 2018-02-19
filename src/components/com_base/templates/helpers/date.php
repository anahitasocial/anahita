<?php

/**
 * Date Helper.
 *
 * @category   Anahita
 *
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 *
 * @link       http://www.GetAnahita.com
 */
class ComBaseTemplateHelperDate extends LibBaseTemplateHelperDate
{
    /**
     * Returns formated date according to current local. If $offset is null the offset is
     * adjusted by the viewer timezone.
     *
     * If format is null the date is given in human friendly format
     *
     * @param	AnDate|string	A date in an US English date format
     * @param	string	format optional format for strftime
     * @returns	string	formated date
     *
     * @see		strftime
     */
    public function format($date, $format = '%B %d %Y', $offset = null)
    {
        $relative = true;

        if (is_array($format)) {
            $config = new KConfig($format);
            $format = $config->format;
            $offset = $config->offset;
            $relative = $config->relative;

            if (!$relative) {
                $offset = get_viewer()->timezone;
            }
        }

        if (!(is_object($date) && $date->inherits('AnDate'))) {
            $date = new AnDate(new KConfig(array('date' => $date)));
        }

        return $this->humanize($date, array(
                                      'format' => $format,
                                      'relative' => $relative,
                                      'offset' => $offset, ));
    }
}
