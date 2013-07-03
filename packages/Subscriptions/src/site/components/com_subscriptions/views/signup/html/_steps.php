<?php defined('KOOWA') or die('Restricted access');?>

<?php 
$steps = array(	
	JText::_('COM-SUB-STEP-TOS') => 'default',
	JText::_('COM-SUB-STEP-REGISTER') => 'login',
	JText::_('COM-SUB-STEP-PAYMENT-METHOD') 	 => 'payment',
	JText::_('COM-SUB-STEP-PAYMENT-CONFIRM')	 => 'confirm',
	JText::_('COM-SUB-STEP-PAYMENT-PROCESSED') => 'processed'
);
?>


<ol class="sub-steps">
	<?php foreach($steps as $label => $step) : ?>
	<li class="<?= ($step == $current_step) ? 'active' : '' ?>">
		<?= $label ?>
	</li>
	<?php endforeach; ?>	
</ol>




