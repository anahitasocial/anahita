<?php defined('KOOWA') or die('Restricted access'); ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" />
<script src="mod_nivoslider/js/jquery.nivo.slider.pack.js" />
<style src="mod_nivoslider/css/nivoslider.css" />

<div class="slider-wrapper theme-<?= $params->get('css-theme', 'default') ?>">
	<?php if($params->get('show-ribbon', 1) && ( $params->get('css-theme') == 'orman' || $params->get('css-theme') == 'pascal' )): ?>
	<div class="ribbon"></div>
	<?php endif; ?>
	
	<div id="slider" class="nivoSlider">
	<?php foreach($articles as $article): ?>
		<?= $this->getService('koowa:filter.html', array('tag_method'=>0, 'tag_list'=>'img'))->sanitize($article->introtext); ?>
	<?php endforeach; ?>
	</div>
</div>

<script>
//no conflict jquery
jQuery.noConflict();

(function($){
	$(window).load(function() {
	   $('#slider').nivoSlider({
	    	 effect:          '<?= $params->get('option-effect', 'random') ?>',
	         slices:           <?= $params->get('option-slices', 15) ?>,
	         boxCols:          <?= $params->get('option-box-cols', 8) ?>,
	         boxRows:          <?= $params->get('option-box-rows', 4) ?>,
	         animSpeed:        <?= $params->get('option-anim-speed', 500) ?>,
	         pauseTime:        <?= $params->get('option-pause-time', 3000) ?>,
	         directionNav:     <?= ($params->get('option-direction-nav', 1)) ? 'true' : 'false'; ?>,
	         directionNavHide: <?= ($params->get('option-direction-nav-hide', 1)) ? 'true' : 'false'; ?>,
	         controlNav:       <?= ($params->get('option-control-nav', 1)) ? 'true' : 'false'; ?>,
	         controlNavThumbs: <?= ($params->get('option-control-nav-thumbs', 0)) ? 'true' : 'false'; ?>,
	         controlNavThumbsFromRel:  <?= ($params->get('option-control-nav-thumbs-from-rel', 0)) ? 'true' : 'false'; ?>,
	         controlNavThumbsSearch:  '<?= $params->get('option-control-nav-thumbs-search', '.jpg') ?>',
	         controlNavThumbsReplace: '<?= $params->get('option-control-nav-thumbs-replace', '_thumb.jpg') ?>',
	         keyboardNav:    <?= ($params->get('option-keyboard-nav', 1)) ? 'true' : 'false'; ?>,
	         pauseOnHover:   <?= ($params->get('option-pause-on-hover', 1)) ? 'true' : 'false'; ?>,
	         manualAdvance:  <?= ($params->get('option-manual-advance', 0)) ? 'true' : 'false'; ?>,
	         captionOpacity: <?= $params->get('option-caption-opacity', 0.8) ?>,
	         prevText:      '<?= $params->get('option-prev-text', 'Prev') ?>',
	         nextText:      '<?= $params->get('option-next-text', 'Next') ?>',
	         randomStart:    <?= ($params->get('option-random-start', 0)) ? 'true' : 'false'; ?>
		 });
	});
})(jQuery);
</script>
