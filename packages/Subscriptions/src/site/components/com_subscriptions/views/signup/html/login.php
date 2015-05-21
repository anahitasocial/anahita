<?php defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step'=>'login')) ?>

<div class="row">
	<div class="span8">  	
	    
	    <h1><?= @text('COM-SUBSCRIPTIONS-STEP-REGISTER') ?></h1>
	    
	    <div class="well">
            <p class="lead">
                <?= @text('COM-SUBSCRIPTIONS-LOGIN-PROMPT'); ?>
            </p>
            
            <p>
                <?php $return = base64UrlEncode( KRequest::url() ); ?>
                <a data-trigger="OpenModal" class="btn btn-primary btn-large" href="#" data-url="<?= @route('option=people&view=session&layout=modal&connect=1&return='.$return ) ?>" >
                    <?= @text('LIB-AN-ACTION-LOGIN') ?>                                               
                </a>
            </p>
        </div>     

        <div class="well">
        
            <p class="lead">
                <?= @text('COM-SUBSCRIPTIONS-REGISTER-PROMMPT-PROMPT') ?>
            </p>
            
            <form action="<?= @route( 'id='.$item->id ) ?>" method="post" name="person-form" id="person-form">
            	<input type="hidden" name="action" value="payment" />
            			
            	<?php $usernamePattern = "^[A-Za-z][A-Za-z0-9_-]*$"; ?>		
            	<?php $emailPattern = "^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" ?>		
            			
            	<?= @helper('ui.form', array(
            		'COM-SUBSCRIPTIONS-REGISTER-FULL-NAME' => @html('textfield', 'user[name]', $person->name)->required('true'),
            		
            		'COM-SUBSCRIPTIONS-REGISTER-USERNAME' => @html('textfield', 'user[username]', $person->username)
            		 ->required('true')->dataValidate('username')->dataUrl( @route('option=com_people&view=person', false) )
            		 ->id('person-username')->pattern( $usernamePattern )->maxlength(100)->minlength(6),
            		 
            		'COM-SUBSCRIPTIONS-REGISTER-EMAIL' => @html('textfield', 'user[email]', $person->email)
            		->required('true')->dataValidate('email')->dataUrl( @route('option=com_people&view=person', false) )
            		->id('person-email')->pattern( $emailPattern )->maxlength(100)->minlength(10),
            		
            		'COM-SUBSCRIPTIONS-REGISTER-PASSWORD' => @html('passwordfield',  'user[password]', '')->required('true')				
            	)); ?>
            			
            	<div class="form-actions">
            		<button type="submit" class="btn btn-large">
            		    <?= @text('LIB-AN-ACTION-REGISTER') ?>
            		</button>
            	</div>
            </form>
        
        </div>
        
	</div>
</div>