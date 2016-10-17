<? defined('KOOWA') or die; ?>

<?= @helper('ui.header') ?>

<div class="row">
    <div class="span6">
        <form
            action="<?= @route('view=person') ?>"
            method="post"
            name="person-form"
            id="person-form"
            autocomplete="off"
        >
            <fieldset>
                <legend><?= @text('COM-PEOPLE-ADD-TITLE') ?></legend>

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
                        <?= @text('COM-PEOPLE-USERNAME'); ?>
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
                        <?= @text('COM-PEOPLE-EMAIL'); ?>
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
                    <label class="control-label" for="person-usertype">
                        <?= @text('COM-PEOPLE-USERTYPE'); ?>
                    </label>
                    <div class="controls">
                        <?= @helper('usertypes') ?>
                    </div>
                </div>
            </fieldset>

            <div class="form-actions">
                <a href="<?= @route('view=people') ?>" class="btn">
                    <?= @text('LIB-AN-ACTION-CANCEL') ?>
                </a>
                <button type="submit" class="btn btn-primary">
                    <?= @text('LIB-AN-ACTION-ADD') ?>
                </button>
            </div>
        </form>

    </div>
</div>
