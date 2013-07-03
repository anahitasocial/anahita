<?php defined('KOOWA') or die('Restricted access'); ?>

<?php if(count($orders)): ?>
<table class="table table-striped">
	<thead>
		<tr>				
			<th><?= @text('COM-SUB-TRANSACTION-ID'); ?></th>
			<th><?= @text('COM-SUB-TRANSACTION-SUBSCRIPTION'); ?></th>
			<th><?= @text('COM-SUB-TRANSACTION-PRICE'); ?></th>
			<th><?= @text('COM-SUB-TRANSACTION-DISCOUNT'); ?></th>
			<th><?= @text('COM-SUB-TRANSACTION-TAX'); ?></th>
			<th><?= @text('COM-SUB-TRANSACTION-PAID'); ?></th>
			<th><?= @text('COM-SUB-TRANSACTION-BILLING-PERIOD'); ?></th>
			<th><?= @text('COM-SUB-TRANSACTION-METHOD'); ?></th>
			<th><?= @text('COM-SUB-TRANSACTION-DATE'); ?></th>
		</tr>
	</thead>

	<?php foreach($orders as $order): ?>
	<tr>
		<td>
			<a href="<?= @route('view=order&id='.$order->id) ?>">
				<?= $order->itemId ?>
			</a>
		</td>
		<td><?= @escape($order->itemName); ?></td>
		<td><?= $order->itemAmount ?></td>
		<td>- <?= round($order->discountAmount, 2) ?></td>
		<td>+ <?= round($order->taxAmount, 2)?></td>
		<td><?= round($order->getTotalAmount(), 2) ?> <?= $order->currency ?></td>
		<td><i class="icon-<?= ($order->recurring) ? 'repeat' : 'ok-circle' ?>"></i> <?= ($order->recurring) ? @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$order->billingPeriod) : @text('COM-SUB-BILLING-PERIOD-'.$order->billingPeriod) ?></td>
		<td><?= $order->method?></td>					
		<td><?= $order->createdOn->getDate('%b %d %Y')?></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php else: ?>
<div class="alert alert-info">
<?= @text('COM-SUB-TRANSACTION-EMPTY-LIST') ?>
</div>
<?php endif; ?>
