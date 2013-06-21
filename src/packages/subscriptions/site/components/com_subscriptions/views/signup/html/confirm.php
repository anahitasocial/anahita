<?php defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step'=>'confirm')) ?>

<module position="sidebar-b" style="none"></module>

<div id="sub-signup-confirm">
	<form class="form-horizontal" action="<?= @route(array('id'=>$item->id)); ?>" method="post" name="sub-login" id="login">
		<input type="hidden" name="action" value="process">
		
		<div class="alert alert-info"><?= @text('COM-SUB-CONFIRM-PURCHASE-DESCRIPTION') ?></div>
		
		<fieldset>
			<legend><?= @text('COM-SUB-PACKAGE-INFORMATION') ?></legend>
					
				<div class="control-group">
					<label class="control-label" for="pkg-name"><?= @text('COM-SUB-PACKAGE-NAME') ?></label>
					<div class="controls">
						<input class="disabled xlarge" type="text" value="<?= @escape($order->itemName) ?>" disabled />
					</div>
				</div>
					
				<div class="control-group">
					<label class="control-label" for="pkg-price"><?=@text('COM-SUB-PACKAGE-PRICE')?></label>
					<div class="controls">
						<input class="disabled small" type="text" value="<?=  round($order->getItemAmount(), 2) ?>" disabled /> 
						<?= $order->currency ?> 
					</div>
				</div>
				
				<?php if( $order->getDiscountAmount() ): ?>	
				<div class="control-group">
					<label class="control-label" for="pkg-discount"><?=@text('COM-SUB-PACKAGE-DISCOUNT')?></label>
					<div class="controls">
						<input class="disabled small" type="text" value="<?= round($order->getDiscountAmount(), 2) ?>" disabled /> <?= $order->currency ?>
					</div>
				</div>
				<?php endif; ?>
				
				<div class="control-group">
					<label class="control-label" for="pkg-tax"><?=@text('COM-SUB-PACKAGE-TAX')?></label>
					<div class="controls">
						<input class="disabled small" type="text" value="<?= round($order->getTaxAmount(), 2) ?>" disabled /> <?= $order->currency ?>
					</div>
				</div>
				
				<div class="control-group info">
					<label class="control-label" for="pkg-total"><?=@text('COM-SUB-PACKAGE-TOTAL')?></label>
					<div class="controls">
						<input class="disabled small" type="text" value="<?= round($order->getTotalAmount(), 2) ?>" disabled /> <?= $order->currency ?>
						<span class="help-inline">
							<?= ($item->recurring) ? @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$item->billingPeriod) : @text('COM-SUB-BILLING-PERIOD-'.$item->billingPeriod) ?>
						</span>
					</div>
				</div>
		</fieldset>	 
		
		<?php if ( $order->getPaymentMethod() instanceof ComSubscriptionsDomainPaymentMethodCreditcard ) : ?>
		<fieldset>
			<legend><?= @text('COM-SUB-CREDITCARD-INFORMATION') ?></legend>		
				
				<div class="control-group">
					<label class="control-label" for="cc-name"><?= @text('COM-SUB-CREDITCARD-NAME') ?></label>
					<div class="controls">
						<input class="disabled xlarge" type="text" value="<?= $creditcard->first_name.' '.$creditcard->last_name ?>" disabled />
					</div>
				</div>
			
				<div class="control-group">
					<label class="control-label" for="cc-type"><?= @text('COM-SUB-CREDITCARD-TYPE') ?></label>
					<div class="controls">
						<input class="disabled small" type="text" value="<?= @text('COM-SUB-CREDITCARD-TYPE-'.strtoupper($creditcard->type)) ?>" disabled />
					</div>
				</div>
						
				<div class="control-group">
					<label class="control-label" for="cc-num"><?=@text('COM-SUB-CREDITCARD-NUM')?></label>
					<div class="controls">
						<input class="disabled xlarge" type="text" value="<?= $creditcard->number ?>" disabled />
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="cc-csv"><?= @text('COM-SUB-CREDITCARD-CSV') ?></label>
					<div class="controls">
						<input class="disabled small" type="text" value="<?= $creditcard->verification_value ?>" disabled />
					</div>
				</div>
			
				<div class="control-group">
					<label class="control-label" for="cc-exp"><?= @text('COM-SUB-CREDITCARD-EXP') ?></label>
					<div class="controls">
						<input class="disabled small" type="text" value="<?= $creditcard->month ?>" disabled /> 
						<input class="disabled small" type="text" value="<?=$creditcard->year ?>" disabled />
					</div>
				</div>

		</fieldset>
		
		<fieldset>
			<legend><?= @text('COM-SUB-CONTACT-INFORMATION') ?></legend>
			
			<div class="control-group">
				<label class="control-label" for="bill-address"><?= @text('COM-SUB-BILLING-ADDR') ?></label>
				<div class="controls">
					<input class="disabled xlarge" type="text" value="<?= $contact->address ?>" disabled />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="bill-city"><?= @text('COM-SUB-BILLING-CITY') ?></label>
				<div class="controls">
					<input class="disabled xlarge" type="text" value="<?=$contact->city?>" disabled />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="bill-state"><?= @text('COM-SUB-BILLING-STATE') ?></label>
				<div class="controls">
					<input class="disabled small" type="text" value="<?=$contact->state?>" disabled />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="bill-country"><?= @text('COM-SUB-BILLING-COUNTRY') ?></label>
				<div class="controls">
					<input class="disabled small" type="text" value="<?= @LibBaseTemplateHelperSelector::$COUNTRIES[$contact->country] ?>" disabled />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="zip"><?= @text('COM-SUB-BILLING-ZIP') ?></label>
				<div class="controls">
					<input class="disabled small" type="text" value="<?= strtoupper($contact->zip) ?>" disabled />
				</div>
			</div>
				
		</fieldset>
		<?php endif; ?>	
		
		<div class="form-actions">
			<button class="btn" type="submit" onclick="document.location='<?=@route(array('layout'=>'payment','id'=>$item->id))?>';return false;"><?=@text('COM-SUB-EDIT-INFORMATION')?></button>
			<button class="btn  btn-primary" type="submit"><?=@text('COM-SUB-PROCESS-PAYMENT')?></button>
		</div>	
	</form>	
</div>
