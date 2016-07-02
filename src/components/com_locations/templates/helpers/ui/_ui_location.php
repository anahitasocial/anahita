<?php defined('KOOWA') or die; ?>

<?php if (defined('JDEBUG') && JDEBUG) : ?>
  <?php if($entity->authorize('edit')): ?>
  <script src="com_locations/js/geoposition.js" />
  <?php endif; ?>
<script src="com_locations/js/location.js" />
<?php else: ?>
  <?php if($entity->authorize('edit')): ?>
  <script src="com_locations/js/min/geoposition.min.js" />
  <?php endif; ?>
<script src="com_locations/js/min/location.min.js" />
<?php endif; ?>

<?= @map_api(array()) ?>

<?php if(count($entity->locations)): ?>
<div class="map-container">
<?= @map($locations) ?>
</div>
<?php endif; ?>

<?php $locations_url = 'option=com_locations&view=locations&layout=list_tags&locatable_id='.$entity->id; ?>
<ul class="an-locations" id="locations-<?= $entity->id ?>" data-url="<?= @route($locations_url) ?>"></ul>

<?php if($entity->authorize('edit')) : ?>
<?php $selector_url = 'option=com_locations&view=locations&layout=selector&locatable_id='.$entity->id; ?>
<div class="toolbar">
  <button disabled class="btn btn-small" data-url="<?= @route($selector_url) ?>" data-trigger="LocationSelector" data-locatable="<?= $entity->id ?>">
  + <?= @text('LIB-AN-ACTION-ADD-LOCATION') ?>
  </button>
</div>
<?php endif; ?>
