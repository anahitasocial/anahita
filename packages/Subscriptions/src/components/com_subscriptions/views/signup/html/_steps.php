<? defined('KOOWA') or die('Restricted access');?>

<?
$steps = array(
    AnTranslator::_('COM-SUBSCRIPTIONS-STEP-TOS') => 'default',
    AnTranslator::_('COM-SUBSCRIPTIONS-STEP-REGISTER') => 'login',
    AnTranslator::_('COM-SUBSCRIPTIONS-STEP-PAYMENT-METHOD') => 'payment',
    AnTranslator::_('COM-SUBSCRIPTIONS-STEP-PAYMENT-CONFIRM') => 'confirm',
    AnTranslator::_('COM-SUBSCRIPTIONS-STEP-PAYMENT-PROCESSED') => 'processed',
);
?>


<ol class="sub-steps">
	<? foreach ($steps as $label => $step) : ?>
	<li class="<?= ($step == $current_step) ? 'active' : '' ?>">
		<?= $label ?>
	</li>
	<? endforeach; ?>
</ol>
