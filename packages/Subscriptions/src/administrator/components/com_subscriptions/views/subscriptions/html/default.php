<?php defined('KOOWA') or die('Restricted access'); ?>

<table class="adminlist mc-list-table mc-second-table" cellspacing="1">
    
    <thead>
        <tr>                
            <th width="1%"><?= @text('NUM'); ?></th>
            <th><?= @text('Person') ?></th>
            <th><?= @text('Package') ?></th>
            <th><?= @text('Expired') ?></th>
            <th width="1%"><?= @helper('grid.sort', array('column'=>'id','sort'=>$sort)); ?></th>
        </tr>
    </thead>
    
    <?php $i = 0; ?>
    <?php foreach( $items as $item ) : ?>
    <tr>
        <td align="center">
            <?= $i + 1 + $items->getOffset(); ?>
        </td>
        <td><?= $item->person->name ?></td>
        <td><?= $item->package->name ?></td>
        <td align="center"><?= ($item->expired() == 1 ) ?  '<img src="images/publish_x.png" width="16" height="16" border="0" alt="Yes" />' : '<img src="images/tick.png" width="16" height="16" border="0" alt="No" />' ?></td>
        <td align="center"><?= $item->id ?></td>
    </tr>
    <? $i = $i + 1; ?>
    <?php endforeach; ?>
    
    <tfoot>
        <tr>
            <td colspan="5">
                <?= @helper('paginator.pagination', array('total'=>$items->getTotal(), 'offset'=>$items->getOffset(), 'limit'=>$items->getLimit())) ?>
            </td>
        </tr>
    </tfoot>
</table>