<?php defined('KOOWA') or die('Restricted access');?>

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
