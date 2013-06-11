<?php defined('KOOWA') or die('Restricted access');?>


<form action="<?= @route()?>" method="post" class="-koowa-grid">
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="action" value="" />
	<table class="adminlist mc-list-table mc-second-table" cellspacing="1">
		<thead>
			<tr>				
				<th width="1%"><?= @text('NUM'); ?></th>
				<th width="1%"><input name="checkall" type="checkbox" class="-koowa-grid-checkall" /></th>
				<th width="10%"><?= @helper('grid.sort', array('title'=>@text('AN-SB-COUPON-CODE'),	'column'=>'code','order'=>$sort)); ?></th>
				<th width="20%"><?= @helper('grid.sort', array('title'=>@text('AN-SB-COUPON-DISCOUNT'),'column'=>'discount','order'=>$sort)); ?></th>				
				<th width="10%"><?= @helper('grid.sort', array('title'=>@text('AN-SB-COUPON-LIMIT'),'column'=>'limit','order'=>$sort)); ?></th>						
				<th width="1%"><?= @helper('grid.sort', array('column'=>'id','state'=>$sort)); ?></th>
			</tr>
		</thead>
		
		<tbody>
		<?= @template('default_item') ?>
		<?php if (!$coupons->getTotal()) : ?>
			<tr>
				<td colspan="6" align="center">
					<?= @text('AN-SB-NO-COUPON'); ?>
				</td>
			</tr>
		<?php endif; ?>
		</tbody>
		
		<tfoot>
			<tr>
				<td colspan="6">
					&nbsp;
				</td>
			</tr>
		</tfoot>
	</table>
</form>
