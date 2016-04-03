<?php defined('KOOWA') or die('Restricted access');?>

<?php $commands = $toolbar->getCommands(); ?>

<div class="btn-toolbar clearfix">
    <?php if ($new = $commands->extract('new')) :?>
    <?= @html('tag', 'a', $new->label, $new->getAttributes())->class('btn btn-primary') ?>
    <?php endif;?>

    <div class="pull-right btn-group">
        <?php
            $sort_types = array(
              'trending' => array(
                'label' => 'LIB-AN-SORT-TRENDING',
                'icon' => 'fire'
              ),
              'top' => array(
                'label' => 'LIB-AN-SORT-TOP',
                'icon' => 'fire'
              ),
              'recent' => array(
                'label' => 'LIB-AN-SORT-RECENT',
                'icon' => 'time'
              ),
            );
        ?>
        <?php foreach($sort_types as $i => $sort_type) : ?>
        <a class="btn <?= ($i == $sort) ? 'disabled' : '' ?>" href="<?= @route(array('sort'=>$i)) ?>">
            <?= @text($sort_type['label']) ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>
