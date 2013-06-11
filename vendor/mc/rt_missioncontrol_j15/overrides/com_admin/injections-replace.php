<?php
if ($task != 'help') {
	$buffer = str_replace('adminlist','adminlist2',$buffer);
	$buffer = str_replace('width="650"','width="75%"',$buffer);
	$buffer = str_replace('width="300"','width="250"',$buffer);
	$pq = phpQuery::newDocument($buffer);
	
	pq('fieldset.adminform')->wrap('<div class="mc-form-frame mc-padding" />');
	
	$buffer = $pq->getDocument()->htmlOuter();
	

} else {
	// needed bits
	$pq = phpQuery::newDocument($buffer);
	pq('table.adminlist ')->addClass('mc-list-table');
	pq('form[name=adminForm] table.adminform table:not(".mc-filter-table")')->wrapAll('<div class="mc-form-frame mc-padding" />');
	pq('form[name=adminForm] > table.admintable,form[name=adminForm] > table.adminform')->wrapAll('<div class="mc-form-frame mc-padding" />');


	// custom bits
	pq('div[id$=cellhelp')->wrapAll('<div class="mc-form-frame mc-padding mc-search-data" />');
	pq('form > div.mc-form-frame:first table.adminform')->removeClass('mc-first-table')->removeClass('adminform')->addClass('mc-search-form');
	pq('form > div.mc-form-frame:first')->removeClass('mc-form-frame')->removeClass('mc-padding');

	
	$buffer = $pq->getDocument()->htmlOuter();



} 


