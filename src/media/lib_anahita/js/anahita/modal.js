/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * License: GPL3
 */

;(function ($, window, document) {
	
    'use strict';
    
    $('#an-modal').bind('hidden', function () {
    	  $(this).find('.modal-footer').find('button').remove();
    });
    
}(jQuery, window, document));