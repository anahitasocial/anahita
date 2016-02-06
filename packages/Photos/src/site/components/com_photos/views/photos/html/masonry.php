<?php defined('KOOWA') or die('Restricted access');?>

<?= @helper('ui.header', array()) ?>

<?php if (count($photos)) : ?>
<div id="an-photos" class="an-entities masonry" data-trigger="InfiniteScroll" data-url="<?= @route('layout=list') ?>">
  <div class="row">

    <?php $view = @view('photo')->layout('masonry')->filter($filter); ?>

    <?php $i = 0; ?>

    <div class="span6">
    <?php foreach ($photos as $photo) : ?>
    		<?php if(($i % 2) == 0) : ?>
    		<?= $view->photo($photo); ?>
    		<?php endif; ?>
    		<?php $i++; ?>
    <?php endforeach; ?>
    </div>

    <div class="span6">
    <?php foreach ($photos as $photo) : ?>
    		<?php if(($i % 2) == 1) : ?>
    		<?= $view->photo($photo); ?>
    		<?php endif; ?>
    		<?php $i++; ?>
    <?php endforeach; ?>
    </div>

  </div>

  <div class="well InfiniteScrollReadmore">
      <?php $start += $limit; ?>
      <a href="<?= @route('layout=masonry&start='.$start.'&limit='.$limit) ?>">
        <?= @text('LIB-AN-READMORE') ?>
      </a>
  </div>
</div>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
