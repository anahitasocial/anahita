<? defined('KOOWA') or die('Restricted access');?>

<? $commands = $toolbar->getCommands(); ?>

<div class="btn-toolbar clearfix">
    <? if ($new = $commands->extract('new')) :?>
    <?= @html('tag', 'a', $new->label, $new->getAttributes())->class('btn btn-primary') ?>
    <? endif;?>

    <div class="pull-right btn-group">
        <?
            $sort_types = array(
                'newest' => array(
                  'label' => 'LIB-AN-SORT-NEWEST',
                  'icon' => 'time'
                  ),
                  'priority' => array(
                    'label' => 'LIB-AN-SORT-PRIORITY',
                    'icon' => 'fire'
                  ),
                  'updated' => array(
                    'label' => 'LIB-AN-SORT-UPDATED',
                    'icon' => 'edit'
                  )
              );
        ?>
        <? foreach($sort_types as $i => $sort_type) : ?>
        <a class="btn <?= ($i == $sort) ? 'disabled' : '' ?>" href="<?= @route(array('sort'=>$i)) ?>">
            <i class="icon icon-<?= $sort_type['icon'] ?>"></i>
            <?= @text($sort_type['label']) ?>
        </a>
        <? endforeach; ?>
    </div>
</div>

<?= @helper('ui.filterbox', @route('layout=list')) ?>
