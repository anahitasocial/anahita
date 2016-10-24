<? defined('KOOWA') or die; ?>

<h3><?= @text('COM-PEOPLE-PROFILE-EDIT-ACCOUNT-INFORMATION') ?></h3>

<ul>
    <li>
      <b><?= @text('COM-PERSON-META-CREATED-ON') ?>: </b>
      <?= @date($item->creationTime) ?>
    </li>
    <li>
      <b><?= @text('COM-PERSON-META-LAST-LOGIN') ?>: </b>
      <? $lastLoginDate = $item->lastVisitDate ?>
      <? if($lastLoginDate->compare($item->creationTime) < 0): ?>
      <?= @text('COM-PERSON-META-NEVER-LOGGED-IN') ?>
      <? else : ?>
      <?= @date($lastLoginDate) ?>
      <? endif; ?>
    </li>
</ul>

<? if(isset($_SESSION['reset_password_prompt']) && $_SESSION['reset_password_prompt'] == 1): ?>
<?= @message(@text('COM-PEOPLE-PROMPT-RESET-PASSWORD'), array('type'=>'alert')) ?>
<? endif; ?>

<form action="<?= @route($item->getURL(false)) ?>" method="post" autocomplete="off">
    <input type="hidden" name="action" value="edit" />

	<div class="control-group">
		<label class="control-label"  for="person-username">
		<?= @text('COM-PEOPLE-USERNAME'); ?>:
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
              value="<?= $item->username ?>"
              maxlength="25"
              minlength="6"
           />
	    </div>
	</div>

	<div class="control-group">
		<label class="control-label"  for="person-email">
			<?= @text('COM-PEOPLE-EMAIL'); ?>:
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
            value="<?= $item->email ?>"
         />
	    </div>
	</div>

	<div class="control-group">
		<label class="control-label"  for="password">
	    	<?= @text('COM-PEOPLE-PASSWORD'); ?>:
	    </label>
	    <div class="controls">
	    	 <?= @helper('password.input') ?>
	    </div>
	</div>

	<div class="form-actions">
		<button type="submit" class="btn" data-loading-text="<?= @text('LIB-AN-ACTION-SAVING') ?>">
            <?= @text('LIB-AN-ACTION-SAVE'); ?>
        </button>
	</div>
</form>
