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
class JElementColorChooser extends JElement {


	function fetchElement($name, $value, &$node, $control_name)
	{
        global $mctrl;
        $mctrl =& MissionControl::getInstance();
        
		$output = '';

        $doc =& JFactory::getDocument();

		$transparent = 1;

		if ($node->attributes('transparent') == 'false') $transparent = 0;


		if (!defined('MC_MOORAINBOW')) {

			$doc->addStyleSheet($mctrl->templateUrl.'/elements/colorchooser/css/mooRainbow.css');
			$doc->addScript($mctrl->templateUrl.'/elements/colorchooser/js/mooRainbow'.$this->getJSVersion().'.js');

			//$scriptconfig  = $this->populateStyles($stylesList);
			$scriptconfig = $this->rainbowInit();

			$doc->addScriptDeclaration($scriptconfig);

			define('MC_MOORAINBOW',1);
		}

		$scriptconfig = $this->newRainbow($name, $transparent);

		$doc->addScriptDeclaration($scriptconfig);

		$output .= "<div class='wrapper'>";
		$output .= "<input class=\"picker-input text-color\" id=\"".$control_name.$name."\" name=\"".$control_name."[".$name."]\" type=\"text\" size=\"7\" maxlength=\"11\" value=\"".$value."\" />";
		$output .= "<div class=\"picker\" id=\"myRainbow_".$name."_input\"><div class=\"overlay".(($value == 'transparent') ? ' overlay-transparent' : '')."\"><div></div></div></div>\n";
		$output .= "</div>";
		//$output = false;

		return $output;
	}

	function newRainbow($name, $transparent)
	{
        global $mctrl;
        
        $name2 = str_replace("-", "_", $name);

		$mt = true;
		$dollar = $mt ? "$" : "document.id";
		$getValue = $mt ? "getValue()" : "get('value')";

		return "
		var r_".$name2.";
		window.addEvent('domready', function() {
			var input = ".$dollar."('params".$name."');
			r_".$name2." = new MooRainbow('myRainbow_".$name."_input', {
				id: 'myRainbow_".$name."',
				startColor: $('params".$name."').".$getValue.".hexToRgb(true) || [255, 255, 255],
				imgPath: '".$mctrl->templateUrlAbsolute."/elements/colorchooser/images/',
				transparent: ".$transparent.",
				onChange: function(color) {
					if (color == 'transparent') {
						input.getNext().getFirst().addClass('overlay-transparent').setStyle('background-color', 'transparent');
						input.value = 'transparent';
					}
					else {
						input.getNext().getFirst().removeClass('overlay-transparent').setStyle('background-color', color.hex);
						input.value = color.hex;
					}

					if (this.visible) this.okButton.focus();

				}
			});

			r_".$name2.".okButton.setStyle('outline', 'none');
			".$dollar."('myRainbow_".$name."_input').addEvent('click', function() {
				(function() {r_".$name2.".okButton.focus()}).delay(10);
			});
			input.addEvent('keyup', function(e) {
				if (e) e = new Event(e);
				if ((this.value.length == 4 || this.value.length == 7) && this.value[0] == '#') {
					var rgb = new Color(this.value);
					var hex = this.value;
					var hsb = rgb.rgbToHsb();
					var color = {
						'hex': hex,
						'rgb': rgb,
						'hsb': hsb
					}
					r_".$name2.".fireEvent('onChange', color);
					r_".$name2.".manualSet(color.rgb);
				};
			}).addEvent('set', function(value) {
				this.value = value;
				this.fireEvent('keyup');
			});
			input.getNext().getFirst().setStyle('background-color', r_".$name2.".sets.hex);
			rainbowLoad('myRainbow_".$name."');
		});\n";
	}

	function populateStyles($list)
	{

		$mt = true;
		$dollar = $mt ? "$" : "document.id";
		$getValue = $mt ? "getValue()" : "get('value')";

		$script = "
		var stylesList = new Hash({});
		var styleSelected = null;
		window.addEvent('domready', function() {
			styleSelected = ".$dollar."('paramspresetStyle').".$getValue.";
			".$dollar."('paramspresetStyle').empty();\n";

		reset($list);
		while ( list($name) = each($list) ) {
  			$style =& $list[$name];
			$js = "			stylesList.set('$name', ['{$style->pstyle}'";
			$js .= ", '{$style->bgstyle}'";
			$js .= ", '{$style->fontfamily}'";
			$js .= ", '{$style->linkcolor}'";
			$js .= "]);\n";
			$script .= $js;
		}

		$script .= "		});";

		return $script;
	}

	function rainbowInit()
	{

		$mt = true;
		$dollar = $mt ? "$" : "document.id";
		$getValue = $mt ? "getValue()" : "get('value')";

		return "var rainbowLoad = function(name, hex) {
				if (hex) {
					var n = name.replace('params', '');
					".$dollar."(n+'_input').getPrevious().value = hex;
					".$dollar."(n+'_input').getFirst().setStyle('background-color', hex);
				}
			};
		";
	}
	
	function getJSVersion(){
	  if (version_compare(JVERSION, '1.5', '>=') && version_compare(JVERSION, '1.6', '<')) {
	    if (JFactory::getApplication()->get('MooToolsVersion', '1.11') != '1.11') return "-mt1.2";
	    else return "";
	  }
	  else {
	    return "";
	  }
	}
}

?>