/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * License: GPL3
 */

;(function ($, window, document) {
	
	'use strict';

	$.widget("anahita.composer", {
	
		options : {
			composerTab : '.tab-content-item',
			formPlaceholder : 'a.form-placeholder',
			composerForm : '.composer-form',
			composerMenu : '.dropdown-menu li',
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
				composer.slideUp();
		});
	}
	
}(jQuery, window, document));