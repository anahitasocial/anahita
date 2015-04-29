<?php defined('KOOWA') or die ?>

<?php $alert_title = sprintf(@text('COM-CONNECT-LOGIN-PROMPT-HI'), $api->getUser()->name, ucfirst($api->getName())) ?>

<div class="row">
	<div class="span8">
    	
    <?php if(@service('com://site/people.controller.person')->canRegister()) : ?>
    <div class="alert alert-block">
    	<h4><?= $alert_title ?></h4>
    	<p><?= @text('COM-CONNECT-LOGIN-PROMPT-SIGN-IN-OR-SIGNUP') ?></p>
    </div>
    
    <div class="well">    
        <button data-trigger="OpenModal" data-url="<?= @route('option=com_people&view=session&layout=modal&connect=1') ?>" class="btn">
            <?= @text('Sign In') ?>
        </button> 
        
        <button data-trigger="OpenModal" data-url="<?= @route('option=com_people&view=person&layout=add&modal=1') ?>" class="btn">
            <?= @text('Register') ?>
        </button>
    </div>
    
    <?php else : ?>
    <div class="alert alert-block">
    <h4><?= $alert_title ?></h4>
    <p><?= @text('COM-CONNECT-LOGIN-PROMPT-SIGN-IN')?></p>
    </div>
    
    <div class="well"> 
    	<button data-trigger="OpenModal" data-url="<?= @route('option=com_people&view=session&layout=modal&connect=1') ?>" class="btn btn-primary">
    		<?= @text('COM-CONNECT-LOGIN-PROMPT-LOGIN')?>
    	</button>
    </div>
    <?php endif;?>

	</div>
</div>
