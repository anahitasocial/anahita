(function(){   
    var uploader = Object.clone(Behavior.getFilter('Uploader'));
    Behavior.addGlobalFilters({
        'PhotoUploader': uploader.config
    });
    Behavior.setFilterDefaults('PhotoUploader', {
        'filters'    : [{title : "Image files", extensions : "jpg,gif,png,jpeg"}]
    });
    Behavior.addGlobalPlugin("PhotoUploader", "PhotoUploader.Plugin", {
        setup: function(element, api, instance) 
        {
            var photos = [];
            var url    = api.get('url');
            instance.addEvents({
                'onInit' : function() {
                    
                },
                'onUploadFile'    : function(file)
                {
                    element.getElements('button').addClass('disabled');
                    element.spin();
                },      
                'onError'         : function(error)
                {
                    
                },             
                'onUploadComplete' : function(files)
                {
                    var ids = files.map(function(file){
                       return 'photo_id[]='+file.resp.id; 
                    });
                    url += '&' + ids.join('&');
                    new Request.HTML({
                        url         : url,
                        update      : element,
                        onSuccess   : element.unspin.bind(element)
                    }).get();                    
                },             
                'onFileUploaded'   : function(file, resp) 
                {
                    file.resp = JSON.decode(resp.response);
                }
            });
            
            instance.init();
            
			var privacySelector = element.getElement('#photo-privacy-selector');
             
            if ( privacySelector ) 
            {
            	instance.addEvents({
	                'onFilesRemoved'  : function(files)
	                {         
	                	if ( instance.total.queued <= 1 && privacySelector ) {
	                		privacySelector.hide();
	                	}
	                },
	                'onFilesAdded'    : function(files)
	                {
	                   element.getElement('#photo-privacy-selector').show();
	                }
            	});            	
            	privacySelector.getElement('select').addEvent('change', function(){
            		instance.options.data.access = this.value;
					console.log(instance.options.data);            		
            	});
            }
        }
    });    
})();