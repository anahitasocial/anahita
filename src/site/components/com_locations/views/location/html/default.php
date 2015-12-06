<?php defined('KOOWA') or die('Restricted access') ?>

<?php $service = get_config_value('locations.service', 'google') ?>

<?php if ($service == 'google'): ?>
<script src="https://maps.googleapis.com/maps/api/js" />
<?php endif; ?>

<?php if (defined('JDEBUG') && JDEBUG) : ?>
<script src="com_locations/js/map.<?= $service ?>.js" />
<?php else: ?>
<script src="com_locations/js/min/map.<?= $service ?>.min.js" />
<?php endif; ?>

<div class="row">
  <div class="span12">
      <?= @helper('ui.header', array()) ?>
  </div>
</div>

<div class="an-entity">
    <?php
    $locations[] = array(
          'longitude' => $location->geoLongitude,
          'latitude' => $location->geoLatitude,
          'name' => $location->name );
    $locations = htmlspecialchars(json_encode($locations), ENT_QUOTES, 'UTF-8');
    ?>
    <div class="entity-map" data-zoom="18" data-locations="<?= $locations ?>"></div>

    <h2 class="entity-title">
      <?= @escape($location->name) ?>
    </h2>

    <div class="entity-meta">
        <?= @helper('address', $location) ?>
    </div>

    <?php if($location->description) : ?>
    <div class="entity-description">
  		<?= @helper('text.truncate', @content( nl2br($location->description), array('exclude' => array('syntax', 'video'))), array('length' => 200, 'consider_html' => true)); ?>
  	</div>
    <?php endif; ?>
</div>

<?php
$paginationUrl = $location->getURL().'&layout=list';

if (!empty($sort)) {
    $paginationUrl .= '&sort='.$sort;
}

if (!empty($scope)) {
    $paginationUrl .= '&scope='.$scope;
}
?>

<div id="an-locatables" class="an-entities masonry" data-trigger="InfiniteScroll" data-url="<?= @route($paginationUrl) ?>">
	<?= @template('_locatables') ?>
</div>
