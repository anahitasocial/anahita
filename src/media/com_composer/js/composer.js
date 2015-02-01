/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * License: GPL3
 * 
 * @todo connect link checkboxes
 */

;(function ($, window, document) {
	
	'use strict';
	
	//Composer Form Widget
	$.widget("anahita.composerform", {
		
		options : {
			stories : "#an-stories",
			form : '.composer-form'
		},
		
		_create : function() {

			var form = $(this.element);
			
			if(form.attr('enctype') === 'multipart/form-data'){
				
				var formData = new FormData(form[0]);
				formData.append('composed', true);
				formData.append('format', 'raw');
				
				this._on(form, {
					submit: function(event){
						event.stopPropagation();
						event.preventDefault();
						
						$.each(form.serializeArray(), function(i, obj){
							formData.append(obj.name, obj.value);	
						});
						
						$.each(form.find(':file')[0].files, function(i, file) {
							formData.append('file', file);
				        });
						
						$.ajax({
							url : form.attr('action'),
							processData: false, 
				            contentType: false,
							cache: false,
							data : formData,
							type : 'POST',
							success : function (html) {
								$(this.element).trigger('reset');
								$(this.options.stories).prepend($(html).fadeIn('slow'));
							}.bind(this)
						});
					}
				});
				
			}else{

				this._on(form, {
					submit: function(event){
						event.stopPropagation();
						event.preventDefault();	
						
						$.ajax({
							url : form.attr('action'),
							data : form.serialize() + '&composed=1',
							cache: false,
							type : 'POST',
							success : function (html) {
								$(this.element).trigger('reset');
								$(this.options.stories).prepend($(html).fadeIn('slow'));
							}.bind(this)
						});
					}	
				});
			
			}
		}
	});

	//Composer Widget
	$.widget("anahita.composer", {
	
		options : {
			composerTab : '.tab-content-item',
			formPlaceholder : 'a.form-placeholder',
			composerForm : '.composer-form',
			composerMenu : '#composer-menu li',
			composerMenuTitle : '.composer-button-title'
		},
		
		_create : function() {
			
			this.firstTime = true;
			this.currentTabIndex = 0;
			
			this.tabs = this.element.find(this.options.composerTab);
			var formPlaceholder = this.options.formPlaceholder;
			
			this.tabs.each(function(index, tab){	
				$(tab).data('placeholder', $(tab).find(formPlaceholder));
			});
			
			//composer dropdown menu
			this._on($(this.options.composerMenu), {
				click : function(event){
					event.preventDefault();
					var selected = $(event.delegateTarget);
					$(this.options.composerMenuTitle).text(selected.find('a').attr('title'));
					this.selectTab(selected.index());
				}
			});
			
			//click on placeholder to show composer form
			this._on($(this.options.composerTab), {
				elem : $(this.options.formPlaceholder),
				click : function(event){
					event.stopPropagation();
					this.showTabContent($(event.delegateTarget).index());
				}
			});
			
			//click outside composer area to hide composer form
			this._on({
				elem: $(this),
				clickoutside : function(event){
					this.hideTabContent(this.currentTabIndex);
				}
			});
			
			this.selectTab(this.currentTabIndex);
		},
		
		selectTab : function(index){
			
			$(this.tabs[this.currentTabIndex]).hide();
			
			this.currentTabIndex = index;
			var tab = $(this.tabs[this.currentTabIndex]);
			
			if(!tab.data('content'))
			{	
				$(this).load(tab.data('url'), function(data){
					tab.append(data);
					$(tab).find('form.composer-form').composerform();
					tab.data('content', tab.find(this.options.composerForm));
					
					if(this.firstTime){
						this.firstTime = false;
						this.hideTabContent(this.currentTabIndex);
					}
				});
			}
			
			if(!this.firstTime){
				this.showTabContent(this.currentTabIndex);
			}
				
			tab.fadeIn();
			
			return this;
		},
		
		showTabContent : function(index){
			var tab = $(this.tabs[index]);
			$(tab.data('placeholder')).hide();
			$(tab.data('content')).fadeIn();
		},
		
		hideTabContent : function(index){	
			var tab = $(this.tabs[index]);	
			$(tab.data('content')).hide();
			$(tab.data('placeholder')).fadeIn();
		}
	});	
	
	//initiate composer widget
	var composer = $("[data-behavior='Composer']").composer();
	
	//show composer only for the stories stream
	var streamTabs = $('ul.streams');
	
	if(streamTabs.length){	
		streamTabs.on('click', 'li', function(event){
			
			if($( this ).data('stream') == 'stories')
				composer.fadeIn();
			else
				composer.fadeOut();
		});
	}
	
	//add tooltips to the connect links if they exist
	$(document).ajaxSuccess(function( event, request, settings ) {	
		
		var connectLinks = $('a.connect-link');
		
		if(connectLinks.length){
			$(connectLinks).tooltip({
				placement : 'top',
				animation : true,
				trigger : 'hover'
			});
		}
	}); 
	
}(jQuery, window, document));