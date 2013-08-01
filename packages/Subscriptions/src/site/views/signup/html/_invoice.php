<?php defined('KOOWA') or die('Restricted access'); ?>

<h1><?= @text('COM-SUB-INVOICE') ?></h1>

<p><?= @text('COM-SUB-INVOICE-DESCRIPTION') ?></p>
<p><strong><?= @text('COM-SUB-INVOICE-BILLED-TO') ?></strong>: <?= @escape($name) ?></p>
<p><strong><?= @text('COM-SUB-INVOICE-SUBSCRIBED-TO') ?></strong>: <?= stripslashes($transaction->itemName) ?></p>
<p><strong><?= @text('COM-SUB-TRANSACTION-DATE') ?></strong>: <?= $transaction->createdOn->format('%D') ?></p>
<p><strong><?= @text('COM-SUB-TRANSACTION-PAID') ?></strong>: <?= round($transaction->getTotalAmount(), 2) ?> <?= $transaction->currency ?></p>

<p><strong><?= @text('COM-SUB-BILLING-PERIOD'); ?></strong>: <?= ($transaction->recurring) ? @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$transaction->billingPeriod) : @text('COM-SUB-BILLING-PERIOD-'.$transaction->billingPeriod) ?></p>

<p><strong><?= @text('COM-SUB-TRANSACTION-ID') ?></strong>: <?= $transaction->orderId ?></p>
<p><strong><?= @text('COM-SUB-TRANSACTION-METHOD') ?></strong>: <?= $transaction->method ?></p>

<p>&nbsp;</p>

<?php if(!empty($contact) && !empty($contact->address)) : ?>
<p><strong><?= @text('COM-SUB-BILLING-ADDR') ?></strong>: <?= $contact->address ?></p>
<p><strong><?= @text('COM-SUB-BILLING-CITY') ?></strong>: <?= $contact->city ?></p>
<p><strong><?= @text('COM-SUB-BILLING-STATE') ?></strong>: <?= $contact->state ?></p>
<p><strong><?= @text('COM-SUB-BILLING-COUNTRY') ?></strong>: <?= $contact->country ?></p>
<p><strong><?= @text('COM-SUB-BILLING-ZIP') ?></strong>: <?= $contact->zip ?></p>
<?php endif; ?>


