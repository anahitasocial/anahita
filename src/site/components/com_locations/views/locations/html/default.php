<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>
<?= @helper('ui.filterbox', @route('layout=list')) ?>

<div class="an-entities masonry" data-trigger="InfiniteScroll" data-url="<?= @route('layout=list&sort='.$sort) ?>">
  <div class="row">
    <?= @template('list') ?>
  </div>

  <div class="well InfiniteScrollReadmore">
      <?php $start += $limit; ?>
      <a href="<?= @route('start='.$start.'&limit='.$limit) ?>">
        <?= @text('LIB-AN-READMORE') ?>
      </a>
  </div>
</div>
