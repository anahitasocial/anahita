<?php defined('KOOWA') or die('Restricted access'); ?>

<form action="<?= @route()?>" method="post" class="-koowa-grid" data-token-name="_token" data-token-value="<?=JUtility::getToken()?>">
    <table class="adminlist mc-list-table mc-second-table" style="clear: both;">
		<thead>
			<tr>
				<th width="1%"><?= @text('NUM'); ?></th>
				<th width="1%"><input name="checkall" type="checkbox" class="-koowa-grid-checkall" /></th>							
				<th width="20%"><?= @helper('grid.sort', array('column'=>'name','sort'=>$sort, 'direction'=>$direction)); ?></th>
				<th width="1%"><?= @text('Published')?></th>
				<th width="1%"><?= @helper('grid.sort', array('column'=>'order','sort'=>$sort, 'direction'=>$direction)); ?></th>								
				<th width="10%"><?= @text('AN-SB-PACKAGE-NUM-SUBSCRIPTIONS') ?></th>
				<th width="10%"><?= @helper('grid.sort', array('column'=>'price','sort'=>$sort, 'direction'=>$direction)); ?></th>
				<th width="10%"><?= @text('COM-SUB-BILLING-PERIOD') ?></th>		
				<th width="1%"><?= @helper('grid.sort', array('column'=>'id','sort'=>$sort, 'direction'=>$direction)); ?></th>
			</tr>
		</thead>
		
		<tbody>
		<?= @template('default_item') ?>
		<?php if (!$packages->getTotal()) : ?>
			<tr>
				<td colspan="9" align="center">
					<?= @text('AN-SB-NO-PACKAGE'); ?>
				</td>
			</tr>
		<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="9">
					&nbsp;
				</td>
			</tr>
		</tfoot>
	</table>
</form>
