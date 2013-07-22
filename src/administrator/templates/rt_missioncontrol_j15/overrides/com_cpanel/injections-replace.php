<?php

	// needed bits
	$pq = phpQuery::newDocument($buffer);
	pq('table.adminlist ')->addClass('mc-list-table');
	pq('.jpane-slider')->children(":first")->addClass('mc-slider-first');
	
	$buffer = $pq->getDocument()->htmlOuter();




