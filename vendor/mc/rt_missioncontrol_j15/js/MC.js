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
			var menu = $('mctrl-menu');
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
				qcc.QCCValue = qcc.getElement('a').getText();
				qcc.QCCBadge = qcc.getElement('.badge');
				qcc.QCCAjax = new Ajax('index.php?process=ajax&model=quickcachecleaner&action=clean', {
					onRequest: function(response){
						MC.QCCRequest(this, qcc, response);
					},
					onSuccess: function(response){
						MC.QCCSuccess(this, qcc, response);
					}
				});
				qcc.addEvent('click', function(e){
					new Event(e).stop();
					var ajax = qcc.QCCAjax;
					
					if (!ajax.running) ajax.request();
				});
			});
		},
		
		QCCRequest: function(ajax, qcc, response){
			qcc.getElement('a').setText('Cleaning Cache...');
		},

		QCCSuccess: function(ajax, qcc, response){
			qcc.getElement('a').setText(qcc.QCCValue);
			qcc.QCCBadge.setText(response);
		},
		
		QCheckins: function(){
			var qcis = $$('.qci');
			qcis.each(function(qci){
				qci.QCCValue = qci.getElement('a').getText();
				qci.QCCBadge = qci.getElement('.badge');
				qci.QCCAjax = new Ajax('index.php?process=ajax&model=quickcheckin&action=checkin', {
					onRequest: function(response){
						MC.QCCRequest(this, qci, response);
					},
					onSuccess: function(response){
						MC.QCCSuccess(this, qci, response);
					}
				});
				qci.addEvent('click', function(e){
					new Event(e).stop();
					var ajax = qci.QCCAjax;
					
					if (!ajax.running) ajax.request();
				});
			});
		},
		
		QCheckinsRequest: function(ajax, qci, response){
			qci.getElement('a').setText('Cleaning Checkins...');
		},

		QCheckinsSuccess: function(ajax, qci, response){
			qci.getElement('a').setText(qci.QCCValue);
			qci.QCCBadge.setText(response);
		}
		
	};
	
	
	var SelectBoxes = this.MC.SelectBoxes = {
		
		init: function(){
			this.selects = $$('.dropdown select');
			
			this.selects.each(function(sel){	
				sel.getParent().addEvent('mouseenter', function(e) {new Event(e).stop();});
				this.build(sel);
			}, this);
		},
		
		build: function(sel){
			var selected = new Element('a', {'class': 'mc-dropdown-selected'}).inject(sel, 'before');
			var list = new Element('ul', {'class': 'mc-dropdown'}).inject(selected, 'after');
			
			sel.setStyle('display', 'none');
			
			sel.getChildren().each(function(option, i){
				var active = option.getProperty('selected') || false;
				var lnk = new Element('a', {'href': '#'}).setText(option.getText());
				var opt = new Element('li').inject(list).adopt(lnk);
				
				opt.addEvent('click', function(e){
					new Event(e).stop();

					sel.selectedIndex = i;
					selected.getFirst().setText(option.getText());
					
					sel.fireEvent('change');
				});
				
				opt['mcselected'] = active;
				opt['mcvalue'] = option.getValue() || '';
				
				if (active) selected.setHTML('<span class="select-active">' + option.getText() + '</span>');
			});
			
			var arrow = new Element('span', {'class': 'select-arrow'}).setHTML('&#x25BE;').inject(selected);
		}
		
	};
	

	window.addEvent('domready', MC.init);
	
})();