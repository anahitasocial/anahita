<? defined('KOOWA') or die('Restricted access') ?>

<? if (defined('ANDEBUG') && ANDEBUG) : ?>
<script src="com_locations/js/nearby.<?= $service ?>.js" />
<? else: ?>
<script src="com_locations/js/min/nearby.<?= $service ?>.min.js" />
<? endif; ?>
