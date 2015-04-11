<?php defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step'=>'login')) ?>

<div class="row">
	<div class="span8">  	
        <h2><?= @text('COM-SUB-LOGIN-PROMPT-TITLE') ?></h2>
        <p><?= @text('COM-SUB-LOGIN-PROMPT-DESCRIPTION'); ?></p>
        
        <?php @template('_login_form', array('username'=>'', 'password'=>'', 'return_url'=>base64_encode(@route(array('id'=>$item->id, 'layout'=>'payment'))))) ?>
        
        <div class="form-actions">
        	<a data-trigger="PersonAuthenticationModal" class="btn btn-large btn-primary" data-url="<?= @route('option=people&view=session&layout=modal_simple&return='.base64UrlEncode(@route('layout=payment')))?>">
        		<?=@text('COM-SUB-LOGIN-ACTION')?>
        	</a>
        </div>
        
        <h2><?= @text('COM-SUB-REGISTER-PROMPT-TITLE') ?></h2>
        <p><?= @text('COM-SUB-REGISTER-PROMMPT-DESCRIPTION') ?></p>
        
        <form action="<?= @route( 'id='.$item->id )?>" method="post">
        	<input type="hidden" name="action" value="payment" />
        			
        	<?= @helper('ui.form', array(
        		'COM-SUB-REGISTER-FULL-NAME'       => @html('textfield', 'user[name]', $person->name)->required('true'),
        		'COM-SUB-REGISTER-USERNAME'	       => @html('textfield', 'user[username]', $person->username)->required('true')->dataValidate('username')->dataUrl( @route('option=com_people&view=person', false) ),
        		'COM-SUB-REGISTER-EMAIL'		   => @html('textfield', 'user[email]', $person->email)->required('true')->dataValidate('username')->dataUrl( @route('option=com_people&view=person', false) ),
        		'COM-SUB-REGISTER-PASSWORD'		   => @html('passwordfield',  'user[password]', '')->required('true')				
        	)); ?>
        			
        	<div class="form-actions">
        		<button type="submit" class="btn btn-large">
        		    <?= @text('COM-SUB-REGISTER-ACTION') ?>
        		</button>
        	</div>
        </form>
	</div>
</div>