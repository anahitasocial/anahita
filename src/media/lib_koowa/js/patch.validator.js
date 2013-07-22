/*
---

description: Monkey patching the Form.Validator to alter its behavior and extend it into doing more

requires:
 - MooTools More

license: @TODO

...
*/

if(!Koowa) var Koowa = {};

(function($){
    
    Koowa.Validator = new Class({
    
        Extends: Form.Validator.Inline,
        
        options: {
        	//Needed to make the TinyMCE editor validation function properly
        	ignoreHidden: false,
        	
            onShowAdvice: function(input, advice) {
                advice.addEvent('click', function(){
                    input.focus();
                });
            }
        }
    
    });

})(document.id);