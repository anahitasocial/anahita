/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
    
    'use strict';
    
    $.widget("anahita.editor", {
        
        _create : function () {
            
            var self = this;
    
            this._createEditor();   
            this._createToolbar(); 
            this._setContent();

            this._on( this.element.closest('form'), {
                   
                'beforeSubmit' : function ( event ) {
                    self._getContent();
                },
                
                submit : function ( event ) {
                    self._getContent();
                },
                
                reset : function ( event ) {
                    self._clear();
                }
            });
            
            this.editor.wysiwyg({
                  hotKeys: {
                    'ctrl+b meta+b': 'bold',
                    'ctrl+i meta+i': 'italic',
                    'ctrl+u meta+u': 'underline',
                    'ctrl+z meta+z': 'undo',
                    'ctrl+y meta+y meta+shift+z': 'redo'
                },
                toolbarSelector: '[data-role=editor-toolbar]',
                commandRole: 'edit',
                activeToolbarClass: 'btn-info',
                selectionMarker: 'edit-focus-marker',
                selectionColor: 'darkgrey',
                dragAndDropImages: true,
              });
        },
        
        _createEditor : function () {
            
            this.element.attr('required', false).hide();
            
            this.editor = $(document.createElement('div')).addClass('editor');
            this.editor.attr('id', this.element.attr('id') + '-editor');
            this.editor.insertAfter(this.element);
            
            
        },
        
        _createToolbar : function () {
            
            this.toolbar = $(document.createElement('div'))
                            .addClass('btn-toolbar')
                            .attr('data-role', 'editor-toolbar')
                            .attr('data-target', this.editor.attr('id')); 
            
            var text = '';
            
            //format blocks
            text += ''
            + '<div class="btn-group">'
            + '<a class="btn dropdown-toggle" data-toggle="dropdown" title="" data-original-title="Text Format">'
            + '<b class="caret"></b> Text' 
            + '</a>'
            + '<ul class="dropdown-menu">'
            + '<li><a data-edit="formatblock p" data-original-title="Paragraph">p</a></li>'
            + '<li><a data-edit="formatblock pre" data-original-title="Preformatted">pre</a></li>'
            + '<li><a data-edit="formatblock address" data-original-title="Address">addr</a></li>'
            + '<li><a data-edit="formatblock h1" data-original-title="Header 1">h1</a></li>'
            + '<li><a data-edit="formatblock h2" data-original-title="Header 2">h2</a></li>'
            + '<li><a data-edit="formatblock h3" data-original-title="Header 3">h3</a></li>'
            + '<li><a data-edit="formatblock h4" data-original-title="Header 4">h4</a></li>'
            + '<li><a data-edit="formatblock h5" data-original-title="Header 5">h5</a></li>'
            + '</ul> '
            + '<a class="btn" data-edit="bold" data-original-title="Bold (Ctrl/Cmd+B)"><b>b</b></a>'
            + '<a class="btn" data-edit="italic" data-original-title="Italic (Ctrl/Cmd+I)"><i>i</i></a>'
            + '<a class="btn" data-edit="strikethrough" data-original-title="Strikethrough"><strike>u</strike></a>'
            + '<a class="btn" data-edit="underline" data-original-title="Underline (Ctrl/Cmd+U)"><u>u</u></a>'
            + '<a class="btn" data-edit="insertunorderedlist" data-original-title="Bullet list">ul</a>'
            + '<a class="btn" data-edit="insertorderedlist" data-original-title="Number list">ol</a>'
            + '</div>';
            
            this.toolbar.html(text);
            
            this.toolbar.insertAfter(this.element);
            
            this.toolbar.find('[data-toggle="dropdown"]').dropdown();
        },
        
        _setContent : function() {           
            
            if(this.element.val()) {
            
                this.editor.html(this.element.val());
            
            } else {
            
                this.editor.html('<p><br/></p>');
            }
        },
        
        _getContent : function() {
            
            this.element.html(this.editor.html());
        },
        
        _clear : function () {
           
           this.editor.html('<p><br/></p>'); 
           this.element.html('');
        }
    });
    
    var bindEditor = function() {
      
      var elements = $('[data-behavior="Editor"]');
        
        $.each(elements, function( index, element ){
            
            if( !$(element).is(":data('anahita-editor')") ) {
            
              $(element).editor();
            }
        });
    };
    
    bindEditor();
    
    $(document).ajaxSuccess(function() {
        bindEditor();
    });
    
}(jQuery, window, document));