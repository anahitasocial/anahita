<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $coupon = empty( $coupon ) ? @service('repos:subscriptions.coupon')->getEntity()->reset() : $coupon; ?>

<form method="post" action="<?= @route( $coupon->getURL() ) ?>">
    
    <fieldset>
        <legend><?= ( $coupon->persisted() ) ? @text('COM-SUBSCRIPTIONS-COUPON-EDIT') : @text('COM-SUBSCRIPTIONS-COUPON-ADD') ?></legend>
    
        <div class="control-group">
            <label class="control-label" for="coupon-code">
                <?= @text('COM-SUBSCRIPTIONS-COUPONE-CODE') ?>
            </label>
            <div class="controls">
                <input required name="code" class="input-block-level" value="<?= ($coupon->persisted()) ? $coupon->code : ''; ?>" size="50" maxlength="255" type="text">
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
            
            <button type="submit" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-MEDIUM-UPDATING') ?>">
                <?= @text('LIB-AN-ACTION-UPDATE') ?>
            </button>
        <?php else : ?>
        <a data-trigger="CancelAdd" class="btn" href="<?= @route($coupon->getURL()) ?>">
            <?= @text('LIB-AN-ACTION-CANCEL') ?>
        </a>  
        
        <button data-trigger="Add" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-MEDIUM-POSTING') ?>">
            <?= @text('LIB-AN-ACTION-ADD') ?>
        </button>
        <?php endif;?>
    </div> 
</form>

