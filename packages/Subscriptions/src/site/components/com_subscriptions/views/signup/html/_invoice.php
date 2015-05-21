<?php defined('KOOWA') or die('Restricted access'); ?>

<h1><?= @text('COM-SUBSCRIPTIONS-INVOICE') ?></h1>

<p><?= @text('COM-SUBSCRIPTIONS-INVOICE-DESCRIPTION') ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-INVOICE-BILLED-TO') ?></strong>: <?= @escape($name) ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-INVOICE-SUBSCRIBED-TO') ?></strong>: <?= stripslashes($transaction->itemName) ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-DATE') ?></strong>: <?= $transaction->createdOn->format('%D') ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-PAID') ?></strong>: <?= round($transaction->getTotalAmount(), 2) ?> <?= $transaction->currency ?></p>

<p><strong><?= @text('COM-SUBSCRIPTIONS-BILLING-PERIOD'); ?></strong>: <?= ($transaction->recurring) ? @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-RECURRING-'.$transaction->billingPeriod) : @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-'.$transaction->billingPeriod) ?></p>

<p><strong><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-ID') ?></strong>: <?= $transaction->orderId ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-METHOD') ?></strong>: <?= $transaction->method ?></p>

<p>&nbsp;</p>

<?php if(!empty($contact) && !empty($contact->address)) : ?>
<p><strong><?= @text('COM-SUBSCRIPTIONS-BILLING-ADDR') ?></strong>: <?= $contact->address ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-BILLING-CITY') ?></strong>: <?= $contact->city ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-BILLING-STATE') ?></strong>: <?= $contact->state ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-BILLING-COUNTRY') ?></strong>: <?= $contact->country ?></p>
<p><strong><?= @text('COM-SUBSCRIPTIONS-BILLING-ZIP') ?></strong>: <?= $contact->zip ?></p>
<?php endif; ?>


