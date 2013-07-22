<?php defined('KOOWA') or die('Restricted access'); ?>
<script	src="lib_koowa/js/koowa.js" />

<form id="" action="<?= @route()?>" method="post" class="-koowa-grid " data-token-name="_token" data-token-value="<?=JUtility::getToken()?>">
	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="1%"><?= @text('NUM'); ?></th>
				<th width="1%"><input name="checkall" type="checkbox" class="-koowa-grid-checkall" /></th>
				<th width="40%"><?= @helper('grid.sort', array('column'=>'name', 'sort'=>$sort, 'direction'=>$direction)); ?></th>
				<th width="5%"><?= @helper('grid.sort', array('column'=>'order', 	  'sort'=>$sort,   'direction'=>$direction)); ?></th>
				<th width="1%"><?= @helper('grid.sort',  array('column'=>'id', 'sort'=>$sort, 'direction'=>$direction)); ?></th>
				<td width="33%">&nbsp;</td>
			</tr>
		</thead>
		
		<tbody>
			<? foreach ($apps as $i => $app) : ?>
			<tr class="-koowa-grid-checkbox">
				<td align="center"><?= $i + 1; ?></td>
				<td align="center"><?= @helper('grid.checkbox', array('row'=>$app)); ?></td>
				<td>
					<span class="editlinktip hasTip" title="<?= @escape($app->getName()); ?>">
						<a href="<?= @route('layout=form&view=app&id='.$app->id.'&hidemainmenu=1')?>">
							<?= @escape($app->getName()); ?>
						</a>
					</span>
				</td>
				
				<td align="center">
					<?= @helper('grid.order', $app); ?>		
				</td>
				<td align="center"><?= $app->id; ?></td>
				<td>&nbsp;</td>
			</tr>
			<?php endforeach;?>
		<?php if (!$apps->getTotal()) : ?>
			<tr>
				<td colspan="4" align="center">
					<?= @text('No Apss Found'); ?>
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
