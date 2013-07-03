//Asset.javascript('http://www.plupload.com/plupload/js/plupload.full.js');
var Uploader = new Class({
	Implements :[Options,Events],
    options : {
        /*
		onInit: $empty,
		onFilesAdded: $empty,
		onUploadProgress: $empty,
		onError: $empty,
		onFileUploaded: $empty,
		onChunkUploaded : $empty,
		
		*/
		runtimes 	: 'gears,html5,flash,silverlight,browserplus',
		container 	: 'container',
		filters : [
			{extensions : "*"}			
		],
		url          : null,
		data         : {},
		maxFileSize  : '4mb',		
		browseButton : null,
		fileList     : null,		
    },
    initialize : function(options) 
    {
        this.setOptions(options);
        this.fileList = document.id(this.options.fileList);
        
        if ( instanceOf(this.options.container, Element) )
        {
        	var container = this.options.container;
        	container.set('id', container.get('id') || String.uniqueID());
        	this.options.container = container.get('id');
        }
        if ( instanceOf(this.options.browseButton, Element) ) 
        {
        	var btn = this.options.browseButton;
        	btn.set('id', btn.get('id') || String.uniqueID());
        	this.options.browseButton = btn.get('id');
        }

        this.uploader  = new plupload.Uploader({
        	runtimes  	  : this.options.runtimes,
        	container 	  : this.options.container,
        	flash_swf_url : 'http://www.plupload.com/plupload/js/plupload.flash.swf',
        	browse_button : this.options.browseButton,
        	max_file_size : this.options.maxFileSize,
        	url           : this.options.url,
            multipart_params : this.options.data,        	
        	filters		  : this.options.filters
        });

        (function(){
            var uploadEvents = 'BeforeUpload ChunkUploaded Destroy Error FilesAdded FilesRemoved FileUploaded Init PostInit QueueChanged Refresh StateChanged UploadComplete UploadFile UploadProgress'.split(' ');
            var self         = this;
            var addEvent     = this.addEvent;
            var uploader     = this.uploader;
            this.addEvent    = function(name, fn) {
                name = name.replace('on', '');
                if ( uploadEvents.contains(name.capitalize()) ) {
                    uploader.bind(name.capitalize(), function() {
                        fn.attempt(Array.from(arguments).slice(1));
                    });
                }
                else {
                    addEvent.apply(self,arguments);
                }
            }
        }.bind(this))();
        
        this.bound = {
           onFilesAdded     : this._addFilesToList.bind(this),
           onQueueChanged   : this._queueChanged.bind(this)
        }
        this.total  = this.uploader.total;
        this.addEvents(this.bound);
        this.noFileSelected = this.fileList.get('html');
    },
    init    : function()
    {
    	this.uploader.init();
    },
    start   : function()
    {
        var canUpload = this.uploader.files.length > 0;        
        if ( canUpload ) {
            this.uploader.start();
            return true;
        }
        return false;
    }, 
    _addFilesToList : function(files)
    {
        if ( this.uploader.files.length == 0 )
                this.fileList.empty();
        var self = this;
    	files.each(function(file) 
    	{
    		var fileEl = new UploaderFile(file);
    		file.element = fileEl;
    		self.fileList.adopt( fileEl );
    		fileEl._updateProgress();
    		document.id(fileEl).addEvent('click:relay(.close)', function() {
    			self.uploader.removeFile(fileEl.file);
    			var hide = document.id(fileEl).hide.bind(document.id(fileEl));
    			document.id(fileEl).fade().get('tween').chain(hide);
    		});
		});
    },
    _queueChanged : function()
    {        
        if ( this.uploader.files.length == 0 )
            this.fileList.set('html', this.noFileSelected);
    }
    
});

Behavior.addGlobalFilter('Uploader',{
	defaults : {
	    initialize   : false,
		uploadButton : null,
		browseButton : '.select',
		fileList     : '.file-list',
		runtimes     : 'gears,html5,flash,silverlight,browserplus',
	    maxFileSize  : '4mb'
	},
	returns  : Uploader,
	setup    : function(el, api) 
	{        
	   
        var uploadButton = el.getElement(api.get('upload-button'));
        var form    = null;

		var options = {
		    filters         : api.getAs(Array, 'filters') || [],		        
			container       : el,
			emptyMsg        : 'Prompt.noFileSelected'.translate(),
			browseButton    : el.getElement(api.get('browseButton')),
			url             : api.get('url'),
			data            : api.get('data'),
			fileList		: el.getElement(api.get('file-list')),
			runtimes        : api.get('runtimes'),
			maxFileSize     : api.get('max-file-size'),
			onInit          : function(params) {
					
			}
		}  
        if ( api.get('form') ) {
            var form = el.getElement(api.get('form'))
            Object.append(options, {
                url   : form.get('action'),
                data  : form.toQueryString().parseQueryString() 
            });
            if ( !uploadButton )
            {
                form.addEvent('submit', function(e){
                    e.stop();                    
                    instance.start();
                });
            }
        }
		
		if ( uploadButton )
		    uploadButton.addEvent('click', instance.start.bind(instance));
		
		var instance = new Uploader(options);
		el.store('uploader', instance);
		if ( api.getAs(Boolean,'initialize') )
		    instance.init();
		return instance;		        
	}
});

var UploaderFile = new Class({
    Implements : [Options],
    options    : {
        tmpl : '<div class="file"><span class="close">x</span>{name}</div>'
    },
    initialize : function(file, options) 
    {
        this.setOptions(options);
        this.file    = file;
        this.element = this.options.tmpl.substitute({name:this.file.name}).parseHTML().getElement('.file');
        this.element.store('file', this);        
        this.timer = this._updateProgress.periodical(10, this);
    },
    toElement : function()
    {
        return this.element
    },
    _updateProgress : function()
    {
        var percentage = this.element.getElement('.percentage');           
        if (  this.file.status == plupload.DONE ) 
        {
            clearInterval(this.timer);           
            this.element.addClass('success');
        }
        else if ( this.file.status == plupload.FAILED ) {
            this.element.addClass('error');
            clearInterval(this.timer);            
        }
    }    
});