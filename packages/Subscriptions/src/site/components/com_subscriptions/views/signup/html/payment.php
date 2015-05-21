<?php defined('KOOWA') or die('Restricted access');?>

<?php if(defined('JDEBUG') && JDEBUG ) : ?>
<script src="com_subscriptions/js/coupon.js" />
<?php else: ?>
<script src="com_subscriptions/js/min/coupon.min.js" />
<?php endif; ?>

<?= @template('_steps', array('current_step'=>'payment')) ?>

<div class="row">
	<div class="span8">
	    
       <h1><?= @text('COM-SUBSCRIPTIONS-STEP-PAYMENT-METHOD') ?></h1>
            
    	<p class="lead">
    	    <?= $order->upgrade ? @text('COM-SUBSCRIPTIONS-YOU-ARE-UPGRADING-TO') : @text('COM-SUBSCRIPTIONS-YOU-ARE-SUBSCRIBING-TO') ?>
        </p>
    
        <div class="an-entity">
        	<h2 class="entity-title">
        		<?= @escape( $item->name ) ?>
        	</h2>
        	
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
            		<dd><?= round(AnHelperDate::secondsTo('day', $item->duration)) ?> <?= @text('COM-SUBSCRIPTIONS-PACKAGE-DAYS') ?></dd>
            		<?php endif; ?>
            	
            		<dt><?= @text('COM-SUBSCRIPTIONS-PACKAGE-PRICE') ?>: </dt>
            		<dd><?= $item->price.' '.get_config_value('subscriptions.currency','US') ?></dd>
            	</dl>
        	</div>
        </div>
        
        <form id="coupon-form" action="<?= @route('view=coupon', false) ?>" class="form-horizontal">	
        	<fieldset>
        		<legend>
        		    <?= @text('COM-SUBSCRIPTIONS-SIGNUP-PROMOTION') ?>
        		</legend>
        		
        		<p class="lead">
        		    <?= @text('COM-SUBSCRIPTIONS-ENTER-COUPON-INFORMATION') ?>
        		</p>
        		
        		<div class="control-group">
        			<label class="control-label" for="COM-SUBSCRIPTIONS-ENTER-COUPON-CODE">
        			    <?=@text('COM-SUBSCRIPTIONS-ENTER-COUPON-CODE')?>: 
        			</label>
        			
        			<div class="controls">
        			    <input name="coupon_code" value="<?= $coupon_code ?>" type="text" limit="100" />
        		    </div>
        		</div>	
        	</fieldset>
        </form>
        
        <hr/>
        
        <p class="lead">
            <?= @text('COM-SUBSCRIPTIONS-CHOOSE-PAYMENT-METHOD') ?>
        </p>
        
        <ul class="nav nav-pills">
        	<li class="active">
        		<a href="#creditcard" data-toggle="pill">
        		    <?= @text('AB-SUB-CHOOSE-PAYMENT-CREDITCARD') ?>
        		</a>
        	</li>
        	<li>
        		<a href="#paypal" data-toggle="pill">
        		    <?= @text('AB-SUB-CHOOSE-PAYMENT-PAYPAL') ?>
        		</a>
        	</li>
        </ul>
        
        <div class="tab-content">
            
        	<div id="creditcard" class="tab-pane fade in active">
        	    
        		<form id="cc-form" action="<?= @route(array('id'=>$item->id)) ?>" method="post" class="form-horizontal">
        			
        			<input type="hidden" name="action" value="confirm">
        			<input type="hidden" name="payment_method" value="direct" />
        			<input type="hidden" name="coupon_code">
        			
        			<fieldset>
        				<legend><?= @text('COM-SUBSCRIPTIONS-CREDITCARD-INFORMATION') ?></legend>
        			
        					<div class="icon-creditcards">
        						<span class="visa"></span>
        						<span class="mastercard"></span>
        						<span class="american-express"></span>
        						<span class="discover"></span>
        					</div>
        					
        					<p class="lead">
        					    <?= @text('COM-SUBSCRIPTIONS-ENTER-CREDITCARD-INFORMATION') ?>
        					</p>
        					
                			<?php if ( isset($flash['credit_card_error'])) : ?>
                			<?= @message($flash['credit_card_error'], array('type'=>'error'))?>
                			<?php endif;?>						
        								
        					<?php
        					$ccName = null;
        					
        					if ( $creditcard->first_name && $creditcard->last_name )
                            {
                                $ccName =  $creditcard->first_name.' '.$creditcard->last_name;
                            }
        					?>			
        								
        					<?= @helper('ui.form', array(
        						'COM-SUBSCRIPTIONS-CREDITCARD-TYPE' => @html('select', 'creditcard[type]', array(
        								'options' => array(
        									null => @text('LIB-AN-SELECTOR-SELECT-OPTION'),
        									'visa' 	=> @text('COM-SUBSCRIPTIONS-CREDITCARD-TYPE-VISA'),
        									'master' 	=> @text('COM-SUBSCRIPTIONS-CREDITCARD-TYPE-MASTER'),
        									'american_express'   => @text('COM-SUBSCRIPTIONS-CREDITCARD-TYPE-AMERICAN_EXPRESS'),
        									'discover'  => @text('COM-SUBSCRIPTIONS-CREDITCARD-TYPE-DISCOVER')
        								),
        								'selected' => $creditcard->type))->class('medium')->required(''),
        						'COM-SUBSCRIPTIONS-CREDITCARD-NAME' => @html('textfield', 'creditcard[name]',	$ccName )->required(''),
        						'COM-SUBSCRIPTIONS-CREDITCARD-NUM' => @html('textfield', 'creditcard[number]',	$creditcard->number)->required(''),
        						'COM-SUBSCRIPTIONS-CREDITCARD-CSV' => @html('textfield', 'creditcard[csv]', 	$creditcard->verification_value)->required('')->class('input-small'),
        						'COM-SUBSCRIPTIONS-CREDITCARD-EXP' => @helper('selector.month', array('name'=>'creditcard[month]', 'selected'=>$creditcard->month))->required('')->class('input-medium')
        						.' '.@helper('selector.year',  array('name'=>'creditcard[year]',  'selected'=>$creditcard->year))->required('')->class('input-medium')
        					)) ?>
        			</fieldset>
        			
        			<fieldset>
        				<legend>
        				    <?= @text('COM-SUBSCRIPTIONS-CONTACT-INFORMATION') ?>
        				</legend>
            			
            			<?php if ( isset($flash['address_error'])) : ?>
            			<?= @message($flash['address_error'], array('type'=>'error'))?>
            			<?php endif;?>				
        				
        				<?= @helper('ui.form', array(
        					
        					'COM-SUBSCRIPTIONS-BILLING-ADDR' => @html('textfield', 'contact[address]', $contact->address )->required(''),   
        					'COM-SUBSCRIPTIONS-BILLING-CITY' => @html('textfield', 'contact[city]', $contact->city )->required(''),
        					       
        					'COM-SUBSCRIPTIONS-BILLING-COUNTRY' => @helper('selector.country', 
        					   array(
        			             'name' => 'contact[country]', 
        					     'id' => 'country-selector', 
        					     'selected' => $contact->country ) )->required(''),
        					     
        					'COM-SUBSCRIPTIONS-BILLING-STATE' => @helper('selector.state', 
        					   array( 
        					       'name' => 'contact[state]', 
        					       'country_selector' => 'country-selector', 
        					       'selected' => $contact->state ) ),
        					       			
        					'COM-SUBSCRIPTIONS-BILLING-ZIP' => @html('textfield', 'contact[zip]', 
        					       $contact->zip)->required('')->class('small')
        				)) ?>
        				
        			</fieldset>	
        			
        			<div class="form-actions">
        				<?php if ( $viewer->guest() ) : ?>
        				<a href="<?=@route(array('layout'=>'login','id'=>$item->id)) ?>" class="btn">
        				    <?=@text('COM-SUBSCRIPTIONS-EDIT-USER-INFORMATION')?>
        				</a>
        				<?php endif; ?>
        				
        				<button class="btn btn-primary" type="submit">
        				    <?=@text('COM-SUBSCRIPTIONS-PREVIEW-ORDER') ?>
        				</button>
        			</div>	
        		</form>	
        	</div>
        	
        	<div id="paypal" class="tab-pane fade">
        	    
        		<form id="paypal-form" action="<?=@route(array('id'=>$item->id))?>" method="post">
        			
        			<input type="hidden" name="action" value="xpayment">
        			<input type="hidden" name="payment_method" value="express" />
        			<input type="hidden" name="coupon_code">
        			
        			<p class="lead">
        			    <span class="paypal-express"></span> 
        			    <?= @text('COM-SUBSCRIPTIONS-PAYPAL-INSTRUCTIONS') ?>
        		    </p>
        			
        			<div class="form-actions">
        				<?php if ( $viewer->guest() ) : ?>
        				<a class="btn" href="<?= @route( array( 'layout'=>'default', 'id'=>$item->id )) ?>">
        				    <?=@text('COM-SUBSCRIPTIONS-EDIT-USER-INFORMATION')?>
        				</a>
        				<?php endif; ?>		
        				<button class="btn btn-primary" type="submit">
        				    <?=@text('COM-SUBSCRIPTIONS-PAYPAL-LOGIN') ?>
        				</button>
        			</div>
        		</form>
        	</div>
        	
        </div>
        
	</div>
</div>