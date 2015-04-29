/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function($, window) {
    
    'use strict';
    
    Dropzone.autoDiscover = false;
    
    //multiple file uploader
    $.widget('anahita.photoUpload', {
    	    	
    	_create : function(){
    		
    		var self = this;
    		this.uploadedPhotoIds = [];
    		
    		var dropzoneOptions = {
    				
    				sending: function(file, xhr, data) {
    					var access = $(this.element).find('select[name="access"]').val();
    					data.append("access", access);
    				},
    				
    				success : function ( file, obj, xhr ){
    					self.uploadedPhotoIds.push(obj.id);
    				},
    				
    				queuecomplete : function(){

    					if(self.uploadedPhotoIds.length > 0){
    						var url = self.options.setsUrl;
        					
        					$.each(self.uploadedPhotoIds, function(index, value){
        						url += '&photo_id[]=' + value;
        						});

        					$.get(url, function ( response ){
        						$(self.element).html(response);
        					});
    					}
    				}
    			};
    		
    		dropzoneOptions = $.extend({}, this.options, dropzoneOptions);
    		
    		this.dropzone = new Dropzone(this.options.filedrop, dropzoneOptions);
    		
    		//upload photos
    		this._on(this.element, {
    			'click [data-trigger="UploadPhotos"]' : function ( event ) {
    				event.preventDefault();
    				this.dropzone.enqueueFiles(this.dropzone.getFilesWithStatus(Dropzone.ADDED));
    			} 
    		});
    		
    		//remove photos
    		this._on(this.element, {
    			'click [data-trigger="RemovePhotos"]' : function ( event ) {
    				event.preventDefault();
    				this.dropzone.removeAllFiles(true);
    			} 
    		});
    	}
    });
    
    //Photos to set assignment
    $.widget('photos.photosSetAssignment',{
    	
    	_create : function () {
    		
    		var textField = $(this.element).find('input[name="title"]');
    		var selector = $(this.element).find('select[name="id"]');
    		
    		this._on(selector, {
    			'change' : function ( event ) {
    				if($(event.target).val() != '' )
    					textField.attr('disabled', true);
    				else
    					textField.attr('disabled', false);
    			}
    		});
    		
    		this._on(textField, {
    			'change' : function ( event ) {
    				if($(event.target).val() != '' )
    					selector.attr('disabled', true);
    				else
    					selector.attr('disabled', false);
    			}
    		});
    	}
    	
    });
    
    $(document).ajaxSuccess( function() {
    	$('#photos-set-assignment').photosSetAssignment();
    });

}(jQuery, window));    