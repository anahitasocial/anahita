<?php defined('KOOWA') or die; ?>

<?php if (defined('JDEBUG') && JDEBUG) : ?>
<script src="com_locations/js/location.js" />
<?php else: ?>
<script src="com_locations/js/min/location.min.js" />
<?php endif; ?>

<?= @map_api(array()) ?>

<?php if(count($entity->locations)): ?>
<div style="margin: 10px 0; border: 1px solid #d5d5d5;">
<?= @map($locations) ?>
</div>
<?php endif; ?>

<?php $locations_url = 'option=com_locations&view=locations&layout=list_tags&locatable_id='.$entity->id; ?>
<ul class="an-locations nav nav-pills" data-url="<?= @route($locations_url) ?>"></ul>
