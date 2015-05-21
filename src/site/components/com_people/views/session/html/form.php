<?php defined('KOOWA') or die('Restricted access') ?>

<?php $return = empty($return) ? null : $return; ?> 
<?php $connect = empty($connect) ? false : true; ?>

<form action="<?= @route() ?>" name="person-form" id="person-form" method="post" class="well">
    <?php if( $return ) : ?>
    <input type="hidden" name="return" value="<?= $return; ?>" />
    <?php endif; ?>

    <?= @flash_message ?>

    <fieldset>
        <legend>
            <?= @text('COM-PEOPLE-SESSION-TITLE') ?>
            
            <?php if(@service('com://site/people.controller.person')->permission->canRegister()): ?>
            <small>
                <a class="pull-right" href="<?= @route('option=com_people&view=person&layout=add'.( ( $return ) ? "&return=$return" : '')) ?>">
                    <?= @text('COM-PEOPLE-ACTION-CREATE-AN-ACCOUNT')?>
                </a>
            </small>
            <?php endif;?>
        </legend>    
        
        <?php if( $connect && KService::get('koowa:loader')->loadIdentifier('com://site/connect.template.helper.service')): ?>
        <p class="lead">
            <?= @text('COM-PEOPLE-SOCIALMEDIA-LOGIN') ?>
        </p>
        <p>
            <?= $this->renderHelper('com://site/connect.template.helper.service.renderLogins') ?>
        </p>
        
        <hr/>
        <?php endif ?>        
 
        <div class="control-group">         
            <div class="controls">
                <input class="input-block-level" name="username" placeholder="<?= @text('COM-PEOPLE-SESSION-PLACEHOLDER-USERNAME-EMAIL')?>" id="person-username" type="text" size="25" />
            </div>
        </div>
        
        <div class="control-group">             
            <div class="controls">
                <?= @helper('password.input', array('required'=>true)) ?>
                <a href="<?= @route('view=token') ?>">
                    <?= @text('COM-PEOPLE-SESSION-FORGOT-PASSWORD'); ?>
                </a> 
            </div>
        </div>
        
        <div class="control-group">
            <label class="checkbox">
                <input type="checkbox" name="remember" value="true" alt="<?= @text('COM-PEOPLE-SESSION-REMEMBER-ME'); ?>" />
                <?= @text('COM-PEOPLE-SESSION-REMEMBER-ME'); ?>
            </label>
        </div>
    </fieldset>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-large pull-right" data-loading-text="<?= @text('LIB-AN-ACTION-PLEASE-WAIT') ?>">
            <?= @text('COM-PEOPLE-ACTION-LOGIN') ?>
        </button>
    </div>
</form>