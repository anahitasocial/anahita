<?php defined('KOOWA') or die('Restricted access') ?>

<?php if (defined('JDEBUG') && JDEBUG) : ?>
<script src="com_locations/js/map.osm.js" />
<?php else: ?>
<script src="com_locations/js/min/map.osm.min.js" />
<?php endif; ?>

<div class="entity-map" data-locations="<?= $locations ?>"></div>
