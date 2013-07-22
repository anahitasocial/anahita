/**
 * @package RokQuickLinks - RocketTheme
 * @version 1.5.0 September 1, 2010
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

(function(){
	var bounds = {
		'add': function(){
			var block = this.getParent().getParent();
			var clone = block.clone(true);
			
			$$('.jpane-slider').setStyle('height', '');	
			
			var sort = QuickLinks.sortables;
			sort.detach();
			sort.elements.push(clone);
			sort.handles.push(clone.getElement('.quicklinks-move'));
			sort.bound.start.push(sort.start.bindWithEvent(sort, sort.elements[sort.elements.length - 1]));
			sort.attach();
			
			var select = clone.getElement('select');
			select.addEvent('change', bounds.selects.change);
			select.addEvent('blur', bounds.selects.blur);
			select.getChildren().addEvent('mouseenter', bounds.opts);
			clone.getElement('.quicklinks-add').addEvent('click', bounds.add);
			clone.getElement('.quicklinks-remove').addEvent('click', bounds.remove);
			
			clone.inject(block, 'after');
			QuickLinks.store();
		},
		
		'remove': function(){
			var block = this.getParent().getParent();
			
			//QuickLinks.sortables.removeItems(block);
			
			block.remove();
			QuickLinks.store();
		},
		
		'selects': {
			'change': function(){
				this.getParent().getParent().getElement('img').setProperty('src', QuickLinks.path + this.value);
				QuickLinks.store();
			},
			
			'blur': function(){
				this.getParent().getParent().getElement('img').setProperty('src', QuickLinks.path + this.value);
			}
		},
		
		'opts': function(){
			this.getParent().getParent().getParent().getElement('img').setProperty('src', QuickLinks.path + this.value);
		},
		
		'inputs': function(){
			QuickLinks.store();
		}
	};
	
	var QuickLinks = {
		init: function(){
			var quicklinks = $('quicklinks-admin');
			if (!quicklinks) return;
			
			QuickLinks.sortables = new Sortables(quicklinks, {
				handles: $$('.quicklinks-move'), 
				opacity: 0.5, 
				ghost: false,
				onStart: function(element, ghost){
					element.setStyle('opacity', 0.5);
				},
				onComplete: function(element){
					element.setStyle('opacity', 1);
					QuickLinks.store();
				}
			});
			QuickLinks.path = $('quicklinks-dir').value;
			
			var adds = $$('.quicklinks-add'),
				removes = $$('.quicklinks-remove'),
				selects = $$('.quicklinks-select'),
				inputs = $$('.quick-input'),
				options = selects.getChildren();
			
			adds.each(function(add){
				add.addEvent('click', bounds.add);
			});
			
			removes.each(function(remove){
				remove.addEvent('click', bounds.remove);
			});
			
			selects.each(function(select){
				select.addEvents({
					'change': bounds.selects.change,
					'blur': bounds.selects.blur
				});
			});
			
			options.each(function(option){
				option.addEvent('mouseover', bounds.opts);
			});
			
			inputs.each(function(input){
				input.addEvent('keyup', bounds.inputs);
			});
			
		},
		
		order: function(){
			var blocks = $$('.quicklinks-block'),
				list = ['title', 'link', 'icon'];

			blocks.each(function(block, i){
				var fields = block.getElements('input');
				fields.push(block.getElement('select'));
					
				fields.each(function(input, j){
					input.id = 'params' + list[j] + '-' + (i + 1);
					input.name = 'params['+list[j] + '-' + (i + 1) +']';
				});
			});
		},
		
		store: function(){
			QuickLinks.order();
			var blocks = $$('.quicklinks-block'),
				json = [],
				list = ['title', 'link', 'icon'];
				
			blocks.each(function(block, i){
				var fields = block.getElements('input');
				fields.push(block.getElement('select'));
				
				var obj = {};
				fields.each(function(input, j){
					obj[list[j]] = input.getValue();
				});
				
				json.push(obj);
			});
			
			$('paramsquickfields').value = Json.toString(json);
		}
		
	};
	
	
	window.addEvent('domready', QuickLinks.init);
	
})();