<? defined('KOOWA') or die('Restricted access'); ?>

<div class="row">
	<div class="span8">

	    <?= @helper('ui.header'); ?>

        <div class="an-entity">
        	<h2 class="entity-title">
        	    <?= @escape($order->itemName) ?>
        	</h2>

            <div class="entity-description">
            	<dl>
            	    <dt><?= @text('COM-SUBSCRIPTIONS-INVOICE-BILLED-TO') ?></dt>
            	    <dd><?= @escape($actor->name) ?></dd>

            	    <dt><?= @text('COM-SUBSCRIPTIONS-INVOICE-SUBSCRIBED-TO') ?></dt>
            	    <dd><?= @escape($order->itemName) ?></dd>

            		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-ID'); ?></dt>
            		<dd><?= $order->itemId ?></dd>

            		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-PRICE'); ?></dt>
            		<dd><?= $order->itemAmount ?></dd>

            		<? if ($order->discountAmount): ?>
            		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-DISCOUNT'); ?></dt>
            		<dd>- <?= round($order->discountAmount, 2) ?></dd>
            		<? endif; ?>

            		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-TAX'); ?></dt>
            		<dd>+ <?= round($order->taxAmount, 2) ?></dd>

            		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-PAID'); ?></dt>
            		<dd><?= round($order->getTotalAmount(), 2) ?></dd>

            		<dt><?= @text('COM-SUBSCRIPTIONS-BILLING-PERIOD'); ?></dt>
            		<dd><?= ($order->recurring) ? @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-RECURRING-'.$order->billingPeriod) : @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-'.$order->billingPeriod) ?></dd>

            		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-METHOD'); ?></dt>
            		<dd><?= $order->method ?></dd>

            		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-DATE'); ?></dt>
            		<dd><?= $order->createdOn->getDate('%b %d %Y')?></dd>

            		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-DURATION'); ?></dt>
            		<dd><?= round(AnHelperDate::secondsTo('day', $order->duration)) ?> <?= @text('COM-SUBSCRIPTIONS-TRANSACTION-DAYS') ?></dd>

                    <dt><?= @text('COM-SUBSCRIPTIONS-BILLING-COUNTRY') ?></dt>
                    <dd><?= $order->country ?></dd>
            	</dl>
        	</div>
        </div>
	</div>
</div>
