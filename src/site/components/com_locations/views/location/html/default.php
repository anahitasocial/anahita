<?php defined('KOOWA') or die('Restricted access') ?>

<script src="https://maps.googleapis.com/maps/api/js" />
<script>
(function ($, window, document) {

    $('document').ready(function(){

        var mapContainers = $('.entity-map');

        $.each(mapContainers, function(index, elem){

            $(elem).css('height', $(elem).closest('.an-entity').width() / 2);

            var location = new google.maps.LatLng(
                $(elem).data('latitude'),
                $(elem).data('longitude')
            );

            var map = new google.maps.Map(
                elem,
                options = {
                  zoom: $(elem).data('zoom'),
                  center: location,
                  mapTypeId: google.maps.MapTypeId.ROADMAP
                }
            );

            $(elem).data('map', map);

            var marker = new google.maps.Marker({
                position: location,
                title: $(elem).data('name')
            }).setMap(map);
        });
    });

}(jQuery, window, document));
</script>

<div class="row">
  <div class="span12">
      <?= @helper('ui.header', array()) ?>
  </div>
</div>

<div class="an-entity">

    <div
      class="entity-map"
      data-latitude="<?= $location->geoLatitude ?>"
      data-longitude="<?= $location->geoLongitude ?>",
      data-name="<?= @escape($location->name) ?>"
      data-zoom="18"
    ></div>

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
