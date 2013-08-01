/**
 * @version		Id
 * @category	Anahita
 * @package  	Anahita_Social_Applications
 * @subpackage  Pages
 * @copyright	Copyright (C) 2008 - 2011 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

var AnEditorMode = {
	Text : 0,
	HTML : 1
};

Behavior.addGlobalFilter('Editor', {
	setup : function(el, api) 
	{
		if ( Browser.Platform.mobile )
			return;
			
		var options = Object.merge(api._getOptions(),{element:el});
		new AnEditor(options);	 
	}
});

var AnEditor = new Class({
	
	//initialize an editor
	initialize : function(options)
	{
		Encoder.EncodeType = "entity";
		this.options   = options;
		this.element   = options.element;
		this.elementId = this.element.id;
		this.extended = options.extended;
		var init      = this._init.bind(this);
		init();
	},
	
	getMode		  : function()
	{
		if ( this._quicktag.toolbar.isDisplayed() )
			return AnEditorMode.Text;
		return AnEditorMode.HTML
	},
	
	switchUI      : function()
	{
		//current mode is text	
		if ( this.getMode() == AnEditorMode.Text) {
			this._fixCode();
			this._quicktag.toolbar.hide();
			this.htmlBtn.show();
			this.visualBtn.hide();
		} else {
			this._quicktag.toolbar.show();
			this.visualBtn.show();
			this.htmlBtn.hide();			
		}
		
		tinyMCE.execCommand('mceToggleEditor', false, this.elementId);
	},
	
	_init : function()
	{
		var element    = document.id(this.element);
		this.container = new Element('div',{'class':'an-editor-container'}).inject(element, 'before');
		this.container.adopt(element);
		var switchui = function(e){
			e.stop();
			this.switchUI();
		}.bind(this);
		if ( !this._quickTagModeOnly() ) {
			var toggle = new Element('div',{'class':'an-editor-links'}).adopt(
					(this.htmlBtn   = new Element('a',{'class':'an-action','html':'HTML',  'href':'#'})),
					(this.visualBtn = new Element('a',{'class':'an-action','html':'Visual', 'href':'#'}))
			).inject(element,'before');
			toggle.setStyles({				
				'position': 'absolute',
				'width'   : this.container.getSize().x - 15
			});
			this.htmlBtn.addEvent('click',   switchui);
			this.visualBtn.addEvent('click', switchui);			
			this.visualBtn.hide();
		}
		var form	  = element.getParent('form');
		Object.append(this.element, {
			setContentFromEditor : function() {
				var tinymce = tinyMCE.get(elementId);
				if ( !this.isDisplayed() && tinymce ) {
					this.set('value', tinymce.getContent());
				}
			}
		});
		var elementId = this.elementId;
		if ( form ) {
			form.addEvents({
				'submit' : function(event) {
				   var tinymce = tinyMCE.get(elementId);
				   if ( !element.isDisplayed() && tinymce ) 
				   {
				   	   element.isVisible = Function.from(true);
					   element.set('value', tinymce.getContent());
					   var validator = this.retrieve('validator') || 
					   				   new Form.Validator(this);
					   if ( !validator.validateField(element, true) ) {
							event.stop();
					   }
					   var emptyContent = function() {
							   tinymce.setContent('');
						};
					   if ( event.request ) 
					   {
					   		event.request.addEvent('success', emptyContent)
					   } else 
					   {
					   	 	emptyContent.delay(1000)
					   }
				   }
				}
			});
		}
		
		
		this._fixCode();
		this._initQuickTag();
		
		if ( !this._quickTagModeOnly() )
		{
			this._initTincyMCE();
			this._quicktag.toolbar.hide();
			tinyMCE.execCommand('mceToggleEditor', false, this.element);
		}
		
	},
	_initTincyMCE : function()
	{		
		var elements = 'br,a[href],strong\/b,em\/i,ul,ol,li,blockquote,img[src],code[lang]';
		var buttons  = '|,bold,italic,bullist,numlist,blockquote,|,image,link';
		var formats  = '';
		if ( this.extended ) {
			elements += ',h2,h3,h4';
			buttons  += ',|,formatselect';
			formats   = 'h2,h3,h4';
		}
		buttons += ',|,undo,redo';
		var switchui = this.switchUI.bind(this);
		var options  = {
			  "apply_source_formatting" : false,
			  "theme": "advanced",
			  "mode": "exact",
			  "elements": this.elementId,
			  "theme_advanced_toolbar_location": "top",
			  "force_br_newlines": true,
			  "force_p_newlines": false,
			  "fix_list_elements": false,
			  "forced_root_block": "",
			  "plugins": "anahita,autoresize",
			  "entity_encoding": "raw",
			  "valid_elements": elements,
			  "theme_advanced_buttons1": buttons,
			  "theme_advanced_blockformats": formats,
			  "theme_advanced_buttons2": "",
			  "theme_advanced_buttons3": "",
			  "cleanup_callback" : this._cleanupEditor.bind(this),
			  "add_form_submit_trigger" : false,
			  "autoresize_max_height" : 800	
		}
		
		this._tinymce = tinyMCE.init(options);
		
	},
	_initQuickTag : function()
	{
		var buttons = [
		{ "title":"B", 		"tag":"strong"},
		{ "title":"I", 		"tag":"em"},
		{ "title":"Quote", 	"tag":"blockquote"},
		{ "title":"Code", 	"tag":"code"},
		{ "title":"ul", 	"tag":"ul"},
		{ "title":"ol", 	"tag":"ol"},
		{ "title":"li", 	"tag":"li"},
		{ "title":"Image", 	"tag":"img"},
		{ "title":"URL", 	"tag":"a"}];
		
		if (this.extended ) {	
			buttons.push({ "title":"H2", "tag":"h2"});
			buttons.push({ "title":"H3", "tag":"h3"});
			buttons.push({ "title":"H4", "tag":"h4"});
		}
		
		this._quicktag = new QuickTag({editor:this, buttons:buttons});
		
	},
	_cleanupEditor : function(type, content)
	{		 		  		 
		  switch (type) 
		  {
		   case "get_from_editor":
			 content  = content.replace(/<br>/gi,"\n").replace(/<br\s*\/>/gi, "\n");			 
			 content  = this._formatText(content, type);
			 content  = Encoder.htmlDecode(content);
		     break;
		   case "insert_to_editor":					 
			 content  = this._formatText(content, type);			
			 content  = content.replace(/\n/gi,'<br />');
		     break;
		  }
		 return content;
	},
	_formatText : function(content, type) 
	{
		 //put each of the following tags in their own line
		 //example <ul><li> 
		 //<ul>
		 //<li>
		 var blocks  = ['ul','ol','blockquote','code','li'];
		 
		 if ( type == 'insert_to_editor') {
			 tags  = "(<(" + blocks.join("|") + ")[^>]*>)\\s(\\S)";
			 tags  = new RegExp(tags, 'gi');
			 content = content.replace(tags, '$1$3');
			 tags    = "(\\S)\\s(</\(" + blocks.join("|") + ")[^>]*>)";
			 tags  = new RegExp(tags, 'gi');
			 content = content.replace(tags, '$1$2');
			 content 	 = content.replace(/<(ul|ol)>\s*/gi,'<$1>').replace(/\s*<\/(ul|ol)>/gi,'</$1>');
			 content	 = content.replace(/\s*(<\/?li>)\s*/gi, '$1');			 
			 return content;
		 }
		 content 	 = content.replace(/<(ul|ol|li)>\s*/gi,'<$1>').replace(/\s*<\/(ul|ol|li)>/gi,'</$1>');
		 blocks.each(function(block){
			 var regexp;
			 if (  block == 'li' )
				 regexp = '(<li>[^>]*<\/li>)';
			 else
				 regexp = '(<\/?' + block + '[^>]*>)';
			 regexp     = '([^<]?)'+regexp+'(.?)';
			 regexp     = new RegExp(regexp,'gi');
			 while (match = regexp.exec(content)) {
				  var pre  = match[1];
				  var tag  = match[2];
				  var post = match[3];
				  var text = match[0];
				  if ( pre.match(/\S/) ) 
					  content = content.replace(pre+tag,pre + "\n" + tag);
		  		  if ( post.match(/\S/) ) 
		  			content   = content.replace(tag + post, tag + "\n" + post);				  	  
			 }
		 });
		 return content;
	},
	_fixCode   : function()
	{			
		textarea = document.id(this.element);
		content  = textarea.get('value');
		content  = this._formatText(content);
		content = new Element('div',{html:content});
		codes	 = content.getElements('code');
		codes.each(function(code){
			 code.set('html', Encoder.htmlEncode(code.get('html')));			
		});		
		textarea.set('value', content.get('html'));
	},
	_quickTagModeOnly : function()
	{
		return false;
	}
	
});
 
