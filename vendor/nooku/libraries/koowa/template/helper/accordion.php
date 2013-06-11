<?php
/**
 * @version		$Id: accordion.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Template Accordion Behavior Helper
 *
 * @author		Stian Didriksen <stian@timble.net>
 * @package		Koowa_Template
 * @subpackage	Helper
 * @uses		KArrayHelper
 */
class KTemplateHelperAccordion extends KTemplateHelperBehavior
{
	/**
	 * Creates a pane and creates the javascript object for it
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return 	string	Html
	 */
	public function startPane( $config = array() )
	{
		$config = new KConfig($config);

		$config->append(array(
			'id'	=> 'accordions',
			'options'	=> array(
				'duration'		=> 300,
				'opacity'		=> false,
				'alwaysHide'	=> true,
				'scroll'		=> false
			),
			'attribs'	=> array(),
			'events'	=> array()
		));

		$html  = '';

		// Load the necessary files if they haven't yet been loaded
		if (!isset($this->_loaded['accordion'])) {
			$this->_loaded['accordion'] = true;
		}

		$id      = strtolower($config->id);
		$attribs = KHelperArray::toString($config->attribs);

		$events			= '';
		$onActive 		= 'function(e){e.addClass(\'jpane-toggler-down\');e.removeClass(\'jpane-toggler\');}';
		$onBackground	= 'function(e){e.addClass(\'jpane-toggler\');e.removeClass(\'jpane-toggler-down\');}';

		if($config->events) {
			$events = '{onActive:'.$onActive.',onBackground:'.$onBackground.'}';
		}

		$scroll = $config->options->scroll ? ".addEvent('onActive', function(toggler){
			new Fx.Scroll(window, {duration: this.options.duration, transition: this.transition}).toElement(toggler);
		})" : '';

		/*
		 * Until we find a solution that let us pass a string into json_encode without it being quoted,
		 * we have to use the mootools $merge method to merge events and regular settings back into one
		 * options object.
		*/
		$html .= '
			<script>
				window.addEvent(\'domready\', function(){
					new Accordion($$(\'.panel h3.jpane-toggler\'),$$(\'.panel div.jpane-slider\'),$merge('.$events.','.$config->options.'))'.$scroll.';
				});
			</script>';

		$html .= '<div id="'.$id.'" class="pane-sliders" '.$attribs.'>';
		return $html;
	}

	/**
	 * Ends the pane
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return 	string	Html
	 */
	public function endPane($config = array())
	{
		return '</div>';
	}

	/**
	 * Creates a tab panel with title and starts that panel
	 *
	 * @param	string	The title of the tab
	 * @param	array	An associative array of pane attributes
	 */
	public function startPanel($config = array())
	{
		$config = new KConfig($config);

		$config->append(array(
			'title'		=> 'Slide',
			'attribs'	=> array(),
			'translate'	=> true
		));

		$title   = $config->translate ? JText::_($config->title) : $config->title;
		$attribs = KHelperArray::toString($config->attribs);

		$html = '<div class="panel"><h3 class="jpane-toggler title" '.$attribs.'><span>'.$title.'</span></h3><div class="jpane-slider content">';
		return $html;
	}

	/**
	 * Ends a tab page
	 *
	 * @param 	array 	An optional array with configuration options
	 * @return 	string	Html
	 */
	public function endPanel($config = array())
	{
		return '</div></div>';
	}
}