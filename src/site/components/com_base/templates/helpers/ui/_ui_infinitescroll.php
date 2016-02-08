<?php defined('KOOWA') or die('Restricted access') ?>

<div id="<?= $id ?>" class="an-entities masonry" data-columns="<?= $columns ?>" data-trigger="InfiniteScroll" data-url="<?= @route($url) ?>">

  <?php if(isset($entities)) : ?>
  <div class="row-fluid">
    <?php $view = @view($entity_type)->layout($layout_item)->filter($filter); ?>
    <?php for($i = 0; $i < $columns; $i++): ?>
    <div class="span<?= 12 / $columns ?>">
    <?php $k = 0; ?>
    <?php foreach ($entities as $entity) : ?>
    		<?php if(($k % $columns) == $i) : ?>
    		<?= $view->$entity_type($entity); ?>
    		<?php endif; ?>
    		<?php $k++; ?>
    <?php endforeach; ?>
    </div>
    <?php endfor; ?>
  </div>
  <?php endif; ?>

  <?php if($hiddenlink) : ?>
  <div class="well InfiniteScrollReadmore">
      <?php $start += $limit; ?>
      <a href="<?= @route('layout='.$layout_list.'&start='.$start.'&limit='.$limit) ?>">
        <?= @text('LIB-AN-READMORE') ?>
      </a>
  </div>
  <?php endif; ?>
</div>
