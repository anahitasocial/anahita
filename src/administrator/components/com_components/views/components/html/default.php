<?php defined('KOOWA') or die('Restricted access'); ?>

<form action="<?= @route()?>" method="post" class="-koowa-grid" data-token-name="_token" data-token-value="<?=JUtility::getToken()?>">
	<table class="adminlist mc-list-table mc-second-table" style="clear: both;">
		<thead>
			<tr>
				<th width="1%"><?= @text('NUM'); ?></th>	
				<th width="1%"><input name="checkall" type="checkbox" class="-koowa-grid-checkall" /></th>				
				<th width="90%"><?= @text('Name')?></th>
				<th width="5%"><?= @helper('grid.sort', array('column'=>'order', 'sort'=>$sort, 'direction'=>$direction)); ?></th>
				<th width="4%"><?= @text('ID')?></th>				
			</tr>
		</thead>
		<tbody>
		<?= @template('default_item') ?>
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
