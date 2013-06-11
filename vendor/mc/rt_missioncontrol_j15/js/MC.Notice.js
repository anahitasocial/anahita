/**
 * @package MissionControl Admin Template - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

(function(){

Fx.Transitions.extend({
	Shake: function(x){
		return Math.sin(x*3*2*Math.PI);
	}
});

var Notice = {
	
	effect: function(){
		var error = $$('.message')[0];
		if (!error) return false;
		
		return new Fx.Style(error, 'margin-left', {duration: 400});
	},
	
	shake: function(times){
		var fx = (!Notice.fx) ? Notice.effect() : Notice.fx;
		if (!fx) return;
		
		fx.setOptions({transition: Fx.Transitions.Shake, duration: 400});

		var margin = fx.element.getStyle('margin-left').toInt();
		fx.start([margin + 5, margin]);
	}
	
};

if (!this.MC) this.MC = {};
this.MC.Notice = Notice;

})();