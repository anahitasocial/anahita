<? defined('KOOWA') or die('Restricted access'); ?>

<?= @helper('ui.header'); ?>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-ID'); ?></th>
			<th><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-SUBSCRIPTION'); ?></th>
			<th><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-PAID'); ?></th>
			<th><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-BILLING-PERIOD'); ?></th>
			<th><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-METHOD'); ?></th>
			<th><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-DATE'); ?></th>
		</tr>
	</thead>

	<? foreach ($orders as $order): ?>
	<tr>
		<td>
			<a href="<?= @route('view=order&id='.$order->id) ?>">
				<?= $order->itemId ?>
			</a>
		</td>
		<td><?= @escape($order->itemName); ?></td>
		<td><?= round($order->getTotalAmount(), 2) ?> <?= $order->currency ?></td>
		<td><i class="icon-<?= ($order->recurring) ? 'repeat' : 'ok-circle' ?>"></i> <?= ($order->recurring) ? @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-RECURRING-'.$order->billingPeriod) : @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-'.$order->billingPeriod) ?></td>
		<td><?= $order->method?></td>
		<td><?= $order->createdOn->getDate('%b %d %Y')?></td>
	</tr>
	<? endforeach; ?>
</table>

<? if (count($orders) == 0): ?>
<div class="alert alert-info">
<?= @text('COM-SUBSCRIPTIONS-TRANSACTION-EMPTY-LIST') ?>
</div>
<? endif; ?>
