<? defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step' => 'processed')) ?>

<div class="row">
	<div class="span8">
        <div class="alert alert-success alert-block">
        	<p><?= @text('COM-SUBSCRIPTIONS-THANK-YOU') ?></p>
        	<? if (!$viewer->guest()) : ?>
        	<p>
        	    <a class="btn" href="<?= @route($viewer->getURL().'&get=settings&edit=subscription') ?>">
        	       <?= @text('COM-SUBSCRIPTIONS-VIEW-YOUR-SUBSCRIPTION') ?>
        	    </a>
        	</p>
        	<? endif;?>
        </div>

        <? if ($viewer->guest()) : ?>
        <p>
            <a class="btn btn-primary btn-large" href="<?= @route('option=people&view=session&connect=1') ?>" >
                <?= @text('LIB-AN-ACTION-LOGIN') ?>
            </a>
        </p>
        <? endif; ?>
	</div>
</div>
