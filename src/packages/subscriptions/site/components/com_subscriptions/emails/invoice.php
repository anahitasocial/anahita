<h1><?= @text('COM-SUB-INVOICE') ?></h1>

<p><?= @text('COM-SUB-INVOICE-DESCRIPTION') ?></p>
<p><strong><?= @text('COM-SUB-INVOICE-BILLED-TO') ?></strong>: <?= @escape($person->name) ?></p>
<p><strong><?= @text('COM-SUB-INVOICE-SUBSCRIBED-TO') ?></strong>: <?= stripslashes($order->itemName) ?></p>
<p><strong><?= @text('COM-SUB-TRANSACTION-DATE') ?></strong>: <?= $order->createdOn->format('%D') ?></p>
<p><strong><?= @text('COM-SUB-TRANSACTION-PAID') ?></strong>: <?= round($order->getTotalAmount(), 2) ?> <?= $order->currency ?></p>

<p><strong><?= @text('COM-SUB-BILLING-PERIOD'); ?></strong>: <?= ($order->recurring) ? @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$order->billingPeriod) : @text('COM-SUB-BILLING-PERIOD-'.$order->billingPeriod) ?></p>

<p><strong><?= @text('COM-SUB-TRANSACTION-ID') ?></strong>: <?= $order->orderId ?></p>
<p><strong><?= @text('COM-SUB-TRANSACTION-METHOD') ?></strong>: <?= $order->method ?></p>

<p>&nbsp;</p>

<?php if(!empty($contact) && !empty($contact->address)) : ?>
<p><strong><?= @text('COM-SUB-BILLING-ADDR') ?></strong>: <?= $contact->address ?></p>
<p><strong><?= @text('COM-SUB-BILLING-CITY') ?></strong>: <?= $contact->city ?></p>
<p><strong><?= @text('COM-SUB-BILLING-STATE') ?></strong>: <?= $contact->state ?></p>
<p><strong><?= @text('COM-SUB-BILLING-COUNTRY') ?></strong>: <?= $contact->country ?></p>
<p><strong><?= @text('COM-SUB-BILLING-ZIP') ?></strong>: <?= $contact->zip ?></p>
<?php endif; ?>
