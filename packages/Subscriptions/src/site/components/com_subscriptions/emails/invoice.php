<h1><?= @text('COM-SUBSCRIPTIONS-INVOICE') ?></h1>

<p><?= @text('COM-SUBSCRIPTIONS-INVOICE-DESCRIPTION') ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-INVOICE-BILLED-TO') ?></strong>: <?= @escape($person->name) ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-INVOICE-SUBSCRIBED-TO') ?></strong>: <?= stripslashes($order->itemName) ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-DATE') ?></strong>: <?= $order->createdOn->format('%D') ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-PAID') ?></strong>: <?= round($order->getTotalAmount(), 2) ?> <?= $order->currency ?></p>

<p><strong><?= @text('COM-SUBSCRIPTIONS-BILLING-PERIOD'); ?></strong>: <?= ($order->recurring) ? @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-RECURRING-'.$order->billingPeriod) : @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-'.$order->billingPeriod) ?></p>

<p><strong><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-ID') ?></strong>: <?= $order->orderId ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-METHOD') ?></strong>: <?= $order->method ?></p>

<p>&nbsp;</p>

<?php if(!empty($contact) && !empty($contact->address)) : ?>
<p><strong><?= @text('COM-SUBSCRIPTIONS-BILLING-ADDR') ?></strong>: <?= $contact->address ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-BILLING-CITY') ?></strong>: <?= $contact->city ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-BILLING-STATE') ?></strong>: <?= $contact->state ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-BILLING-COUNTRY') ?></strong>: <?= $contact->country ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-BILLING-ZIP') ?></strong>: <?= $contact->zip ?></p>
<?php endif; ?>
