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

            this._on(this.element.closest('form'), {
                'submit' : function ( event ) {
                    self._getContent();
                }
            });
            
            this.editor.wysiwyg({
                  hotKeys: {
                    'ctrl+b meta+b': 'bold',
                    'ctrl+i meta+i': 'italic',
                    'ctrl+u meta+u': 'underline',
                    'ctrl+z meta+z': 'undo',
                    'ctrl+y meta+y meta+shift+z': 'redo',
                    'shift+tab': 'outdent',
                    'tab': 'indent'
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
            + '<a class="btn dropdown-toggle" data-toggle="dropdown" title="" data-original-title="Format">'
            + '<b class="caret"></b> Format' 
            + '</a>'
            + '<ul class="dropdown-menu">'
            + '<li><a data-edit="formatblock p" data-original-title="Paragraph">Paragraph</a></li>'
            + '<li><a data-edit="formatblock pre" data-original-title="Preformatted">Preformatted</a></li>'
            + '<li><a data-edit="formatblock address" data-original-title="Address">Address</a></li>'
            + '<li><a data-edit="formatblock h1" data-original-title="Header 1">Header 1</a></li>'
            + '<li><a data-edit="formatblock h2" data-original-title="Header 2">Header 2</a></li>'
            + '<li><a data-edit="formatblock h3" data-original-title="Header 3">Header 3</a></li>'
            + '<li><a data-edit="formatblock h4" data-original-title="Header 4">Header 4</a></li>'
            + '<li><a data-edit="formatblock h5" data-original-title="Header 5">Header 5</a></li>'
            + '</ul>'
            + '</div>';
            
            //bold, italic, Strike Through, Underline
            text += '' 
            + '<div class="btn-group">'
            + '<a class="btn" data-edit="bold" data-original-title="Bold (Ctrl/Cmd+B)"><strong>B</strong></a>'
            + '<a class="btn" data-edit="italic" data-original-title="Italic (Ctrl/Cmd+I)"><em>I</em></a>'
            + '<a class="btn" data-edit="strikethrough" data-original-title="Strikethrough"><strike>U</strike></a>'
            + '<a class="btn" data-edit="underline" data-original-title="Underline (Ctrl/Cmd+U)"><u>U</u></a>'
            + '</div>'; 
            
            //lists
            text += ''
            + '<div class="btn-group">'
            + '<a class="btn" data-edit="insertunorderedlist" data-original-title="Bullet list">&#8226; List</a>'
            + '<a class="btn" data-edit="insertorderedlist" data-original-title="Number list">&sup1; &sup2; &sup3; List</a>'
            + '</div>';
            
            this.toolbar.html(text);
            
            this.toolbar.insertAfter(this.element);
        },
        
        _setContent : function() {           
            
            if(this.element.val()) {
            
                this.editor.html(this.element.val());
            
            } else {
            
                this.editor.html('<p><br/></p>');
            }
        },
        
        _getContent : function() {
            
            var value = this._htmlEncode(this.editor.html());
            this.element.val(value);
        },
        
        _htmlEncode : function(value) {
            
            var div = $(document.createElement('div'));
            return div.text(value).html();
        }
    });
    
    $('[data-behavior="Editor"]').editor();
    
}(jQuery, window, document));