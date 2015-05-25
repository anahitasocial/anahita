<?php defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step'=>'processed')) ?>

<div class="row">
	<div class="span8">	
        <div class="alert alert-success alert-block">
        	<p><?= @text('COM-SUBSCRIPTIONS-THANK-YOU') ?></p>
        	<?php if ( !$viewer->guest() ) : ?>
        	<p>
        	    <a class="btn" href="<?= @route( $viewer->getURL().'&get=settings&edit=subscription' ) ?>">
        	       <?= @text('COM-SUBSCRIPTIONS-VIEW-YOUR-SUBSCRIPTION') ?>
        	    </a>
        	</p>
        	<?php endif;?>
        </div>
        
        <?php if ( $viewer->guest() ) : ?>
        <p>
            <a data-trigger="OpenModal" class="btn btn-primary btn-large" href="#" data-url="<?= @route('option=people&view=session&layout=modal&connect=1' ) ?>" >
                <?= @text('LIB-AN-ACTION-LOGIN') ?>                                               
            </a>
        </p>
        <?php endif; ?>
	</div>
</div>
