<?php defined('KOOWA') or die('Restricted access'); ?>

<module position="sidebar-b" style="none"></module>

<div class="an-entity">
	<h3 class="entity-title"><?= @escape($oder->itemName) ?></h3>

	<dl>
		<dt><?= @text('COM-SUB-TRANSACTION-ID'); ?></dt>
		<dd><?= $oder->itemId ?></dd>
		
		<dt><?= @text('COM-SUB-TRANSACTION-PRICE'); ?></dt>
		<dd><?= $oder->itemAmount ?></dd>
		
		<?php if($oder->discountAmount): ?>
		<dt><?= @text('COM-SUB-TRANSACTION-DISCOUNT'); ?></dt>
		<dd>- <?= round($oder->discountAmount, 2) ?></dd>
		<?php endif; ?>
		
		<dt><?= @text('COM-SUB-TRANSACTION-TAX'); ?></dt>
		<dd>+ <?= round($oder->taxAmount, 2) ?></dd>
		
		<dt><?= @text('COM-SUB-TRANSACTION-PAID'); ?></dt>
		<dd><?= round($oder->getTotalAmount(), 2) ?></dd>
		
		<dt><?= @text('COM-SUB-BILLING-PERIOD'); ?></dt>
		<dd><?= ($oder->recurring) ? @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$oder->billingPeriod) : @text('COM-SUB-BILLING-PERIOD-'.$oder->billingPeriod) ?></dd>
		
		<dt><?= @text('COM-SUB-TRANSACTION-METHOD'); ?></dt>
		<dd><?= $oder->method?></dd>
		
		<dt><?= @text('COM-SUB-TRANSACTION-DATE'); ?></dt>
		<dd><?= $oder->createdOn->getDate('%b %d %Y')?></dd>
		
		<dt><?= @text('COM-SUB-TRANSACTION-DURATION'); ?></dt>
		<dd><?= AnHelperDate::secondsTo('day', $oder->duration)?> <?= @text('COM-SUB-TRANSACTION-DAYS') ?></dd>

	</dl>
</div>