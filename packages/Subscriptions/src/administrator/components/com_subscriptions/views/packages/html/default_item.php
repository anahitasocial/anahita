<? defined('KOOWA') or die('Restricted access'); ?>

<?php $i = 0; $m = 0; ?>
<?php foreach ($packages as $package) : ?>
<tr class="<?php echo 'row'.$m; ?>">
	<td align="center"><?= $i + 1; ?></td>
	<td align="center"><?= @helper('grid.checkbox', array('row'=>$package)); ?></td>
	<td>
		<a href="<?= @route('view=package&id='.$package->id)?>">
			<?= @escape($package->getName()); ?>				
		</a>
	</td>
	<td align="center">
		<?= @helper('grid.enable', array('row'=>$package, 'url'=>@route('view=package&id='.$package->id))); ?>
	</td>
	<td align="center">
		<?= @helper('grid.order', array('row'=>$package, 'url'=>@route('view=package&id='.$package->id))); ?>
	</td>
	<td align="center"><?=$package->subscriptions->getTotal()?></td>
	<td align="center"><?= $package->price ?> <?= get_config_value('subscriptions.currency','US') ?></td>
	
	<td align="center"><?= ($package->recurring) ? @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$package->billingPeriod) : @text('COM-SUB-BILLING-PERIOD-'.$package->billingPeriod) ?></td>
	<td align="center"><?= $package->id; ?></td>
</tr>
<? $i = $i + 1; $m = (1 - $m); ?>
<? endforeach; ?>