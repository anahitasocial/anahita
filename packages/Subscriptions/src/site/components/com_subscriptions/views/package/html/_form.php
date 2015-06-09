<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $package = empty($package) ?@controller($this->getView()->getName())->getRepository()->getEntity()->reset() : $package; ?>

<form method="post" action="<?= @route() ?>" class="an-entity">
    <fieldset>
        <legend><?= ( $package->persisted() ) ? @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-EDIT') : @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-ADD') ?></legend>
        <div class="control-group">
            <label class="control-label" for="package-title">
                <?= @text('LIB-AN-ENTITY-TITLE') ?>
            </label>
            <div class="controls">
                <input required class="input-block-level" id="package-title" name="title" value="<?= @escape( $package->title ) ?>" size="50" maxlength="255" type="text" />
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label" for="package-body">
                <?= @text('LIB-AN-ENTITY-DESCRIPTION') ?>
            </label>
            <div class="controls">
                <?= @editor(array(
                    'name'=>'body',
                    'content'=> @escape( $package->body ), 
                    'html' => array(    
                        'maxlength'=>'20000', 
                        'cols'=>'10',
                        'rows'=>'5', 
                        'class'=>'input-block-level', 
                        'id'=>'package-body' 
                        )
                )); ?>
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label" for="package-price">
                <?= @text('COM-SUBSCRIPTIONS-PACKAGE-PRICE') ?>
            </label> 
            <div class="controls">
                <div class="input-append">
                    <input class="span2" id="package-price" required type="text" placeholder="00.00" value="<?= $package->price ?>" size="10" maxlength="10" name="price" /> 
                    <span class="add-on">
                        <?= get_config_value('subscriptions.currency','US') ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label" for="package-duration">
                <?= @text('COM-SUBSCRIPTIONS-PACKAGE-DURATION') ?>
            </label> 
            <div class="controls">
                <?php 
                $period_options = array(
                    array( ComSubscriptionsDomainEntityPackage::BILLING_PERIOD_YEAR, @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-YEAR')),
                    array( ComSubscriptionsDomainEntityPackage::BILLING_PERIOD_MONTH, @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-MONTH')),
                    array( ComSubscriptionsDomainEntityPackage::BILLING_PERIOD_WEEK, @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-WEEK')),
                    array( ComSubscriptionsDomainEntityPackage::BILLING_PERIOD_DAY, @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-DAY'))
                );
                ?>
                <select required name="billingPeriod" id="package-duration">
                <?= @helper('html.options', $period_options, $package->billing_period ) ?>
                </select>
            </div>
        </div>
        
       <div class="control-group">
           <label class="control-label" for="package-recurring">
                <?= @text('COM-SUBSCRIPTIONS-BILLING-PERIOD-RECURRING') ?>
           </label>
           <div class="controls"> 
                <select class="span1" name="recurring" id="package-recurring">
                 <?php
                 $recurring_options = array(
                    array( 0, @text('LIB-AN-NO') ),
                    array( 1, @text('LIB-AN-YES') )
                 );
                 ?>   
                 <?= @helper('html.options', $recurring_options, $package->recurring ) ?>   
                </select>
           </div>
       </div>
       
       <?php if ( $package->ordering > 1 ) : ?>
       <div class="control-group">
            <label class="control-label" for="package-upgrade-discount">
                <?= @text('COM-SUBSCRIPTIONS-PACKAGE-UPGRADE-DISCOUNT') ?>
            </label> 
            <div class="controls">
                <div class="input-append">
                    <input class="span1" id="package-upgrade-discount" required type="text" placeholder="00.00" value="<?= $package->getUpgradeDiscount() * 100 ?>" size="2" maxlength="2" name="upgrade_discount" /> 
                    <span class="add-on">%</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="form-actions">
            <?php $cancelURL = ($package->persisted()) ? $package->getURL() : 'view=packages' ?>
            <a class="btn" href="<?= @route( $cancelURL ) ?>">
                <?= @text('LIB-AN-ACTION-CANCEL') ?>
            </a>  
            
            <?php $action = ($package->persisted()) ? 'LIB-AN-ACTION-UPDATE' : 'LIB-AN-ACTION-ADD' ?>
            <?php $actionLoading = ($package->persisted()) ? 'LIB-AN-MEDIUM-UPDATING' : 'LIB-AN-MEDIUM-POSTING' ?>
            <button type="submit" class="btn btn-primary" data-loading-text="<?= @text($actionLoading) ?>">
                <?= @text($action) ?>
            </button>
        </div>
</form>