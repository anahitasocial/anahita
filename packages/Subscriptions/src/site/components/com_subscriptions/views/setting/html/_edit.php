<?php defined('KOOWA') or die('Restricted access'); ?>

<?php if(defined('JDEBUG') && JDEBUG ) : ?>
<script src="com_subscriptions/js/setting.js" />
<?php else: ?>
<script src="com_subscriptions/js/min/setting.min.js" />
<?php endif; ?>

<?php $action = ( $selectedPackageId ) ? 'editsubscription' : 'addsubscriber'; ?>

<form action="<?= @route('view=package') ?>" method="post" />
    <input type="hidden" name="action" value="<?= $action ?>" />
    <input type="hidden" name="actor_id" value="<?= $actor->id ?>" />
    
    <div class="control-group">
        <label for="package">
            <?= @text('COM-SUBSCRIPTIONS-PACAKGE') ?>
        </label>        
         
        <div class="controls">
        <?php foreach( $packages as $package ): ?>
            <label class="radio">
                <?php $checked = ( $package->id == $selectedPackageId ) ? 'checked' : ''; ?>
                <input required type="radio" name="package_id" value="<?= $package->id ?>" <?= $checked ?> /> 
                <?= @escape( $package->title ) ?>
            </label>
        <?php endforeach; ?>    
        </div>
    </div>

    <?php if( count($packages) ): ?>
    <div class="control-group">
        <label class="class-label">
            <?= @text('COM-SUBSCRIPTIONS-SUBSCRIPTION-EXPIRY-DATE') ?>
        </label>
         
        <div class="controls">
            <?= @helper('selector.day', array( 'name'=>'day', 'required'=>'', 'selected'=> $endDate->day, 'class'=>'span2')) ?>
            <?= @helper('selector.month', array( 'name'=>'month', 'required'=>'', 'selected'=> $endDate->month, 'class'=>'span2')) ?>
            <?= @helper('selector.year', array( 'name'=>'year', 'required'=>'', 'selected'=> $endDate->year, 'class'=>'span2')) ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="form-actions">   
        <?php if ( $subscription ) : ?> 
        <a data-trigger="DeleteSubscriber" class="btn btn-danger">
            <?= @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-UNSUBSCRIBE') ?>
        </a>
        <?php endif; ?>
            
        <button type="submit" class="btn btn-primary">
            <?php if ( $subscription ) : ?>
            <?= @text('LIB-AN-ACTION-UPDATE') ?>
            <?php else : ?>
            <?= @text('COM-SUBSCRIPTIONS-PACKAGE-ACTION-SUBSCRIBE') ?>  
            <?php endif; ?>    
        </button>
    </div>
</form>
