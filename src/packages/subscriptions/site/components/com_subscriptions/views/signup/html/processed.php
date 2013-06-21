<?php defined('KOOWA') or die('Restricted access');?>

<?= @template('_steps', array('current_step'=>'processed')) ?>

<module position="sidebar-b" style="none"></module>

<div class="alert alert-success alert-block">
	<p><?= @text('COM-SUB-THANK-YOU') ?></p>
	<?php if ( !$viewer->guest() ) : ?>
	<p><a class="btn" href="<?= @route('view=subscription') ?>"><?= @text('COM-SUB-VIEW-YOUR-SUBSCRIPTION') ?></a></p>
	<?php endif;?>
</div>

<?php if ( $viewer->guest() ) : ?>
<form action="<?@route()?>" method="post">
    <input type="hidden" name="_action" value="login" />
    <input type="submit" class="btn" value="Login">
</form>
<?php endif; ?>
