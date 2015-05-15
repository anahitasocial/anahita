<?php defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step'=>'confirm')) ?>

<div class="row">
	<div id="sub-signup-confirm" class="span8">
    	<form class="form-horizontal" action="<?= @route(array('id'=>$item->id)); ?>" method="post" name="sub-login" id="login">
    		<input type="hidden" name="action" value="process">
    		
    		<p class="lead">
    		    <?= @text('COM-SUB-CONFIRM-PURCHASE-DESCRIPTION') ?>
    		</p>
    		
    		<fieldset>
    			<legend><?= @text('COM-SUB-PACKAGE-INFORMATION') ?></legend>
   
    				
    				<div class="an-entity">
                        <h3 class="entity-title">
                            <?= @escape($order->itemName) ?>
                        </h3>
                        
                        <div class="entity-description">
                            <?= nl2br($item->description) ?>
                        </div>
                    </div>
                    
                    <dl class="dl-horizontal">
                        
                        <?php if($item->recurring): ?>
                        <dt><?= @text('COM-SUB-BILLING-PERIOD') ?>:</dt> 
                        <dd><?= @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$item->billingPeriod) ?></dd>
                        <?php else: ?>
                        <dt><?= @text('COM-SUB-PACKAGE-DURATION') ?>:</dt>
                        <dd><?= AnHelperDate::secondsTo('day', $item->duration)?> <?= @text('COM-SUB-PACKAGE-DAYS') ?></dd>
                        <?php endif; ?>
                    
                        <dt><?= @text('COM-SUB-PACKAGE-PRICE') ?>: </dt>
                        <dd><?= $item->price.' '.get_config_value('subscriptions.currency','US') ?></dd>
                        
                        <?php if( $order->getDiscountAmount() ): ?>
                        <dt><?=@text('COM-SUB-PACKAGE-DISCOUNT')?></dt>
                        <dd><?= round($order->getDiscountAmount(), 2) ?></dd>
                        <?php endif; ?>
                        
                        <dt><?=@text('COM-SUB-PACKAGE-TAX')?></dt>
                        <dd><?= round($order->getTaxAmount(), 2) ?></dd>
                        
                        <dt><?=@text('COM-SUB-PACKAGE-TOTAL')?></dt>
                        <dd>
                            <?= round($order->getTotalAmount(), 2) ?> <?= $order->currency ?> 
                            <?= ($item->recurring) ? @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$item->billingPeriod) : @text('COM-SUB-BILLING-PERIOD-'.$item->billingPeriod) ?>
                        </dd>
                    </dl>
    				
    		</fieldset>	 
    		
    		<?php if ( $order->getPaymentMethod() instanceof ComSubscriptionsDomainPaymentMethodCreditcard ) : ?>
    		<fieldset>
    			<legend><?= @text('COM-SUB-CREDITCARD-INFORMATION') ?></legend>		
    				
    				<dl class="dl-horizontal">
    				    <dt><?= @text('COM-SUB-CREDITCARD-NAME') ?></dt>
    				    <dd><?= $creditcard->first_name.' '.$creditcard->last_name ?></dd>
    				    
    				    <dt><?= @text('COM-SUB-CREDITCARD-TYPE') ?></dt>
    				    <dd><?= @text('COM-SUB-CREDITCARD-TYPE-'.strtoupper($creditcard->type)) ?></dd>
    				    
    				    <dt><?=@text('COM-SUB-CREDITCARD-NUM')?></dt>
    				    <dd><?= $creditcard->number ?></dd>
    				    
    				    <dt><?= @text('COM-SUB-CREDITCARD-CSV') ?></dt>
    				    <dd><?= $creditcard->verification_value ?></dd>
    				    
    				    <dt><?= @text('COM-SUB-CREDITCARD-EXP') ?></dt>
    				    <dd><?= $creditcard->month ?> / <?=$creditcard->year ?></dd>
    				</dl>
    				
    		</fieldset>
    		
    		<fieldset>
    			<legend><?= @text('COM-SUB-CONTACT-INFORMATION') ?></legend>
    			
    			<dl class="dl-horizontal">
    			    <dt><?= @text('COM-SUB-BILLING-ADDR') ?></dt>
    			    <dd><?= $contact->address ?></dd>
    			    
    			    <dt><?= @text('COM-SUB-BILLING-CITY') ?></dt>
    			    <dd><?= $contact->city ?></dd>
    			    
    			    <dt><?= @text('COM-SUB-BILLING-STATE') ?></dt>
                    <dd><?= $contact->state ?></dd>
                    
                    <dt><?= @text('COM-SUB-BILLING-COUNTRY') ?></dt>
                    <dd><?= @LibBaseTemplateHelperSelector::$COUNTRIES[$contact->country] ?></dd>
                    
                    <dt><?= @text('COM-SUB-BILLING-ZIP') ?></dt>
                    <dd><?= strtoupper($contact->zip) ?></dd>
    			</dl>
    				
    		</fieldset>
    		<?php endif; ?>	
    		
    		<div class="form-actions">
    			<button class="btn" type="submit" onclick="document.location='<?=@route(array('layout'=>'payment','id'=>$item->id))?>';return false;"><?=@text('COM-SUB-EDIT-INFORMATION')?></button>
    			<button class="btn  btn-primary" type="submit"><?=@text('COM-SUB-PROCESS-PAYMENT')?></button>
    		</div>	
    	</form>
	</div>
</div>
