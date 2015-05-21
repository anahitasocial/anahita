<?php defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step'=>'confirm')) ?>

<div class="row">
	<div class="span8">
	    
	    <h1><?= @text('COM-SUBSCRIPTIONS-STEP-PAYMENT-CONFIRM'); ?></h1>

		<p class="lead">
		    <?= @text('COM-SUBSCRIPTIONS-CONFIRM-PURCHASE-DESCRIPTION'); ?>
		</p>
		
		<h3><?= @text('COM-SUBSCRIPTIONS-PACKAGE-INFORMATION'); ?></h3>
			
		<div class="an-entity">
            <h3 class="entity-title">
                <?= @escape($order->itemName); ?>
            </h3>
            
            <div class="entity-description">
                <?= $item->description ?>
            </div>
            
            <div class="entity-description">
                
                <dl>
                    <?php if($item->recurring): ?>
                    <dt><?= @text('COM-SUBSCRIPTIONS-BILLING-PERIOD') ?>:</dt> 
                    <dd><?= @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-RECURRING-'.$item->billingPeriod) ?></dd>
                    <?php else: ?>
                    <dt><?= @text('COM-SUBSCRIPTIONS-PACKAGE-DURATION') ?>:</dt>
                    <dd>
                        <?= round(AnHelperDate::secondsTo('day', $item->duration)) ?> 
                        <?= @text('COM-SUBSCRIPTIONS-PACKAGE-DAYS') ?>
                    </dd>
                    <?php endif; ?>
                
                    <dt><?= @text('COM-SUBSCRIPTIONS-PACKAGE-PRICE') ?>: </dt>
                    <dd>
                        <?= $item->price ?> 
                        <?= get_config_value('subscriptions.currency','US') ?>
                    </dd>
                    
                    <?php if( $order->getDiscountAmount() ): ?>
                    <dt><?=@text('COM-SUBSCRIPTIONS-PACKAGE-DISCOUNT')?></dt>
                    <dd>
                        <?= round($order->getDiscountAmount(), 2) ?> 
                        <?= get_config_value('subscriptions.currency','US') ?>
                    </dd>
                    <?php endif; ?>
                    
                    <dt><?=@text('COM-SUBSCRIPTIONS-PACKAGE-TAX')?></dt>
                    <dd><?= round($order->getTaxAmount(), 2) ?></dd>
                    
                    <dt><?=@text('COM-SUBSCRIPTIONS-PACKAGE-TOTAL')?></dt>
                    <dd>
                        <?= round($order->getTotalAmount(), 2) ?> <?= $order->currency ?>,  
                        <?= ( $item->recurring ) ? @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-RECURRING-'.$item->billingPeriod) : @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-'.$item->billingPeriod) ?>
                    </dd>
                </dl>
                
            </div>    
        </div>
            
        

    		
		<?php if ( $order->getPaymentMethod() instanceof ComSubscriptionsDomainPaymentMethodCreditcard ) : ?>
		<h3><?= @text('COM-SUBSCRIPTIONS-CREDITCARD-INFORMATION') ?></h3>
   
			<dl class="dl-horizontal">
		    <dt><?= @text('COM-SUBSCRIPTIONS-CREDITCARD-NAME') ?></dt>
		    <dd><?= $creditcard->first_name.' '.$creditcard->last_name ?></dd>
		    
		    <dt><?= @text('COM-SUBSCRIPTIONS-CREDITCARD-TYPE') ?></dt>
		    <dd><?= @text('COM-SUBSCRIPTIONS-CREDITCARD-TYPE-'.strtoupper($creditcard->type)) ?></dd>
		    
		    <dt><?=@text('COM-SUBSCRIPTIONS-CREDITCARD-NUM')?></dt>
		    <dd><?= $creditcard->number ?></dd>
		    
		    <dt><?= @text('COM-SUBSCRIPTIONS-CREDITCARD-CSV') ?></dt>
		    <dd><?= $creditcard->verification_value ?></dd>
		    
		    <dt><?= @text('COM-SUBSCRIPTIONS-CREDITCARD-EXP') ?></dt>
		    <dd><?= $creditcard->month ?> / <?=$creditcard->year ?></dd>
		</dl>
			
		<h3><?= @text('COM-SUBSCRIPTIONS-CONTACT-INFORMATION') ?></h3>
  
			<dl class="dl-horizontal">
		    <dt><?= @text('COM-SUBSCRIPTIONS-BILLING-ADDR') ?></dt>
		    <dd><?= $contact->address ?></dd>
		    
		    <dt><?= @text('COM-SUBSCRIPTIONS-BILLING-CITY') ?></dt>
		    <dd><?= $contact->city ?></dd>
		    
		    <dt><?= @text('COM-SUBSCRIPTIONS-BILLING-STATE') ?></dt>
            <dd><?= $contact->state ?></dd>
            
            <dt><?= @text('COM-SUBSCRIPTIONS-BILLING-COUNTRY') ?></dt>
            <dd><?= @LibBaseTemplateHelperSelector::$COUNTRIES[$contact->country] ?></dd>
            
            <dt><?= @text('COM-SUBSCRIPTIONS-BILLING-ZIP') ?></dt>
            <dd><?= strtoupper($contact->zip) ?></dd>
		</dl>
		<?php endif; ?>	
    		
    	<form action="<?= @route( array( 'id'=>$item->id ) ); ?>" method="post">
    		<input type="hidden" name="action" value="process">
    		
    		<div class="form-actions">
    			<a class="btn" href="<?= @route(array('layout'=>'payment','id'=>$item->id)) ?>">
                    <?= @text('COM-SUBSCRIPTIONS-EDIT-INFORMATION') ?>      
    			</a>
    			
    			<button class="btn btn-primary" type="submit">
    			    <?= @text('COM-SUBSCRIPTIONS-PROCESS-PAYMENT') ?>
    			</button>
    		</div>	
    	</form>
    	
	</div>
</div>
