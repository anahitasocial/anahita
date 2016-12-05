<? defined('KOOWA') or die('Restricted access') ?>

<? if (defined('ANDEBUG') && ANDEBUG) : ?>
<script src="com_locations/js/map.osm.js" />
<? else: ?>
<script src="com_locations/js/min/map.osm.min.js" />
<? endif; ?>

<div class="entity-map" data-locations="<?= $locations ?>"></div>
