<? defined('KOOWA') or die('Restricted access') ?>

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
            <? if (@service('com:people.controller.person')->permission->isRegistrationOpen()): ?>
            <small>
                <? $signup_link = 'option=com_people&view=person&layout=signup' ?>
                <? $signup_link .= $return ? '&return='.$return : '' ?>
                <a class="pull-right" href="<?= @route($signup_link) ?>">
                    <?= @text('COM-PEOPLE-ACTION-CREATE-AN-ACCOUNT')?>
                </a>
            </small>
            <? endif;?>
        </legend>

        <? if ($connect && KService::get('koowa:loader')->loadIdentifier('com://site/connect.template.helper.service')): ?>
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
                    size="25"/>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <?= @helper('password.input', array('required' => true)) ?>
                <a href="<?= @route('view=token') ?>">
                    <?= @text('COM-PEOPLE-SESSION-FORGOT-PASSWORD'); ?>
                </a>
            </div>
        </div>

        <div class="control-group">
            <label class="checkbox">
                <input
                    type="checkbox"
                    name="remember"
                    value="true"
                    alt="<?= @text('COM-PEOPLE-SESSION-REMEMBER-ME'); ?>"/>
                <?= @text('COM-PEOPLE-SESSION-REMEMBER-ME'); ?>
            </label>
        </div>
    </fieldset>

    <div class="form-actions">
        <button
            type="submit"
            class="btn btn-primary btn-large pull-right"
            data-loading-text="<?= @text('LIB-AN-ACTION-PLEASE-WAIT') ?>">
            <?= @text('COM-PEOPLE-ACTION-LOGIN') ?>
        </button>
    </div>
</form>
