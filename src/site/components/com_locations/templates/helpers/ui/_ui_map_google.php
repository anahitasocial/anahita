<?php defined('KOOWA') or die('Restricted access') ?>

<script src="https://maps.googleapis.com/maps/api/js" />

<?php if (defined('JDEBUG') && JDEBUG) : ?>
<script src="com_locations/js/map.google.js" />
<?php else: ?>
<script src="com_locations/js/min/map.google.min.js" />
<?php endif; ?>

<div class="an-map" data-locations="<?= $locations ?>"></div>
