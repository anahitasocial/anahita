<?php defined('KOOWA') or die('Restricted access'); ?>

<div class="row">
	<div class="span8">   	
        <div class="an-entity">
        	<h3 class="entity-title"><?= @escape($oder->itemName) ?></h3>
        
        	<dl>
        		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-ID'); ?></dt>
        		<dd><?= $oder->itemId ?></dd>
        		
        		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-PRICE'); ?></dt>
        		<dd><?= $oder->itemAmount ?></dd>
        		
        		<?php if($oder->discountAmount): ?>
        		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-DISCOUNT'); ?></dt>
        		<dd>- <?= round($oder->discountAmount, 2) ?></dd>
        		<?php endif; ?>
        		
        		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-TAX'); ?></dt>
        		<dd>+ <?= round($oder->taxAmount, 2) ?></dd>
        		
        		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-PAID'); ?></dt>
        		<dd><?= round($oder->getTotalAmount(), 2) ?></dd>
        		
        		<dt><?= @text('COM-SUBSCRIPTIONS-BILLING-PERIOD'); ?></dt>
        		<dd><?= ($oder->recurring) ? @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-RECURRING-'.$oder->billingPeriod) : @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-'.$oder->billingPeriod) ?></dd>
        		
        		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-METHOD'); ?></dt>
        		<dd><?= $oder->method?></dd>
        		
        		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-DATE'); ?></dt>
        		<dd><?= $oder->createdOn->getDate('%b %d %Y')?></dd>
        		
        		<dt><?= @text('COM-SUBSCRIPTIONS-TRANSACTION-DURATION'); ?></dt>
        		<dd><?= AnHelperDate::secondsTo('day', $oder->duration)?> <?= @text('COM-SUBSCRIPTIONS-TRANSACTION-DAYS') ?></dd>
        
        	</dl>
        </div>
	</div>
</div>