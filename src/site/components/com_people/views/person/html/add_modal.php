<?php defined('KOOWA') or die; ?>

<?php $return = empty($return) ? null : $return; ?>

<div class="modal-header">
    <h3><?= @text('COM-PEOPLE-ACTION-CREATE-AN-ACCOUNT') ?></h3>
</div>

<div class="modal-body">
    <form action="<?= @route('view=person') ?>" method="post" name="person-form" id="person-form" autocomplete="off">
        <?php if ( $return ) : ?>
        <input type="hidden" name="return" value="<?= $return; ?>" />
        <?php endif; ?>
    
        <div class="control-group">
            <label class="control-label"  for="person-name">
                <?= @text( 'COM-PEOPLE-ADD-NAME' ); ?>
            </label>
            <div class="controls">
                <input class="input-block-level" type="text" id="person-name" name="name" maxlength="100" minlength="6" required />
            </div>
        </div>
        
        <div class="control-group">
            <label class="control-label"  for="person-username">
                <?= @text('COM-PEOPLE-ADD-USERNAME'); ?>
            </label>
            <div class="controls">
                <? $usernamePattern = "^[A-Za-z][A-Za-z0-9_-]*$"; ?>
                <input data-validate="username" data-url="<?= @route('view=person', false ) ?>" type="text" id="person-username" class="input-block-level" name="username" pattern="<?= $usernamePattern ?>" maxlength="100" minlength="6" required />
            </div>
        </div>
                
        <div class="control-group">
            <label class="control-label"  for="person-email">
                <?= @text('COM-PEOPLE-ADD-EMAIL'); ?>
            </label>
            <div class="controls">
               <?php $emailPattern = "^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" ?>
               <input data-validate="email" data-url="<?= @route('view=person', false) ?>" type="email" name="email" pattern="<?= $emailPattern ?>" id="person-email" class="input-block-level" maxlength="100" minlength="10" required  />
            </div>
        </div>
                
        <div class="control-group">
            <label class="control-label"  for="password">
                <?= @text('COM-PEOPLE-ADD-PASSWORD'); ?>
            </label>
            <div class="controls">
                <?= @helper('password.input', array('required'=>true)) ?>               
            </div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-ACTION-PLEASE-WAIT') ?>">
        <?= @text('COM-PEOPLE-ACTION-REGISTER') ?>
    </button> 
</div>