var QuickTag = new Class({
	
	Implements: Options,
	
	options	: {
		buttons	: new Array()
	},
	
	initialize: function(options){	
		this.setOptions(options);
		this.editor	    = options.editor;		
		this.buttons	= this.options.buttons;
		this.element	= this.editor.element;
		this.toolbar	= this.element + '-quicktags';
		this.buildToolbar();
	},
	
	buildToolbar : function()
	{
		var toolbar = this.toolbar  = new Element('div', {'class':'an-editor-qt-toolbar btn-group'}).inject(this.element, 'before');
		var insertTag = this.insertTag.bind(this);		
		var switchui  = this.editor.switchUI.bind(this.editor);
		var buttons   = this.buttons;
		var editor = this.editor;
		buttons.each(function(button, index){		
			button = new Element('button', 
				{
					'name'   : button.tag, 
					'id'     : 'an-qt-button-' + button.tag, 
					'class'  : 'an-qt-button btn',
					'text'   : button.title,
					'styles' : {
						'position' : 'relative',
						'z-index' : '10'
					 }
				});
			button.addEvent('click', function(e){
				e.stop();
				if ( this.get('name') == 'HTML' )
					switchui();
				else
					insertTag(this.get('name'));				
			});
			button.inject(toolbar);
		});
	},
	
	insertTag : function(tag) 
	{		
		var element = document.id(this.element);
		
		//IE support
		if(document.selection)
		{
			element.focus();
			sel = document.selection.createRange();
			
			if (tag == 'a') 
			{
				 if ( link = linkTag(sel.text) ) {
					 sel.text = link;					 
				 }
			}
			else if(tag == 'img')
			{
				 if ( img = this.buildImageTag(sel.text) ) {
					 sel.text = img;
				 }
			}
			else 
			{
				sel.text = this.buildTag(tag, sel.text);
			}
			
			element.focus();
		}
		
		//MOZILLA/NETSCAPE support
		else if( element.selectionStart || element.selectionStart == '0' )
		{
			var startPos 	= element.selectionStart;
			var endPos 		= element.selectionEnd;
			var cursorPos 	= endPos;
			var scrollTop 	= element.scrollTop;
			
			if ( tag == 'a') 
			{
				var linkString = element.value.substring(startPos, endPos);
				var linkTag = this.buildLinkTag(linkString);
				if ( linkTag  )
				{
					element.value = element.value.substring(0, startPos) +
					linkTag +
		            element.value.substring(endPos, element.value.length);
		
					cursorPos += linkTag.length;					
				}

			}
			else if( tag == 'img' )
			{
				var imgString = element.value.substring(startPos, endPos);
				var imgTag = this.buildImageTag(imgString);
				if ( imgTag )
				{
					element.value = element.value.substring(0, startPos) +
		            imgTag +
		            element.value.substring(endPos, element.value.length);
					
					cursorPos += imgTag.length;					
				}
			}
			else 
			{
				element.value = element.value.substring(0, startPos) +
				this.buildTag(tag, element.value.substring(startPos, endPos)) +
	            element.value.substring(endPos, element.value.length);
	
				cursorPos += tag.length + 2;
			}
			
			element.focus();
			element.selectionStart = cursorPos;
			element.selectionEnd = cursorPos;
			element.scrollTop = scrollTop;
		}
	},
	
	buildTag : function(tag, selectedString)
	{
		html = '<' + tag + '>' + selectedString + '</' + tag + '>';		
		return html;
	},
	
	buildImageTag : function(selectedString)
	{
		var promptStr = 'http://';
		if ( selectedString.match(/^(http|https)\:\/\/[a-z0-9\-\.]+\.[a-z]{2,3}(:[a-z0-9]*)?\/?([a-z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*$/i) ) {
			promptStr = selectedString;
		}		
		var src = prompt('Enter the URL', promptStr);
		if ( src )
			return '<img src="' + src + '" />';
		return false;
	},
	
	buildLinkTag : function(selectedString)
	{
		var promptStr = 'http://';
		if ( selectedString.match(/^(http|https|ftp)\:\/\/[a-z0-9\-\.]+\.[a-z]{2,3}(:[a-z0-9]*)?\/?([a-z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*$/i) ) {
			promptStr = selectedString;
		}
		var src = prompt('Enter the URL', promptStr);
		if ( src )
			return '<a href="'+ src +'">' + selectedString + '</a>'
		return false;
		
	}
});