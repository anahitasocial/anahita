<?php defined('KOOWA') or die; ?>

<?php if (defined('JDEBUG') && JDEBUG) : ?>
<script src="com_locations/js/location.js" />
<?php else: ?>
<script src="com_locations/js/min/location.min.js" />
<?php endif; ?>

<?= @map_api(array()) ?>

<div style="margin: 10px 0; border: 1px solid #d5d5d5;">
<?= @map($locations) ?>
</div>

<?php $locations_url = 'option=com_locations&view=locations&layout=list&locatable_id='.$entity->id; ?>
<ul class="an-locations nav nav-pills" data-url="<?= @route($locations_url) ?>">
    <?php foreach($locations as $location) : ?>
    <li>
        <a href="<?= @route($location->getURL()) ?>">
          <?= @escape($location->name) ?>
        </a>
    </li>
    <?php endforeach; ?>
    <?php $selector_url = 'option=com_locations&view=locations&layout=selector&locatable_id='.$entity->id; ?>
    <li>
        <a href="<?= @route($selector_url) ?>" data-toggle="LocationSelector">
          + <?= @text('LIB-AN-ACTION-ADD-LOCATION') ?>
        </a>
    </li>
</ul>
