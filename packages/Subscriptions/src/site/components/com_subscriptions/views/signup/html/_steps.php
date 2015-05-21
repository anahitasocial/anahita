<?php defined('KOOWA') or die('Restricted access');?>

<?php 
$steps = array(	
	JText::_('COM-SUBSCRIPTIONS-STEP-TOS') => 'default',
	JText::_('COM-SUBSCRIPTIONS-STEP-REGISTER') => 'login',
	JText::_('COM-SUBSCRIPTIONS-STEP-PAYMENT-METHOD') 	 => 'payment',
	JText::_('COM-SUBSCRIPTIONS-STEP-PAYMENT-CONFIRM')	 => 'confirm',
	JText::_('COM-SUBSCRIPTIONS-STEP-PAYMENT-PROCESSED') => 'processed'
);
?>


<ol class="sub-steps">
	<?php foreach($steps as $label => $step) : ?>
	<li class="<?= ($step == $current_step) ? 'active' : '' ?>">
		<?= $label ?>
	</li>
	<?php endforeach; ?>	
</ol>




