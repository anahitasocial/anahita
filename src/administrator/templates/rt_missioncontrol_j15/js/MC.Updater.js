/**
 * @package MissionControl Admin Template - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

(function(){

var Updater = {
	
	init: function(){
		var updaters = $$('.mc-update a');
		
		Updater.ajax = new Ajax('index.php?process=ajax&model=updater', {onRequest: Updater.request, onSuccess: Updater.success});
		
		updaters.each(Updater.addAjax);
	},
	
	addAjax: function(updater){
		updater.addEvent('click', function(e){
			new Event(e).stop();
			if (Updater.ajax.running) return;
			
			var spinner = this.getNext();
			if (!spinner.hasClass('spinner')) spinner = this.getPrevious();
			if (!spinner.hasClass('spinner')) spinner = null;
			
			spinner.setStyle('display', 'block');
			
			Updater.ajax.spinner = spinner;
			Updater.ajax.request();
		});
	},
	
	request: function(){},
	
	success: function(r){
		if (Updater.ajax.spinner) Updater.ajax.spinner.setStyle('display', 'none');
		Updater.ajax.spinner = null;
		
		var tmp = new Element('div').setHTML(r);
		tmp = tmp.getFirst();
		var box = $(document.body).getElement('.mc-update-check');
		
		// updating classname
		box.className = tmp.className;
		
		// updating content
		box.innerHTML = tmp.innerHTML;
		
		// attaching click event
		Updater.addAjax(box.getElement('a'));
	}
	
};

if (!this.MC) this.MC = {};
this.MC.Updater = Updater;

window.addEvent('domready', Updater.init);

})();