/**
 * @version		Id
 * @category	Anahita
 * @package  	Anahita_Social_Applications
 * @subpackage  Entitys
 * @copyright	Copyright (C) 2008 - 2010 rmdStudio Inc. and Peerglobe Technology Inc. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link     	http://www.anahitapolis.com
 */

window.addEvent('domready', function(){
    window.entityHelper = new EntityHelper();
});


Delegator.register('click', {
	
	'ReadForm' : function(event, el, api){
		event.stop();
		entityHelper.resetForm();
		document.id('entity-add-wrapper').show();
	},
	
	'CancelAdd' : function(event, el, api){
		event.stop();
		entityHelper.resetForm();
		document.id('entity-add-wrapper').hide();
	},
	
	'Add' : function(event, el, api){
		event.stop();
		entityHelper.add();
	}
});