<?php

$pq = phpQuery::newDocument($buffer);

pq('div#tablecell')->wrap('<div class="mc-form-frame mc-padding" />');

$buffer = $pq->getDocument()->htmlOuter();



