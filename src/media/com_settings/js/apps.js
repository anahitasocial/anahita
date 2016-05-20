/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2016 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {

   'use strict';

   $.widget("anahita.apps", {

      options : {
          baseurl : $('.an-entities').data('baseurl'),
          params : $('.an-entities').data('params'),
          sortSelect : $('a[data-trigger="sort"]'),
          container : $('.an-entities')
      },

      _create : function() {

          this.sort = this.options.params.sort;
          this.baseurl = this.options.baseurl;
          this.container = this.options.container;

          this._on(this.options.sortSelect, {
            click : function (event) {
              event.preventDefault();
              this.sort = $(event.delegateTarget).data('field');
              this._browse();
            }
          });
      },

      _browse : function() {
          this.container.load(this._getURL());
      },

      _getURL : function(){
        return this.baseurl + '&sort=' + this.sort;
      }
   });

   var apps = $('body').apps();

}(jQuery, window, document));
