<?php defined('KOOWA') or die('Restricted access') ?>

<div class="row">
  <div class="span12">
      <?= @helper('ui.header', array()) ?>
  </div>
</div>

<div class="an-entity">
    <div class="entity-meta">
    <?= @map($location) ?>
    </div>

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
