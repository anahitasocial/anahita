<? defined('KOOWA') or die('Restricted access'); ?>

<? $i = 0; $m = 0; ?>
<? foreach ($packages as $package) : ?>
<tr class="<?php echo 'row'.$m; ?>">
	<td align="center"><?= $i + 1; ?></td>
	<td align="center"><?= @helper('grid.checkbox', array('row'=>$package)); ?></td>
	<td>
		<span class="editlinktip hasTip" title="<?= @escape($package->getName()); ?>">
			<a href="<?= @route('view=package&id='.$package->id)?>">
				<?= @escape($package->getName()); ?>				
			</a>
		</span>
	</td>
	<td align="center">
		<?= @helper('grid.enable', $package, array('url'=>@route('view=package&id='.$package->id))); ?>
	</td>
	<td align="center">
		<?= @helper('grid.order',  $package, array('url'=>@route('view=package&id='.$package->id))); ?>
	</td>
	<td align="center"><?=$package->subscriptions->getTotal()?></td>
	<td align="center"><?= $package->price ?> <?= get_config_value('subscriptions.currency','US') ?></td>
	
	<td align="center"><?= ($package->recurring) ? @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$package->billingPeriod) : @text('COM-SUB-BILLING-PERIOD-'.$package->billingPeriod) ?></td>
	<td align="center"><?= $package->id; ?></td>
</tr>
<? $i = $i + 1; $m = (1 - $m); ?>
<? endforeach; ?>