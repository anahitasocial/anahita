<?php defined('KOOWA') or die('Restricted access');?>
<?php 
$transactions = @service('repos:subscriptions.transaction')->fetchSet(array('actorId'=>$person->id));
?>
<?php if ( count($transactions) ) : ?>
<fieldset class="adminform">
	<legend><?= JText::_( 'AN-SB-PERSON-TRANSACTIONS' ); ?></legend>
	<?php $sort = !empty($sort) ? $sort : null ?>

	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>				
				<th width="1%"><?= @text('NUM'); ?></th>
				<th width="30%"><?= @helper('grid.sort', array('title'=>@text('AN-SB-TRANSACTION-ITEM-NAME'),'sort'=>$sort)); ?></th>
				<th width="10%"><?= @helper('grid.sort', array('title'=>@text('AN-SB-TRANSACTION-ITEM-AMOUNT'),'sort'=>$sort)); ?></th>
				<th width="5%"><?= @helper('grid.sort', array('title'=>@text('AN-SB-TRANSACTION-TAX-AMOUNT'),'sort'=>$sort)); ?></th>
				<th width="5%"><?= @helper('grid.sort', array('title'=>@text('AN-SB-TRANSACTION-DISCOUNT-AMOUNT'),'sort'=>$sort)); ?></th>
				<th width="10%"><?= @helper('grid.sort', array('title'=>@text('AN-SB-TRANSACTION-PAYMENT-METHOD'),'sort'=>$sort)); ?></th>
				<th width="20%"><?= @helper('grid.sort', array('title'=>@text('AN-SB-TRANSACTION-TIMESTAMP'),'sort'=>$sort)); ?></th>
				<th width="10%"><?= @text('COM-SUB-BILLING-PERIOD'); ?></th>
				<th width="10%"><?= @text('AN-SB-TRANSACTION-DURATION'); ?></th>				
			</tr>
		</thead>
		<tbody>
			<?php foreach($transactions as $transaction) : ?>
				<tr>
					<td><?=$transaction->id?></td>
					<td>
						<?=$transaction->itemName?>
					</td>
					<td align="center"><?=$transaction->itemAmount.' '.$transaction->currency?></td>
					<td align="center"><?=$transaction->taxAmount?></td>
					<td align="center"><?=$transaction->discountAmount?></td>
					<td align="center"><?=$transaction->method?></td>					
					<td align="center"><?= $transaction->createdOn->getDate('%b/%d/%y - %T')?></td>
					<td align="center"><?= ($transaction->recurring) ? @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$transaction->billingPeriod) : @text('COM-SUB-BILLING-PERIOD-'.$transaction->billingPeriod) ?></td>
					<td align="center"><?= floor(AnHelperDate::secondsTo('day', $transaction->duration))?> <?=@text('AN-SB-TRANSACTION-DURATION-DAYS') ?></td>
				</tr>
			<?php endforeach; ?>			
		</tbody>
	</table>
</fieldset>
<?php endif ?>