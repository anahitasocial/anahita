/**
 * @version		Id
 * @category	Anahita
 * @package  	Anahita_Social_Applications
 * @subpackage  Pages
 * @copyright	Copyright (C) 2008 - 2011 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

var OrderPagesOption = function() {
	var el = this;
	return {
		   method: 'get',
		   update: document.id('an-entities-main-wrapper'),
		   onSuccess : function() 
		   {			   
			   document.getElements('.page-ordering').removeClass('active');
			   el.getParent().addClass('active');
		   }
	}
}