<?php defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step'=>'payment')) ?>

<div class="row">
	<div class="span8">
                
        	<p class="lead">
        	    <?= $order->upgrade ? @text('COM-SUB-YOU-ARE-UPGRADING-TO') : @text('COM-SUB-YOU-ARE-SUBSCRIBING-TO') ?>
            </p>
        
            <div class="an-entity">
            	<h2 class="entity-title">
            		<?= @escape( $item->name ) ?>
            	</h2>
            	
            	<div class="entity-description">
                	<dl>
                		<?php if($item->recurring): ?>
                		<dt><?= @text('COM-SUB-BILLING-PERIOD') ?>:</dt> 
                		<dd><?= @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$item->billingPeriod) ?></dd>
                		<?php else: ?>
                		<dt><?= @text('COM-SUB-PACKAGE-DURATION') ?>:</dt>
                		<dd><?= AnHelperDate::secondsTo('day', $item->duration)?> <?= @text('COM-SUB-PACKAGE-DAYS') ?></dd>
                		<?php endif; ?>
                	
                		<dt><?= @text('COM-SUB-PACKAGE-PRICE') ?>: </dt>
                		<dd><?= $item->price.' '.get_config_value('subscriptions.currency','US') ?></dd>
                	</dl>
            	</div>
            </div>
        
        <form action="<?=@route(array('id'=>$item->id))?>" class="form-horizontal">	
        	<fieldset>
        		<legend>
        		    <?= @text('COM-SUB-SIGNUP-PROMOTION') ?>
        		</legend>
        		
        		<p class="lead">
        		    <?= @text('COM-SUB-ENTER-COUPON-INFORMATION') ?>
        		</p>
        		
        		<div class="control-group">
        			<label class="control-label" for="COM-SUB-ENTER-COUPON-CODE">
        			    <?=@text('COM-SUB-ENTER-COUPON-CODE')?>: 
        			</label>
        			
        			<div class="controls">
        				<input id="coupon" data-validator-properties="{successMsg:'<?=@text('COM-SUB-VALID-COUPON')?>',url:'<?=@route('view=coupon')?>',key:'code'}" data-validators="validate-remote" type="text" value="<?=$coupon_code?>" name="coupon" />
        			</div>
        		</div>	
        	</fieldset>
        </form>
        
        <p class="lead">
            <?= @text('COM-SUB-CHOOSE-PAYMENT-METHOD') ?>
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
        	    
        		<form action="<?=@route(array('id'=>$item->id))?>" method="post" onsubmit="this.coupon_code.value=document.id('coupon').value" class="form-horizontal">
        			
        			<input type="hidden" name="payment_method" value="direct" />
        			<input type="hidden" name="coupon_code" value="">
        			
        			<fieldset>
        				<legend><?= @text('COM-SUB-CREDITCARD-INFORMATION') ?></legend>
        			
        					<input type="hidden" name="action" value="confirm">
        					
        					<div class="icon-creditcards">
        						<span class="visa"></span>
        						<span class="mastercard"></span>
        						<span class="american-express"></span>
        						<span class="discover"></span>
        					</div>
        					
        					<p class="lead">
        					    <?= @text('COM-SUB-ENTER-CREDITCARD-INFORMATION') ?>
        					</p>
        					
                			<?php if ( isset($flash['credit_card_error'])) : ?>
                			<?= @message($flash['credit_card_error'], array('type'=>'error'))?>
                			<?php endif;?>						
        								
        					<?= @helper('ui.form', array(
        						'COM-SUB-CREDITCARD-TYPE' => @html('select', 'creditcard[type]', array(
        								'options' => array(
        									'visa' 	=> @text('COM-SUB-CREDITCARD-TYPE-VISA'),
        									'master' 	=> @text('COM-SUB-CREDITCARD-TYPE-MASTER'),
        									'american_express'   => @text('COM-SUB-CREDITCARD-TYPE-AMERICAN_EXPRESS'),
        									'discover'  => @text('COM-SUB-CREDITCARD-TYPE-DISCOVER')
        								),
        								'selected' => $creditcard->type))->class('medium'),
        						'COM-SUB-CREDITCARD-NAME' => @html('textfield', 'creditcard[name]',	$creditcard->first_name.' '.$creditcard->last_name)->dataValidators('required'),
        						'COM-SUB-CREDITCARD-NUM' => @html('textfield', 'creditcard[number]',	$creditcard->number)->dataValidators('required validate-cc-num'),
        						'COM-SUB-CREDITCARD-CSV' => @html('textfield', 'creditcard[csv]', 	$creditcard->verification_value)->dataValidators('required')->class('input-small'),
        						'COM-SUB-CREDITCARD-EXP' => @helper('selector.month', array('name'=>'creditcard[month]', 'selected'=>$creditcard->month))->dataValidators('required')->class('input-medium')
        						.' '.@helper('selector.year',  array('name'=>'creditcard[year]',  'selected'=>$creditcard->year))->dataValidators('required')->class('input-medium')
        					)) ?>
        			</fieldset>
        			
        			<fieldset>
        				<legend>
        				    <?= @text('COM-SUB-CONTACT-INFORMATION') ?>
        				</legend>
            			
            			<?php if ( isset($flash['address_error'])) : ?>
            			<?= @message($flash['address_error'], array('type'=>'error'))?>
            			<?php endif;?>				
        				
        				<?= @helper('ui.form', array(
        					
        					'COM-SUB-BILLING-ADDR' => @html('textfield', 'contact[address]', 
        					       $contact->address )->dataValidators('required'),
        					       
        					'COM-SUB-BILLING-CITY' => @html('textfield', 'contact[city]', 
        					       $contact->city)->dataValidators('required'),
        					       
        					'COM-SUB-BILLING-COUNTRY' => @helper('selector.country', 
        					   array(
        			             'name' => 'contact[country]', 
        					     'id' => 'country-selector', 
        					     'selected' => $contact->country ) ),
        					     
        					'COM-SUB-BILLING-STATE' => @helper('selector.state', 
        					   array( 
        					       'name' => 'contact[state]', 
        					       'country_selector' => 'country-selector', 
        					       'selected' => $contact->state ) ),
        					       			
        					'COM-SUB-BILLING-ZIP' => @html('textfield', 'contact[zip]', 
        					       $contact->zip)->dataValidators('required')->class('small')
        				)) ?>
        				
        			</fieldset>	
        			
        			<div class="form-actions">
        				<?php if ( $viewer->guest() ) :?>
        				<button class="btn" type="submit" onclick="document.location='<?=@route(array('layout'=>'login','id'=>$item->id))?>';return false;">
        				    <?=@text('COM-SUB-EDIT-USER-INFORMATION')?>
        				</button>
        				<?php endif; ?>
        				
        				<button class="btn btn-primary" type="submit">
        				    <?=@text('COM-SUB-PREVIEW-ORDER') ?>
        				</button>
        			</div>	
        		</form>	
        	</div>
        	
        	<div id="paypal" class="tab-pane fade">
        	    
        		<form action="<?=@route(array('id'=>$item->id))?>" method="post" onsubmit="this.coupon_code.value=document.id('coupon').value">
        			
        			<input type="hidden" name="coupon_code" value="">			
        			<input type="hidden" name="action" value="xpayment">	
        			<input type="hidden" name="payment_method" value="express" />
        			
        			<p class="lead">
        			    <span class="paypal-express"></span> 
        			    <?= @text('COM-SUB-PAYPAL-INSTRUCTIONS') ?>
        		    </p>
        			
        			<div class="form-actions">
        				<?php if ( $viewer->guest() ) :?>
        				<button class="btn" type="submit" onclick="document.location='<?=@route(array('layout'=>'default','id'=>$item->id))?>';return false;">
        				    <?=@text('COM-SUB-EDIT-USER-INFORMATION')?>
        				</button> 
        				<?php endif; ?>		
        				<button class="btn btn-primary" type="submit">
        				    <?=@text('COM-SUB-PAYPAL-LOGIN') ?>
        				</button>
        			</div>
        		</form>
        	</div>
        	
        </div>
        
	</div>
</div>