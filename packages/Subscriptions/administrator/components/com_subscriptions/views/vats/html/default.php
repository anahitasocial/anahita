<?php defined('KOOWA') or die('Restricted access');?>

<form action="<?= @route()?>" method="post" class="-koowa-grid" data-token-name="_token" data-token-value="<?=JUtility::getToken()?>">
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="action" value="" />
	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>				
				<th width="1%"><?= @text('NUM'); ?></th>
				<th width="1%"><input name="checkall" type="checkbox" class="-koowa-grid-checkall" /></th>							
				<th width="90%"><?= @helper('grid.sort', array('title'=>@text('AN-SB-VAT-COUNTRY'),'order'=>$sort)); ?></th>
				<th width="1%"><?= @helper('grid.sort', array('column'=>'id','order'=>$sort)); ?></th>
			</tr>
		</thead>
		
		<tbody>
		<? $i = 0; $m = 0; ?>
		<? foreach ($vats as $vat) : ?>
		<tr class="<?php echo 'row'.$m; ?>">
			<td align="center"><?= $i + 1; ?></td>
			<td align="center"><?= @helper('grid.checkbox', array('row'=>$vat)); ?></td>
			<td>
				<a href="<?=@route(array('id'=>$vat->id,'view'=>'vat')) ?>">
				<?= @text(LibBaseTemplateHelperSelector::$COUNTRIES[$vat->country]) ?>		
				</a>
			</td>
			<td align="center"><?= $vat->id; ?></td>
		</tr>
		<? $i = $i + 1; $m = (1 - $m); ?>
		<? endforeach; ?>
		<?php if (!$vats->getTotal()) : ?>
			<tr>
				<td colspan="4" align="center">
					<?= @text('AN-SB-NO-VATS'); ?>
				</td>
			</tr>
		<?php endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">
					&nbsp;
				</td>
			</tr>
		</tfoot>
	</table>
</form>
