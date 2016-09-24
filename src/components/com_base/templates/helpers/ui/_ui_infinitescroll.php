<? defined('KOOWA') or die('Restricted access') ?>

<div id="<?= $id ?>" class="an-entities masonry" data-columns="<?= $columns ?>" data-trigger="InfiniteScroll" data-url="<?= @route($url) ?>">

  <? if(isset($entities)) : ?>
  <div class="row-fluid">
    <? $view = @view($entity_type)->layout($layout_item)->filter($filter); ?>
    <? for($i = 0; $i < $columns; $i++): ?>
    <div class="span<?= 12 / $columns ?>">
    <? $k = 0; ?>
    <? foreach ($entities as $entity) : ?>
    		<? if(($k % $columns) == $i) : ?>
    		<?= $view->$entity_type($entity); ?>
    		<? endif; ?>
    		<? $k++; ?>
    <? endforeach; ?>
    </div>
    <? endfor; ?>
  </div>
  <? endif; ?>
  <? if($hiddenlink && count($entities) >= $limit) : ?>
  <div class="well InfiniteScrollReadmore">
      <? $start += $limit; ?>
      <a href="<?= @route($url) ?>">
        <?= @text('LIB-AN-READMORE') ?>
      </a>
  </div>
  <? endif; ?>
</div>
