<?php
/**
 * @version		$Id: behavior.php 4628 2012-05-06 19:56:43Z johanjanssens $
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Template Behavior Helper
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_Template
 * @subpackage	Helper
 */
class KTemplateHelperBehavior extends KTemplateHelperAbstract
{
	/**
	 * Array which holds a list of loaded javascript libraries
	 *
	 * boolean
	 */
	protected static $_loaded = array();

	/**
	 * Method to load the mootools framework into the document head
	 *
	 * - If debugging mode is on an uncompressed version of mootools is included for easier debugging.
	 *
	 * @param	boolean	$debug	Is debugging mode on? [optional]
	 */
	public function mootools($config = array())
	{
		$html ='';

		// Only load once
		if (!isset(self::$_loaded['mootools'])) 
		{
		    $config = new KConfig($config);
		    
		    $html .= '<script src="media://lib_koowa/js/mootools.js" />';
			self::$_loaded['mootools'] = true;
		}

		return $html;
	}

	/**
	 * Render a modal box
	 *
	 * @return string	The html output
	 */
	public function modal($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'selector' => 'a.modal',
			'options'  => array('disableFx' => true)
 		));

 		$html = '';

		// Load the necessary files if they haven't yet been loaded
		if (!isset(self::$_loaded['modal']))
		{
			$html .= '<script src="media://lib_koowa/js/modal.js" />';
			$html .= '<style src="media://lib_koowa/css/modal.css" />';

			self::$_loaded['modal'] = true;
		}

		$signature = 'modal-'.$config->selector;
		if (!isset(self::$_loaded[$signature]))
		{
			$options = !empty($config->options) ? $config->options->toArray() : array();
			$html .= "
			<script>
				window.addEvent('domready', function() {

				SqueezeBox.initialize(".json_encode($options).");
				SqueezeBox.assign($$('".$config->selector."'), {
			        parse: 'rel'
				});
			});
			</script>";

			self::$_loaded[$signature] = true;
		}

		return $html;
	}

	/**
	 * Render a tooltip
	 *
	 * @return string	The html output
	 */
	public function tooltip($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'selector' => '.hasTip',
			'options'  => array()
 		));

 		$html = '';

		$signature = 'tooltip-'.$config->selector;
		if (!isset(self::$_loaded[$signature]))
		{
		    //Don't pass an empty array as options
			$options = $config->options->toArray() ? ', '.$config->options : '';
			$html .= "
			<script>
				window.addEvent('domready', function(){ new Tips($$('".$config->selector."')".$options."); });
			</script>";

			self::$_loaded[$signature] = true;
		}

		return $html;
	}

	/**
	 * Render an overlay
	 *
	 * @return string	The html output
	 */
	public function overlay($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'url'  		=> '',
			'options'  	=> array(),
			'attribs'	=> array(),
		));

		$html = '';
		// Load the necessary files if they haven't yet been loaded
		if (!isset(self::$_loaded['overlay']))
		{
			$html .= '<script src="media://lib_koowa/js/koowa.js" />';
			$html .= '<style src="media://lib_koowa/css/koowa.css" />';

			self::$_loaded['overlay'] = true;
		}

		$url = $this->getService('koowa:http.url', array('url' => $config->url));
		if(!isset($url->query['tmpl'])) {
		    $url->query['tmpl'] = '';
		}

		$attribs = KHelperArray::toString($config->attribs);

        $id = 'overlay'.rand();
        if($url->fragment)
        {
            //Allows multiple identical ids, legacy should be considered replaced with #$url->fragment instead
            $config->append(array(
                'options' => array(
                    'selector' => '[id='.$url->fragment.']'
                )
            ));
        }
		
		//Don't pass an empty array as options
		$options = $config->options->toArray() ? ', '.$config->options : '';
		$html .= "<script>window.addEvent('domready', function(){new Koowa.Overlay('$id'".$options.");});</script>";

		$html .= '<div data-url="'.$url.'" class="-koowa-overlay" id="'.$id.'" '.$attribs.'><div class="-koowa-overlay-status">'.JText::_('Loading...').'</div></div>';
		return $html;
	}

	/**
	 * Keep session alive
	 *
	 * This will send an ascynchronous request to the server via AJAX on an interval
	 * in miliseconds
	 *
	 * @return string	The html output
	 */
	public function keepalive($config = array())
	{
	    $html = '';
	    
	    // Only load once
	    if (!isset(self::$_loaded['keepalive']))
	    { 
	        $config = new KConfig($config);
		    $config->append(array(
				'refresh'  => 15 * 60000, //15min
		    	'url'	   => $this->getTemplate()->getView()->getRoute()
		    ));

		    $refresh = (int) $config->refresh;

	        // Longest refresh period is one hour to prevent integer overflow.
		    if ($refresh > 3600000 || $refresh <= 0) {
			    $refresh = 3600000;
		    }

		    // Build the keepalive script.
		    $html =
			"<script>
				Koowa.keepalive =  function() {
					var request = new Request({method: 'get', url: '".$config->url."'}).send();
				}

				window.addEvent('domready', function() { Koowa.keepalive.periodical('".$refresh."'); });
			</script>";
		    
		    self::$_loaded['keepalive'] = true;
	    }

		return $html;
	}
	
	/**
	 * Loads the Forms.Validator class and connects it to Koowa.Controller
	 *
	 * This allows you to do easy, css class based forms validation-
	 * Koowa.Controller.Form works with it automatically.
	 * Requires koowa.js and mootools to be loaded in order to work.
	 *
	 * @see    http://www.mootools.net/docs/more125/more/Forms/Form.Validator
	 *
	 * @return string	The html output
	 */
	public function validator($config = array())
	{
	    $config = new KConfig($config);
		$config->append(array(
			'selector' => '.-koowa-form',
		    'options'  => array(
		        'scrollToErrorsOnChange' => true,
		        'scrollToErrorsOnBlur'   => true
		    )
		));

		$html = '';
		// Load the necessary files if they haven't yet been loaded
		if(!isset(self::$_loaded['validator']))
		{
		    if(version_compare(JVERSION,'1.6.0','ge')) {
		        $html .= '<script src="media://lib_koowa/js/validator-1.3.js" />';
		    } else {
		        $html .= '<script src="media://lib_koowa/js/validator-1.2.js" />';
		    }
		    $html .= '<script src="media://lib_koowa/js/patch.validator.js" />';

            self::$_loaded['validator'] = true;
        }
        
        $signature = 'validator-'.$config->selector;
        if (!isset(self::$_loaded[$signature]))
        {
            //Don't pass an empty array as options
		    $options = $config->options->toArray() ? ', '.$config->options : '';
		    $html .= "<script>
			window.addEvent('domready', function(){
		    	$$('$config->selector').each(function(form){
		        	new Koowa.Validator(form".$options.");
		        	form.addEvent('validate', form.validate.bind(form));
		   	 });
			});
			</script>";
		    
		    self::$_loaded[$signature] = true;
	    }

		return $html;
	}
	
	/**
	 * Loads the autocomplete behavior and attaches it to a specified element
	 *
	 * @see    http://mootools.net/forge/p/meio_autocomplete
	 * @return string	The html output
	 */
	public function autocomplete($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'identifier'    => null,
			'element'       => null,
			'path'          => 'name',
			'filter'		=> array(),
		    'validate'		=> true,
		    'selected'		=> null	
		))->append(array(
		    'value_element' => $config->element.'-value',
		    'attribs' => array(
		        'id'    => $config->element,
		        'type'  => 'text',
		        'class' => 'inputbox value',
		        'size'	=> 60
		    ),
		))->append(array(
			'options' => array( 
		        'valueField'     => $config->value_element,
		        'filter'         => array('path' => $config->path),
				'requestOptions' => array('method' => 'get'),
		        'urlOptions'	 => array(
		    		'queryVarName' => 'search',
		        	'extraParams'  => KConfig::unbox($config->filter)
		        )
		    )
		));
		
		if($config->validate) 
		{
		    $config->attribs['data-value']  = $config->element.'-value';
		    $config->attribs['class']      .= ' ma-required';
		}
		
		if(!isset($config->url)) 
		{
		    $identifier = $this->getIdentifier($config->identifier);
		    $config->url = JRoute::_('index.php?option=com_'.$identifier->package.'&view='.$identifier->name.'&format=json', false);
		}
		    
		$html = '';
		
		// Load the necessary files if they haven't yet been loaded
		if(!isset(self::$_loaded['autocomplete']))
		{
		    $html .= '<script src="media://lib_koowa/js/autocomplete.js" />';
		    $html .= '<script src="media://lib_koowa/js/patch.autocomplete.js" />';
		    $html .= '<style src="media://lib_koowa/css/autocomplete.css" />';
		}
		
		$html .= "
		<script>
			window.addEvent('domready', function(){				
				new Koowa.Autocomplete($('".$config->element."'), ".json_encode($config->url).", ".json_encode(KConfig::unbox($config->options)).");
			});
		</script>";
		
		$html .= '<input '.KHelperArray::toString($config->attribs).' />';
	    $html .= '<input '.KHelperArray::toString(array(
            'type'  => 'hidden',
            'name'  => $config->value,
            'id'    => $config->element.'-value',
            'value' => $config->selected
	       )).' />';

	    return $html;
	}
	
	/**
	 * Loads the calendar behavior and attaches it to a specified element
	 *
	 * @return string	The html output
	 */
    public function calendar($config = array())
	{
		$config = new KConfig($config);
		$config->append(array(
			'date'	  => gmdate("M d Y H:i:s"),
		    'name'    => '',
		    'format'  => '%Y-%m-%d %H:%M:%S',
		    'attribs' => array('size' => 25, 'maxlenght' => 19),
		    'gmt_offset' => JFactory::getConfig()->getValue('config.offset') * 3600
 		));
 
        if($config->date && $config->date != '0000-00-00 00:00:00' && $config->date != '0000-00-00') { 
            $config->date = strftime($config->format, strtotime($config->date) /*+ $config->gmt_offset*/);
        }
        else $config->date = '';
        
	    $html = '';
		// Load the necessary files if they haven't yet been loaded
		if (!isset(self::$_loaded['calendar']))
		{
			$html .= '<script src="media://lib_koowa/js/calendar.js" />';
			$html .= '<script src="media://lib_koowa/js/calendar-setup.js" />';
			$html .= '<style src="media://lib_koowa/css/calendar.css" />';
			
			$html .= '<script>'.$this->_calendarTranslation().'</script>';

			self::$_loaded['calendar'] = true;
		}
	   
		$html .= "<script>
					window.addEvent('domready', function() {Calendar.setup({
        				inputField     :    '".$config->name."',     	 
        				ifFormat       :    '".$config->format."',   
        				button         :    'button-".$config->name."', 
        				align          :    'Tl',
        				singleClick    :    true,
        				showsTime	   :    false
    				});});
    			</script>";
		
		$attribs = KHelperArray::toString($config->attribs);

   		$html .= '<input type="text" name="'.$config->name.'" id="'.$config->name.'" value="'.$config->date.'" '.$attribs.' />';
		$html .= '<img class="calendar" src="media://lib_koowa/images/calendar.png" alt="calendar" id="button-'.$config->name.'" />';
		
		return $html;
	}
	
	/**
	 * Method to get the internationalisation script/settings for the JavaScript Calendar behavior.
	 *
	 * @return string	The html output
	 */
	protected function _calendarTranslation()
	{
		// Build the day names array.
		$dayNames = array(
			'"'.JText::_('Sunday').'"',
			'"'.JText::_('Monday').'"',
			'"'.JText::_('Tuesday').'"',
			'"'.JText::_('Wednesday').'"',
			'"'.JText::_('Thursday').'"',
			'"'.JText::_('Friday').'"',
			'"'.JText::_('Saturday').'"',
			'"'.JText::_('Sunday').'"'
		);

		// Build the short day names array.
		$shortDayNames = array(
			'"'.JText::_('Sun').'"',
			'"'.JText::_('Mon').'"',
			'"'.JText::_('Tue').'"',
			'"'.JText::_('Wed').'"',
			'"'.JText::_('Thu').'"',
			'"'.JText::_('Fri').'"',
			'"'.JText::_('Sat').'"',
			'"'.JText::_('Sun').'"'
		);

		// Build the month names array.
		$monthNames = array(
			'"'.JText::_('January').'"',
			'"'.JText::_('February').'"',
			'"'.JText::_('March').'"',
			'"'.JText::_('April').'"',
			'"'.JText::_('May').'"',
			'"'.JText::_('June').'"',
			'"'.JText::_('July').'"',
			'"'.JText::_('August').'"',
			'"'.JText::_('September').'"',
			'"'.JText::_('October').'"',
			'"'.JText::_('November').'"',
			'"'.JText::_('December').'"'
		);

		// Build the short month names array.
		$shortMonthNames = array(
			'"'.JText::_('January_short').'"',
			'"'.JText::_('February_short').'"',
			'"'.JText::_('March_short').'"',
			'"'.JText::_('April_short').'"',
			'"'.JText::_('May_short').'"',
			'"'.JText::_('June_short').'"',
			'"'.JText::_('July_short').'"',
			'"'.JText::_('August_short').'"',
			'"'.JText::_('September_short').'"',
			'"'.JText::_('October_short').'"',
			'"'.JText::_('November_short').'"',
			'"'.JText::_('December_short').'"'
		);

		// Build the script.
		$i18n = array(
			'// Calendar i18n Setup.',
			'Calendar._FD = 0;',
			'Calendar._DN = new Array ('.implode(', ', $dayNames).');',
			'Calendar._SDN = new Array ('.implode(', ', $shortDayNames).');',
			'Calendar._MN = new Array ('.implode(', ', $monthNames).');',
			'Calendar._SMN = new Array ('.implode(', ', $shortMonthNames).');',
			'',
			'Calendar._TT = {};',
			'Calendar._TT["INFO"] = "'.JText::_('About the calendar').'";',
			'Calendar._TT["PREV_YEAR"] = "'.JText::_('Prev. year (hold for menu)').'";',
			'Calendar._TT["PREV_MONTH"] = "'.JText::_('Prev. month (hold for menu)').'";',
			'Calendar._TT["GO_TODAY"] = "'.JText::_('Go Today').'";',
			'Calendar._TT["NEXT_MONTH"] = "'.JText::_('Next month (hold for menu)').'";',
			'Calendar._TT["NEXT_YEAR"] = "'.JText::_('Next year (hold for menu)').'";',
			'Calendar._TT["SEL_DATE"] = "'.JText::_('Select date').'";',
			'Calendar._TT["DRAG_TO_MOVE"] = "'.JText::_('Drag to move').'";',
			'Calendar._TT["PART_TODAY"] = "'.JText::_('(Today)').'";',
			'Calendar._TT["DAY_FIRST"] = "'.JText::_('Display %s first').'";',
			'Calendar._TT["WEEKEND"] = "0,6";',
			'Calendar._TT["CLOSE"] = "'.JText::_('Close').'";',
			'Calendar._TT["TODAY"] = "'.JText::_('Today').'";',
			'Calendar._TT["TIME_PART"] = "'.JText::_('(Shift-)Click or drag to change value').'";',
			'Calendar._TT["DEF_DATE_FORMAT"] = "'.JText::_('%Y-%m-%d').'";',
			'Calendar._TT["TT_DATE_FORMAT"] = "'.JText::_('%a, %b %e').'";',
			'Calendar._TT["WK"] = "'.JText::_('wk').'";',
			'Calendar._TT["TIME"] = "'.JText::_('Time:').'";',
			'',
			'"Date selection:\n" +',
			'"- Use the \xab, \xbb buttons to select year\n" +',
			'"- Use the " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " buttons to select month\n" +',
			'"- Hold mouse button on any of the above buttons for faster selection.";',
			'',
			'Calendar._TT["ABOUT_TIME"] = "\n\n" +',
			'"Time selection:\n" +',
			'"- Click on any of the time parts to increase it\n" +',
			'"- or Shift-click to decrease it\n" +',
			'"- or click and drag for faster selection.";',
			''
		);

		return implode("\n", $i18n);
	}
}