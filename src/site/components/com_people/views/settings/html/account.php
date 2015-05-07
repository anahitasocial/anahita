<?php defined('KOOWA') or die; ?>

<h3><?= @text('COM-PEOPLE-PROFILE-EDIT-ACCOUNT-INFORMATION') ?></h3>

<?php $user = $item->getJUserObject() ?>

<form action="<?= @route('view=person&id='.$item->id) ?>" method="post" name="person-form" id="person-form" autocomplete="off">

	<div class="control-group">
		<label class="control-label"  for="person-name">
		<?= @text( 'COM-ACTORS-NAME' ); ?>:
	    </label>
	    <div class="controls">
	    	<input required type="text" id="person-name" class="input-block-level" name="name" value="<?= $item->name?>" maxlength="25" />
	    </div>
	</div>
	
	<div class="control-group">
		<label class="control-label"  for="person-username">
		<?= @text('COM-PEOPLE-USERNAME'); ?>:
	    </label>
	    <div class="controls">
	        <? $usernamePattern = "^[A-Za-z][A-Za-z0-9_-]*$"; ?>
	        <input required data-validate="username" data-url="<?= @route('view=person', false ) ?>" type="text" id="person-username" class="input-block-level" name="username" pattern="<?= $usernamePattern ?>" value="<?= $item->username ?>" maxlength="25" minlength="6" />
	    </div>
	</div>
	        
	<div class="control-group">
		<label class="control-label"  for="person-email">
			<?= @text('COM-PEOPLE-EMAIL'); ?>:
		</label>
	    <div class="controls">
	       <?php $emailPattern = "^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" ?>
	       <input required data-validate="email" data-url="<?= @route('view=person', false) ?>" type="email" name="email" pattern="<?= $emailPattern ?>" id="person-email" class="input-block-level" maxlength="100" value="<?= $item->email ?>"  />
	    </div>
	</div>
	        
	<div class="control-group">
		<label class="control-label"  for="password">
	    	<?= @text('COM-PEOPLE-PASSWORD'); ?>:
	    </label>
	    <div class="controls">
	    	<?= @helper('password.input', array( 'required' => false ) ) ?>	    		
	    </div>
	</div>

	<?php        
	$user = JFactory::getUser($item->userId);
	$params = $user->getParameters(true)->renderToArray();        
	?>                
	
	<div class="control-group">
		<label class="control-label" for="paramslanguage">
			<?= @text('COM-PEOPLE-LANGUAGE') ?>
		</label>
		<div class="controls">
			<?= $params['language'][1] ?>
		</div>
	</div>
	        
	<div class="form-actions">
		<button type="submit" class="btn" data-loading-text="<?= @text('LIB-AN-ACTION-SAVING') ?>">
		    <?= @text('LIB-AN-ACTION-SAVE'); ?>
		</button>
	</div>
    
</form>