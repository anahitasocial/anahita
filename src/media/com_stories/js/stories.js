(function(){
	var openedForms = [];
	document.addEvent('click', function(e) {		
		openedForms.each(function(form) {
			if( !$(e.target).getParents().contains(form) ) { 
				var hotspot = form.retrieve('hotspot');
				if ( hotspot ) {					
					hotspot.show();
				}
				form.hide();
				openedForms.erase(form);
			}
		});
	});
	
	var open  = function(event, link) {
		
		event.stop();
		
		var form = document.getElement('#story-comments-' + link.get('storyid') + ' form');
		
		if ( !form )
				return;
		
		var hotspot = link.hasClass('action-comment-overtext') ? link.getParent() : document.getElement('#story-comments-' + link.get('storyid') + ' .comment-overtext-box');
		
		if ( hotspot ) {
			hotspot.hide();
			form.store('hotspot', hotspot);
		}
		
		openedForms.include(form);
		
		form.show();
		
		form.getElement('textarea').focus();
		
		var scroll = new Fx.Scroll(window,{
			offset:{x:0,y:-200}
		}).toElement(form);			
	}
	
	'*[data-trigger="ViewSource"]'.addEvent('domready', function() {
		this.set('html', this.get('alt'));
		this.removeClass('pull-right');
	});
	
	document.addEvents({
        'click:relay(.an-actions .comment)' : open,
        'click:relay(.comment-overtext-box .action-comment-overtext)' : open                
	});
	
}).apply();

Delegator.register(['click'], 'Share', function(event, el, api) {
	event.stop();
	var textarea = el.form.getElement('textarea');
	//before sumbmitting validate min and max leng of text area
	//if ok then submit 	 
	textarea.value = textarea.value.stripTags();
	if ( el.form.get('validator').validateField(textarea) && textarea.value.match(/^(?!\s*$).+/) ) {
		window.delegator.trigger('Request', el, event);
		el.form.reset();
	}
});
