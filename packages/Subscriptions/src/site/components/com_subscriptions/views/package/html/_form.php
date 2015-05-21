<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $package = empty( $package ) ? @service('repos:packages.package')->getEntity()->reset() : $package; ?>

<form method="post" action="<?= @route() ?>">
    <fieldset>
        <legend><?= ( $package->persisted() ) ? @text('LIB-AN-ACTION-EDIT') : @text('LIB-AN-ACTION-ADD') ?></legend>
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
        
        <div class="form-actions">
            <?php $cancelURL = ($package->persisted()) ? $package->getURL() : 'view=packages' ?>
            <a class="btn" href="<?= @route( $cancelURL ) ?>">
                <?= @text('LIB-AN-ACTION-CANCEL') ?>
            </a>  
            
            <?php $action = ($package->persisted()) ? 'LIB-AN-ACTION-UPDATE' : 'LIB-AN-ACTION-EDIT' ?>
            <?php $actionLoading = ($package->persisted()) ? 'LIB-AN-MEDIUM-UPDATING' : 'LIB-AN-MEDIUM-POSTING' ?>
            <button class="btn btn-primary" data-loading-text="<?= @text($actionLoading) ?>">
                <?= @text($action) ?>
            </button>
        </div>
</form>