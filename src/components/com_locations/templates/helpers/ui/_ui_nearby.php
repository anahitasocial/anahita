<?php defined('KOOWA') or die('Restricted access') ?>

<?php if (defined('JDEBUG') && JDEBUG) : ?>
<script src="com_locations/js/nearby.<?= $service ?>.js" />
<?php else: ?>
<script src="com_locations/js/min/nearby.<?= $service ?>.min.js" />
<?php endif; ?>
