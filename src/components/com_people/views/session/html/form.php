<? defined('ANAHITA') or die('Restricted access') ?>

<? $return = empty($return) ? null : $return; ?>
<? $connect = empty($connect) ? false : true; ?>

<form
    action="<?= @route() ?>"
    name="person-form"
    id="person-form"
    method="post"
    class="well recaptcha">

    <? if ($return) : ?>
    <input type="hidden" name="return" value="<?= $return; ?>" />
    <? endif; ?>

    <?= @flash_message ?>

    <fieldset>
        <legend>
            <?= @text('COM-PEOPLE-SESSION-TITLE') ?>
            <? if (@service('com:people.controller.signup')->permission->isRegistrationOpen()): ?>
            <small>
                <? $signup_link = 'option=com_people&view=signup' ?>
                <? $signup_link .= $return ? '&return='.$return : '' ?>
                <a class="pull-right" href="<?= @route($signup_link) ?>">
                    <?= @text('COM-PEOPLE-ACTION-CREATE-AN-ACCOUNT')?>
                </a>
            </small>
            <? endif;?>
        </legend>

        <? if ($connect && AnService::get('anahita:loader')->loadIdentifier('com://site/connect.template.helper.service')): ?>
        <p class="lead"><?= @text('COM-PEOPLE-SOCIALMEDIA-LOGIN') ?></p>
        <p><?= $this->renderHelper('com:connect.template.helper.service.renderLogins') ?></p>
        <hr/>
        <? endif ?>

        <div class="control-group">
            <div class="controls">
                <input
                    required
                    class="input-block-level"
                    name="username"
                    placeholder="<?= @text('COM-PEOPLE-SESSION-PLACEHOLDER-USERNAME-EMAIL')?>"
                    id="person-username"
                    type="text"
                    maxlength="30"
                    minlength="3"
                    autocomplete
                />
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <?= @helper('password.input', array(
                    'required' => true, 
                    'autocomplete'=> 'current-password'
                )) ?>
                <a href="<?= @route('view=token') ?>">
                    <?= @text('COM-PEOPLE-SESSION-FORGOT-PASSWORD'); ?>
                </a>
            </div>
        </div>
    </fieldset>

    <div class="form-actions">
        <button
            type="submit"
            name="submit-btn"
            class="btn btn-primary btn-large pull-right"
            data-loading-text="<?= @text('LIB-AN-ACTION-PLEASE-WAIT') ?>">
            <?= @text('COM-PEOPLE-ACTION-LOGIN') ?>
        </button>
    </div>
</form>
