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
		'click:relay(.action-comment-overtext)' : open,
		'click:relay(.connect-service-share a)' : function(e){
		    e.stop();
		    e.target.toggleClass('selected');
		    e.target.getElement('+input').set('checked', e.target.hasClass('selected'));
		}
	});
}).apply();

/*
Behavior.addGlobalFilter('ShowMoreComments',{
	setup : function(el, api) {
		var comments = el.getElements('.an-comment');
	}
});
*/

Delegator.register(['click'], 'Share', function(event, el, api) {
	event.stop();
	var textarea = el.form.getElement('textarea');
	//before sumbmitting validate min and max leng of text area
	//if ok then submit 	 
	if ( el.form.get('validator').validateField(textarea) && textarea.value.match(/^(?!\s*$).+/) ) {
		window.delegator.trigger('Request', el, event);
		textarea.value = '';
		el.form.getElements('input').set('checked', false);
		el.form.getElements('.connect-service').removeClass('selected');
	}
});
