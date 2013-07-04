<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Com_Search
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Search View 
 * 
 * @category   Anahita
 * @package    Com_Search
 * @subpackage View
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class ComSearchViewSearchHtml extends ComBaseViewHtml
{
	/**
	 * (non-PHPdoc)
	 * @see LibBaseViewTemplate::load()
	 */
	public function load($template, array $data = array())
	{
		$identifier = $this->_state->getItem()->getIdentifier();
		$path 		= JPATH_ROOT.'/components/'.$identifier->package.'/views/'.$identifier->name.'/html/search';
		
		if ( $this->getTemplate()->findTemplate($identifier->name) ) {
			$template = $identifier->name;
		}
		
		$data   = array_merge($this->_data, $data);
		$output = $this->getTemplate()->loadTemplate($template, $data)->render();
		return $output;		
	}
}