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
             this.form.prepend('<div data-callback="recaptchaCallback" class="g-recaptcha" data-size="invisible" data-sitekey="' + this.options.siteKey + '" />');
             this._on( this.form, {
                 'submit' : function (event) {
                     event.preventDefault();
                     if (this.element.context.checkValidity()) {
                         grecaptcha.execute();
                     } else {
                         grecaptcha.reset();
                     }
                 }
             });
         },
         recaptchaCallback : function (token) {
             this.element.context.submit();
         }
     });

}(jQuery, window, document));
