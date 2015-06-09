<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $coupon = empty( $coupon ) ? @service('repos:subscriptions.coupon')->getEntity()->reset() : $coupon; ?>

<form method="post" action="<?= @route( $coupon->getURL() ) ?>" class="an-entity">
    
    <fieldset>
        <legend><?= ( $coupon->persisted() ) ? @text('COM-SUBSCRIPTIONS-COUPON-EDIT') : @text('COM-SUBSCRIPTIONS-COUPON-ADD') ?></legend>
    
        <div class="control-group">
            <label class="control-label" for="coupon-code">
                <?= @text('COM-SUBSCRIPTIONS-COUPONE-CODE') ?>
            </label>
            <div class="controls">
                <input id="coupon-code" required name="code" class="input-block-level" value="<?= ($coupon->persisted()) ? $coupon->code : ''; ?>" maxlength="255" type="text">
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label" for="coupon-discount">
                <?= @text('COM-SUBSCRIPTIONS-COUPONE-DISCOUNT') ?>
            </label>
            <div class="controls input-append">
                <input required name="discount" class="span1" value="<?= $coupon->discount * 100 ?>" maxlength="2" type="number">
                <span class="add-on">&#37;</span>
            </div>
        </div>
        
        <div class="control-group">
            <label class="class-label">
                <?= @text('COM-SUBSCRIPTIONS-COUPONE-EXPIRY-DATE') ?>
            </label>
            
            <div class="controls">
                <?= @helper('selector.day', array( 'name'=>'expiresOnDay', 'required'=>'', 'selected'=> $expiresOn->day, 'class'=>'span2')) ?>
                <?= @helper('selector.month', array( 'name'=>'expiresOnMonth', 'required'=>'', 'selected'=> $expiresOn->month, 'class'=>'span2')) ?>
                <?= @helper('selector.year', array( 'name'=>'expiresOnYear', 'required'=>'', 'selected'=> $expiresOn->year, 'class'=>'span2')) ?>
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label" for="coupon-limit">
                <?= @text('COM-SUBSCRIPTIONS-COUPONE-LIMIT') ?>
            </label>
            <div class="controls">
                <input required name="limit" class="span1" value="<?= $coupon->limit ?>" maxlength="5" type="number">
            </div>
        </div>
    
    </fieldset>
    
    <div class="form-actions">
        <?php if($coupon->persisted()): ?>
            <?php if(KRequest::type() == 'AJAX'): ?>
            <a data-action="cancel" class="btn" href="<?= @route( $coupon->getURL().'&layout=list' ) ?>">
                <?= @text('LIB-AN-ACTION-CANCEL') ?>
            </a> 
            <?php else : ?>
            <a class="btn" href="<?= @route($url) ?>">
                <?= @text('LIB-AN-ACTION-CANCEL') ?>
            </a> 
            <?php endif;?> 
            
            <button type="submit" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-ACTION-SAVING') ?>">
                <?= @text('LIB-AN-ACTION-UPDATE') ?>
            </button>
        <?php else : ?>
        <a data-trigger="CancelAdd" class="btn" href="<?= @route($coupon->getURL()) ?>">
            <?= @text('LIB-AN-ACTION-CANCEL') ?>
        </a>  
        
        <button data-trigger="Add" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-ACTION-SAVING') ?>">
            <?= @text('LIB-AN-ACTION-ADD') ?>
        </button>
        <?php endif;?>
    </div> 
</form>

