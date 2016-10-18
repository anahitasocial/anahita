<? defined('KOOWA') or die('Restricted access') ?>

<?= @map_api(array()) ?>

<div class="row">
  <div class="span12">
      <?= @helper('ui.header') ?>
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

    <? if($location->description) : ?>
    <div class="entity-description">
    <?= @helper('text.truncate', @content(nl2br($location->description), array('exclude' => array('syntax', 'video'))), array('length' => 200, 'consider_html' => true)); ?>
    </div>
    <? endif; ?>
</div>

<div class="row">
    <div class="span12">
      <div class="btn-toolbar clearfix">
          <div class="pull-right btn-group">
              <a class="btn <?= ($sort != 'top') ? 'disabled' : '' ?>" href="<?= @route($location->getURL().'&sort=recent') ?>">
                  <i class="icon-time"></i>
                  <?= @text('LIB-AN-SORT-RECENT') ?>
              </a>
              <a class="btn <?= ($sort == 'top') ? 'disabled' : '' ?>" href="<?= @route($location->getURL().'&sort=top') ?>">
                  <i class="icon-fire"></i>
                  <?= @text('LIB-AN-SORT-TOP') ?>
              </a>
          </div>
      </div>
    </div>
</div>

<?
$url = $location->getURL().'&layout=taggables';

if (!empty($sort)) {
    $url .= '&sort='.$sort;
}

if (!empty($scope)) {
    $url .= '&scope='.$scope;
}
?>

<?= @infinitescroll($item->tagables->fetchSet(), array(
  'url' => $url,
  'id' => 'an-geolocatables'
)) ?>
