/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
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

						event.preventDefault();
						
						form.trigger('beforeSubmit');
						
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
							data : formData,
							method : 'post',
							beforeSend : function () {
                            
                                form.find(':submit').button('loading');
                            
                            },
							success : function (html) {
							
								$(this.element).trigger('reset');
								$(this.options.stories).prepend($(html).fadeIn('slow'));
							
							}.bind(this),
                            complete : function () {
                               form.find(':submit').button("reset");
                            }
						});
					}
				});
				
			} else {

				this._on( form, {
					
					submit : function( event ){
					
						event.preventDefault();	
						
						form.trigger('beforeSubmit');
						
						$.ajax({
							
							url : form.attr('action'),
							data : form.serialize() + '&composed=1',
							method : 'post',
							beforeSend : function () {
							
							    form.find(':submit').button('loading');
							
							},
							success : function (html) {
							
								$(this.element).trigger('reset');
								$(this.options.stories).prepend($(html).fadeIn('slow'));
							
							}.bind(this),
                            complete : function () {
                               form.find(':submit').button("reset");
                            }
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
			
			this.tabs.each(function(index, tab) {	
				$(tab).data( 'placeholder', $(tab).find(formPlaceholder) );
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
			
			var self = this;
			
			$(this.tabs[this.currentTabIndex]).hide();
			
			this.currentTabIndex = index;
			
			var tab = $(this.tabs[this.currentTabIndex]);
			
			if(!tab.data('content'))
			{	
				
				$.ajax({
				    
				    method: 'get',
				    url : tab.data('url'),
				    beforeSend : function () {
				        
				        if ( !self.firstTime ) {
				           self.element.fadeTo('fast', 0.8).addClass('uiActivityIndicator'); 
				        }
				        
				    },
				    success : function ( response ) {
				        
				        tab.append(response);
				        
                        $(tab).find('form.composer-form').composerform();
                        
                        tab.data('content', tab.find(self.options.composerForm));
                        
                        if ( self.firstTime ) {
                            
                            self.firstTime = false;
                            self.hideTabContent(self.currentTabIndex);
                        }
				    },
				    complete : function () {
				        
				        self.element.fadeTo('fast', 1).removeClass('uiActivityIndicator');
				    }
				});
			}
			
			if ( !this.firstTime ) {
				this.showTabContent( this.currentTabIndex );
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
	if ( $("[data-behavior='Composer']").length ) {
	   
	   var composer = $("[data-behavior='Composer']").composer(); 
	   
	   composer.hide();
	   
	   //show composer only for the stories stream
        var streamTabs = $('ul.streams');
        
        if ( streamTabs.length ) {  
            
            streamTabs.on('click', 'li', function(event){
                
                if ( $( this ).data('stream') == 'stories') {
                     composer.fadeIn();
                } else {
                     composer.fadeOut(); 
                }
                    
            });
        }
	}
	
	//add tooltips to the connect links if they exist
	$(document).ajaxSuccess(function( event, request, settings ) {	
		
		var connectLinks = $('a.connect-link');
		
		if ( connectLinks.length && !connectLinks.is(":data('tooltip')") ) {
		
			$(connectLinks).tooltip( {
				
				placement : 'top',
				animation : true,
				trigger : 'hover'
			});
		}
	}); 
	
}(jQuery, window, document));