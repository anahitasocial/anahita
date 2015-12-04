<?php defined('KOOWA') or die('Restricted access') ?>

<script src="https://maps.googleapis.com/maps/api/js" />

<script>
(function ($, window, document) {

    $('document').ready(function(){

        var myLatlng = new google.maps.LatLng(
            <?= $location->geoLatitude ?>,
            <?= $location->geoLongitude ?>
        );

        var myOptions = {
          zoom: 17,
          center: myLatlng,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map(
            document.getElementById("map-canvas"),
            myOptions
        );

        var marker = new google.maps.Marker({
            position: myLatlng,
            title: "<?= @escape($location->title) ?>"
        });

        marker.setMap(map);
    });

}(jQuery, window, document));
</script>

<style>
.map-canvas {
   height: 350px;
   border-top: 1px solid #ebebeb;
   border-bottom: 1px solid #ebebeb;
}
</style>

<div class="row">
  <div class="span12">
      <?= @helper('ui.header', array()) ?>
  </div>
</div>

<div class="an-entity">
    <h2 class="entity-title">
      <?= @escape($location->name) ?>
    </h2>

    <div id="map-canvas" class="map-canvas"></div>

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
