<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: view.php 13650 2012-04-11 08:56:41Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Date Helper
 *
 * @category   Anahita
 * @package    Com_Base
 * @subpackage Template_Helper
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComBaseTemplateHelperDate extends LibBaseTemplateHelperDate
{
	/**
	 * Returns formated date according to current local. If $offset is null the offset is
	 * adjusted by the viewer timezone.
	 * 
	 * If format is null the date is given in human friendly format
	 *
	 * @param	KDate|string	A date in an US English date format
	 * @param	string	format optional format for strftime
	 * @returns	string	formated date
	 * @see		strftime
	 */	
	public function format($date, $format = '%B %d %Y', $offset = NULL)
	{
		$relative = true;
		
		if ( is_array($format) )
		{
			$config   = new KConfig($format);
			
			$format   = $config->format;
			
			$offset   = $config->offset;

			$relative = $config->relative;
			
			if ( !$relative )
			{
				$offset = get_viewer()->timezone;
			}
		}
		
		if ( is_object($date) && $date->inherits('KDate') ) 
		{
			//@TODO apply offset
			return $this->humanize($date, array('format'=>$format, 'relative'=>$relative, 'offset'=>$offset));
		}
		
		if ( ! $format ) 
		{
			$format = JText::_('DATE_FORMAT_LC1');
		}

		if(is_null($offset))
		{
			$config = JFactory::getConfig();
			$offset = $config->getValue('config.offset');
		}

		$instance = new KDate(array('date'=>$date));
		$instance->setOffset($offset);

		return $instance->toFormat($format);
	}
}
?>