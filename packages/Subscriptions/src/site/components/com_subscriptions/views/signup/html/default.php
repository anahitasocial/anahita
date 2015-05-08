<?php defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step'=>'default')) ?>

<div class="row">
	<div class="span8">
        <div id="sub-tos">
        	
        	<h2 class="entity-title">
        	    <?= @text('COM-SUB-TERMS-TITLE') ?>
        	</h2>
        	
        	<div class="entity-description">
        		Terms of services go here
        	</div>
        	
        	<?php 
        	$class = '';
        	$checkboxes = array( 
        	       get_config_value('subscriptions.tos_confirmation_checkbox1'), 
        	       get_config_value('subscriptions.tos_confirmation_checkbox2'));
            ?> 
            
            <div class="entity-description">
                <?php foreach($checkboxes as $i => $checkbox) : ?>
            	   <?php if ( !$checkbox ) continue ?>
            	   <?php $class = 'disabled'; ?>
            	   <div class="clearfix alert alert-box alert-warning">
            	         <?= @html('checkbox','confirm'.$i)->class('confirm-tos')?>
                         <?= $checkbox ?>
            	   </div>
            	<?php endforeach;?>
            	<div class="well">
            		<a href="<?= @route('view=packages') ?>" class="btn">
            		    <?=@text('COM-SUB-TERM-CANCEL')?>
            		</a> 
            		 
            		<a id="proceed" href="<?=@route('layout=payment&id='.$item->id)?>" class="btn btn-primary">
            		    <?=@text('COM-SUB-TERM-AGREE')?>
            		</a>
            	</div>
        	</div>
        	
        </div>
	</div>
</div>

