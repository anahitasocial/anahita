/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2017 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

 ;(function ($, window, document) {

     'use strict';

     $.widget("anahita.recaptcha", {
         options : {
             siteKey: ''
         },
         _create : function () {
             this.form = $(this.element);
             this.form.prepend('<div data-callback="recaptcha.onSubmitCallback()" class="g-recaptcha" data-size="invisible" data-sitekey="' + this.options.siteKey + '" />');

             this._on( this.form, {
                 'submit' : function ( event ){
                     event.preventDefault();
                     grecaptcha.execute();
                 }
             });
         },
         onSubmitCallback : function() {
             console.log("onSubmitCallback called");
         }
     });

}(jQuery, window, document));
