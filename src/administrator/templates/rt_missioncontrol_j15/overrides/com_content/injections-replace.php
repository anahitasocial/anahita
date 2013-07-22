<?php
if ($task == 'edit' or $task == 'add') {

	// add the tabber switcher js
	$this->document->addScript($this->templateUrl.'/html/com_content/switcher.js');

	$pq = phpQuery::newDocument($buffer);

	$framework = '<div id="mc-article-key" />
				  <ul id="mc-article-tabs">
				  	<li><a id="editor" class="active">Article Editor</a></li>
					<li><a id="publishing">Publishing &amp; MetaData</a></li>
					<li><a id="advanced">Advanced</a></li>
				  </ul>
				  <div id="mc-article">
				   	<div id="page-editor" />
				   	<div id="page-publishing">
				   		<div id="mc-pubdata" />
				   		<div id="mc-metadata" />
				   	</div>
				   	<div id="page-advanced">
				   		<div id="mc-settings" />
				   		<div id="mc-statusbox" />
				   	</div>
				  </div>';
	
	
	pq('form[name=adminForm')->prepend($framework);
	pq('#mc-article-key')->append(pq('form[name=adminForm] table td table.adminform:first-child'));
	pq('#page-editor')->append(pq('form[name=adminForm] table td table.adminform'));
	pq('#mc-statusbox')->append('<div class="mc-block"><h3 class="title">Summary</h3></div>');
	pq('#mc-statusbox .mc-block')->append(pq('table[style*="dashed silver"]'));
	pq('#mc-pubdata')->append(pq('#content-pane .panel:first-child')->removeClass('panel')->addClass('mc-block'));
	pq('#mc-settings')->append(pq('#content-pane .panel:first-child')->removeClass('panel')->addClass('mc-block'));
	pq('#mc-metadata')->append(pq('#content-pane .panel:first-child')->removeClass('panel')->addClass('mc-block'));
	
	// add editors if in edit mode	
	if ($task == 'edit') {
	
		// if roktracking found, add editor block
		if (file_exists($this->basePath.DS.'plugins'.DS.'system'.DS.'roktracking.php')) {
		
			$limit = 10;
			
			$cid = JRequest::getVar('cid');
			if (is_array($cid)) $cid = $cid[0];
			
			$db =& JFactory::getDBO();
			$query = 'select r.*, u.name, u.username, u.email,c.name as extension from #__rokadminaudit as r, #__users as u, #__components as c where r.user_id = u.id and c.option = r.option and r.cid = '.$cid.' and (r.task ="apply" or r.task="save") order by id desc limit '. intval($limit);
			$db->setQuery($query);
			$results = $db->loadObjectList();
			
			$editors = '<h3 class="title">Editors</h3>';
            if (!empty($results)) {
                $editors .= '<div class="mc-editors-list"><ul>';

                foreach ($results as $r) {
                    $editors .= '<li>'.$r->username.' ('.$r->timestamp.')</li>';
                }
                $editors .= '</ul></div>';
            } else {
                $editors .= 'no one has edited this article...';
            }

			
			pq('#mc-statusbox .mc-block')->append($editors);
		}
	}
	
	//change titles
	pq('#detail-page > span')->replaceWith('Publishing Information');
	pq('#params-page > span')->replaceWith('Advanced Information');
	
	//tweak key bits
	pq('#mc-article-key tr td:nth-child(2)')->addClass('mc-bigger-field');
	
	// remove unused bits
	pq('form[name=adminForm] > table')->remove();
	pq('div#content-pane')->remove();
	
	$buffer = $pq->getDocument()->htmlOuter();
	

} else {
	// needed bits
	$pq = phpQuery::newDocument($buffer);
		// add filter-table class for filter
		pq('form[name=adminForm] td:contains("Filter") > input[type=text]')->parents('table')->addClass('mc-filter-table');
		pq('form[name=adminForm] td:contains("toggle state")')->parents('table')->addClass('mc-legend-table');
		pq('table.adminlist ')->addClass('mc-list-table');
		
		
		// generic first/last classes
		pq('form[name=adminForm] table:first,table.noshow table.mc-list-table')->addClass('mc-first-table');
		pq('form[name=adminForm] table.mc-first-table')->next('table')->addClass('mc-second-table');
		pq('form[name=adminForm] table.mc-first-table tr:first td:first,form[name=adminForm] table.mc-first-table tr:first th:first')->addClass('mc-first-cell');
		pq('form[name=adminForm] table.mc-first-table tr:first td:last,form[name=adminForm] table.mc-first-table tr:first th:last')->addClass('mc-last-cell');

	
	$buffer = $pq->getDocument()->htmlOuter();



} 


