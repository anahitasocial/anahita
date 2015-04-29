/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$('#mobile-main-menu ul').hide();
	
	$('body').on('click', 'a[data-trigger="ShowMainmenu"]', function ( event ) {
		event.preventDefault();
		$('#mobile-main-menu ul').slideToggle();
    });
	
	$('body').on('click', 'a[data-trigger="Logout"]', function ( event ) {
		$.post(this.href, { action : 'delete' });
	});

}(jQuery, window, document));	