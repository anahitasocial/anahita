/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	tinymce.create('tinymce.plugins.Anahita', {
		
		init : function(ed, url) {
			ed.addCommand('addImage', function() {					
				var sel		 		 = ed.selection;
				var selection		 = sel;
				var dom				 = ed.dom;
				var img	 			 = ed.dom.getParent(sel.getNode(), 'img');
				var value = 'http://';
				if ( sel.getContent().match(/^(http|https)\:\/\/[a-z0-9\-\.]+\.[a-z]{2,3}(:[a-z0-9]*)?\/?([a-z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*$/i) )
					value = sel.getContent();
				
				if ( value = prompt('Enter the URL', value ) ) {
					value = {src:value};					
					if (!img) {
						ed.execCommand('mceInsertContent', false, '<img src="'+value.src+'" />', {skip_undo : 1});
					} else {
						if (value.href)
							dom.setAttribs(img, value);
						else
							editor.dom.remove(img, true);
					}
				}
			});
			
			ed.addCommand('addLink', function() {					
				var sel		 		 = ed.selection;
				var selection		 = sel;
				var dom				 = ed.dom;
				var link 			 = ed.dom.getParent(sel.getNode(), 'a'), img, style, cls;
				
				// No selection and not in link
				if (sel.isCollapsed() && !ed.dom.getParent(sel.getNode(), 'A'))
					return;
				
				var value = 'http://';
				if ( link )
					value = document.id(link).get('href');
				else if ( sel.getContent().match(/^(http|https)\:\/\/[a-z0-9\-\.]+\.[a-z]{2,3}(:[a-z0-9]*)?\/?([a-z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*$/i) )
					value = sel.getContent();
				
				if ( value = prompt('Enter the URL', value ) ) {
					value = {href:value};
					value.href = value.href.replace(' ', '%20');
					if (!link) {
						// WebKit can't create links on floated images for some odd reason
						// So, just remove styles and restore it later
						if (tinymce.isWebKit) {
							img = dom.getParent(selection.getNode(), 'img');

							if (img) {
								style = img.style.cssText;
								cls = img.className;
								img.style.cssText = null;
								img.className = null;
							}
						}

						ed.execCommand('CreateLink', false, value.href);

					} else {
						if (value.href)
							dom.setAttribs(link, value);
						else
							editor.dom.remove(link, true);
					}					
				}
			});

			// Register buttons
			ed.addButton('image', {
				title : 'Image',
				cmd : 'addImage'
			});
			ed.addButton('link', {
				title : 'Link',
				cmd : 'addLink'
			});	
		}
	});

	// Register plugin
	tinymce.PluginManager.add('anahita', tinymce.plugins.Anahita);
})();