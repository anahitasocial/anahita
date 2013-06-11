<?php
// old
//pq('table.adminform tr:first-child th:last-child')->wrapInner('<div class="mc-button" />');

global $moo_override;

if ($task == 'edit') {

	$pq = phpQuery::newDocument($buffer);
	
	pq('form[name=adminForm] fieldset.adminform')->parents('form[name=adminForm])')->wrapInner('<div class="mc-form-frame" />');
	pq('div.col:last')->addClass('mc-last-column');
	pq('form[name=adminForm] table.adminform table:not(".mc-filter-table")')->wrapAll('<div class="mc-form-frame mc-padding" />');
	pq('form[name=adminForm] > table.admintable,form[name=adminForm] > table.adminform')->wrapAll('<div class="mc-form-frame mc-padding" />');
	pq('table.mc-filter-table')->parent('div.mc-form-frame')->removeClass('mc-form-frame');

    // set moo override if moo 1.3 template
    $cid = JRequest::getVar('cid');
    if (is_array($cid)) {
 		
        if (defined("GANTRY_VERSION")) $moo_override = true;
    }
	
	if ($this->_isGantryTemplate()) {

		//special handling for gantry templates
//		$framework = '<div id="mc-gantry-header">
//						<div class="col width-small mc-template">
//							<span class="mc-name">Name:</span>
//							<span class="mc-value" />
//						</div>
//						<div class="col width-large mc-description">
//							<span class="mc-name">Full Name:</span>
//							<span class="mc-value" />
//						</div>
//					  </div>';
//					  
		pq('.mc-form-frame')->addClass('gantry-template');
//		pq('.mc-form-frame')->before($framework);
//		pq('.mc-template .mc-value')->append($this->_getTemplateName());
//		pq('.mc-description .mc-value')->append(pq('h3.template-title')->text());
//		
		//pq('.col.width-50.mc-last-column')->after(pq('.mc-form-frame .col:first-child > fieldset:last-child'));
//		
//		
//		$thumb = '<fieldset class="adminform mc-thumbnail">
//					<legend>Thumbnail</legend>
//				  </fieldset>';
//				  
//		pq('.mc-form-frame .col:first-child fieldset:first-child')->after($thumb);
//		pq('.mc-thumbnail legend')->after(pq('.template-pad img:first-child'));
//		
		//remove stuff
//		pq('.mc-form-frame .col:first-child fieldset:first-child')->remove();
//		pq('.mc-form-frame .col:first-child td.key')->remove();

	
	}

	

	$buffer = $pq->getDocument()->htmlOuter();

} elseif ($task == 'preview' || $task == 'edit_source' || $task == 'edit_css') {

	$pq = phpQuery::newDocument($buffer);

	pq('table.adminform')->wrapAll('<div class="mc-form-frame mc-padding" />');

	$buffer = $pq->getDocument()->htmlOuter();

} else {

	//list view
	$pq = phpQuery::newDocument($buffer);

	// add filter-table class for filter
	pq('form[name=adminForm] td:contains("Filter") > input[type=text]')->parents('table')->addClass('mc-filter-table');
	pq('form[name=adminForm] td:contains("toggle state")')->parents('table')->addClass('mc-legend-table');
	pq('table.adminlist ')->addClass('mc-list-table');
	pq('table.adminlist')->prev('table')->addClass('mc-filter-table');
	
	// generic first/last classes
	pq('form[name=adminForm] table:first,table.noshow table.mc-list-table')->addClass('mc-first-table');
	pq('form[name=adminForm] table.mc-first-table')->next('table')->addClass('mc-second-table');
	pq('form[name=adminForm] table.mc-first-table tr:first td:first,form[name=adminForm] table.mc-first-table tr:first th:first')->addClass('mc-first-cell');
	pq('form[name=adminForm] table.mc-first-table tr:first td:last,form[name=adminForm] table.mc-first-table tr:first th:last')->addClass('mc-last-cell');

	
	$buffer = $pq->getDocument()->htmlOuter();
}