/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * License: GPL3
 * 
 * @todo form verification
 * @todo file upload
 * @todo connect link checkboxes
 */

;(function ($, window, document) {
	
	'use strict';
	
	//Composer Form Plugin
	$.fn.composerform = function (){
		
		var options = $.extend( {}, $.fn.composerform.defaults, options );
		
		if($(options.connect.length))
		{
			$(options.connectLink).tooltip({
				placement : 'top',
				animation : true,
				trigger : 'hover'
			});
		}	
		
		if(this.attr('enctype') == 'multipart/form-data')
		{
			//file upload code goes here
			console.log('file upload will be happening');
		}
		else
		{
			var form = $(options.form);
			
			form.on("click", "button[type='submit']", function(event){
				event.preventDefault();
			
				$.ajax({
					url : form.attr('action'),
					data : form.serialize() + '&composed=true',
					type : 'POST',
					success : function (html) {
						form.trigger('reset');
						$(options.stories).prepend($(html).fadeIn('slow'));
					}.bind(form)
				});
			});
		}
		
	};
	
	//Composer Form Plugin Defaults
	$.fn.composerform.defaults = {
		    form : "form.composer-form",
			connect : ".connect",
			connectLink: "a.connect-link",
			stories : "#an-stories"
		};

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
					event.preventDefault();
					this.showTabContent($(event.delegateTarget).index());
				}
			});
			
			//click outside composer area to hide composer form
			this._on({
				elem: $(this),
				clickoutside : function(event){
					event.preventDefault();
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
				$.ajax({
					url : tab.data('url'),
					success : function(data){
						tab.append(data);
						
						$(tab).find('form.composer-form').composerform();
						
						tab.data('content', tab.find(this.options.composerForm));
						
						if(this.firstTime){
							this.firstTime = false;
							this.hideTabContent(this.currentTabIndex);
						}
						
					}.bind(this)
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
	
	var composer = $("[data-behavior='Composer']").composer();
	var streamTabs = $('ul.streams');

	if(streamTabs.length){	
		streamTabs.on('click', 'li', function(event){
			
			if($( this ).data('stream') == 'stories')
				composer.fadeIn();
			else
				composer.fadeOut();
		});
	}
	
}(jQuery, window, document));