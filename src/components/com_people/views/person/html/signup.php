<? defined('KOOWA') or die; ?>

<? @service('application.dispatcher')->getRequest()->tmpl = 'component'; ?>
<? $anybody = @service('repos:people.person')->getQuery(true)->fetchValue('id'); ?>

<div class="row">
    <div class="offset3 span6">

        <? if (!$anybody): ?>
        <div class="alert alert-info alert-block">
            <h4><?= @text('COM-PEOPLE-WELCOME1') ?></h4>
            <p><?= @text('COM-PEOPLE-WELCOME2') ?></p>
        </div>
        <? endif; ?>

        <? $return = empty($return) ? null : $return; ?>

        <form
            action="<?= @route('view=person') ?>"
            method="post"
            name="person-form"
            id="person-form"
            class="well recaptcha"
            autocomplete="off"
        >
            <fieldset>
                <legend>
                    <?= @text('COM-PEOPLE-ACTION-CREATE-AN-ACCOUNT') ?>
                </legend>

				<div class="control-group">
                    <label class="control-label"  for="person-given-name">
                        <?= @text('COM-PEOPLE-GIVEN-NAME'); ?>
                    </label>
                    <div class="controls">
                        <input
                            required
                            class="input-block-level"
                            type="text"
                            id="person-given-name"
                            name="givenName"
							maxlength="25"
                            minlength="3"
                         />
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label"  for="person-family-name">
                        <?= @text('COM-PEOPLE-FAMILY-NAME'); ?>
                    </label>
                    <div class="controls">
                        <input
                            required
                            class="input-block-level"
                            type="text"
                            id="person-family-name"
                            name="familyName"
							maxlength="25"
                            minlength="3"
                         />
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label"  for="person-username">
                        <?= @text('COM-PEOPLE-SIGNUP-USERNAME'); ?>
                    </label>
                    <div class="controls">
                        <input
							required
							data-validate="username"
							data-url="<?= @route('view=person', false) ?>"
							type="text"
							id="person-username"
							class="input-block-level"
							name="username"
							pattern="<?= @helper('regex.username') ?>"
							maxlength="100"
							minlength="6"
						/>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label"  for="person-email">
                        <?= @text('COM-PEOPLE-SIGNUP-EMAIL'); ?>
                    </label>
                    <div class="controls">
                       <input
					 		required
					 		data-validate="email"
							data-url="<?= @route('view=person', false) ?>"
							type="email"
							name="email"
							pattern="<?= @helper('regex.email') ?>"
							id="person-email"
							class="input-block-level"
							maxlength="100"
							minlength="10"
						/>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label"  for="password">
                        <?= @text('COM-PEOPLE-SIGNUP-PASSWORD'); ?>
                    </label>
                    <div class="controls">
                        <?= @helper('password.input') ?>
                    </div>
                </div>
            </fieldset>

            <div class="form-actions">
                <button
					type="submit"
					class="btn btn-primary btn-large pull-right"
					data-loading-text="<?= @text('LIB-AN-ACTION-PLEASE-WAIT') ?>"
				>
                    <?= @text('COM-PEOPLE-ACTION-REGISTER') ?>
                </button>
            </div>
        </form>

	</div>
</div>
