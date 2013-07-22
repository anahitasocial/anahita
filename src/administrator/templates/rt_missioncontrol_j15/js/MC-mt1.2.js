/**
 * @package MissionControl Admin Template - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

(function(){
	
	var MC = this.MC = {
		
		init: function(){
			if (this.MC.Notice) this.MC.Notice.shake.delay(500, this.MC.Notice.shake, 3);
			SelectBoxes.init();
			MC.fixIOS();
			MC.QCC();
			MC.QCheckins();
		},
		
		fixIOS: function(){
			var menu = document.id('mctrl-menu');
			if (menu){
				var children = menu.getElements('li');
				if (children.length){
					children.addEvent('mouseenter', function(e){ new Event(e).stop(); });
				}
			}
		},
		
		
		
		QCC: function(){
			var qccs = $$('.qcc');
			qccs.each(function(qcc){
				qcc.store('value', qcc.getElement('a').get('text'));
				qcc.store('badge', qcc.getElement('.badge'));
				qcc.store('ajax', new Request({
					url: 'index.php?process=ajax&model=quickcachecleaner&action=clean',
					onRequest: function(response){
						MC.QCCRequest(this, qcc, response);
					},
					onSuccess: function(response){
						MC.QCCSuccess(this, qcc, response);
					}
				}));
				qcc.addEvent('click', function(e){
					e.stop();
					var ajax = this.retrieve('ajax');
					
					if (!ajax.running) ajax.send();
				});
			});
		},
		
		QCCRequest: function(ajax, qcc, response){
			qcc.getElement('a').set('text', 'Cleaning Cache...');
		},

		QCCSuccess: function(ajax, qcc, response){
			qcc.getElement('a').set('text', qcc.retrieve('value'));
			qcc.retrieve('badge').set('text', response);
		},
		
		QCheckins: function(){
			var qcis = $$('.qci');
			qcis.each(function(qci){
				qci.store('value', qci.getElement('a').get('text'));
				qci.store('badge', qci.getElement('.badge'));
				qci.store('ajax', new Request({
					url: 'index.php?process=ajax&model=quickcheckin&action=checkin',
					onRequest: function(response){
						MC.QCheckinsRequest(this, qci, response);
					},
					onSuccess: function(response){
						MC.QCheckinsSuccess(this, qci, response);
					}
				}));
				qci.addEvent('click', function(e){
					e.stop();
					var ajax = this.retrieve('ajax');
					
					if (!ajax.running) ajax.send();
				});
			});
		},
		
		QCheckinsRequest: function(ajax, qci, response){
			qci.getElement('a').set('text', 'Cleaning Checkins...');
		},

		QCheckinsSuccess: function(ajax, qci, response){
			qci.getElement('a').set('text', qci.retrieve('value'));
			qci.retrieve('badge').set('text', response);
		}
		
	};
	
	
	var SelectBoxes = this.MC.SelectBoxes = {
		
		init: function(){
			this.selects = $$('.dropdown select');
			
			this.selects.each(function(sel){
				sel.getParent().addEvent('mouseenter', function(e) {e.stop();});
				this.build(sel);
			}, this);
		},
		
		build: function(sel){
			var selected = new Element('a', {'class': 'mc-dropdown-selected'}).inject(sel, 'before');
			var list = new Element('ul', {'class': 'mc-dropdown'}).inject(selected, 'after');
			
			sel.setStyle('display', 'none');
			
			sel.getChildren().each(function(option, i){
				var active = option.get('selected') || false;
				var lnk = new Element('a', {'href': '#'}).set('text', option.get('text'));
				var opt = new Element('li').inject(list).adopt(lnk);
				
				opt.addEvent('click', function(e){
					e.stop();

					sel.selectedIndex = i;
					selected.getFirst().set('text', option.get('text'));
					
					sel.fireEvent('change');
				});
				
				opt.store('selected', active);
				opt.store('value', option.get('value') || '');
				
				if (active) selected.set('html', '<span class="select-active">' + option.get('text') + '</span>');
			});
			
			var arrow = new Element('span', {'class': 'select-arrow'}).set('html', '&#x25BE;').inject(selected);
		}
		
	};
	

	window.addEvent('domready', MC.init);
	
})();