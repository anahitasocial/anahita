<?php defined('KOOWA') or die('Restricted access');?>

<div class="an-entities">

	<?php foreach($vats as $vat) : ?>
	<?= @view('vat')->layout('list')->vat( $vat ) ?>
	<?php endforeach; ?>
    
    <?php if( count( $vats ) == 0): ?>
	<?= @message(@text('COM-SUBSCRIPTIONS-VATS-EMPTY-LIST-MESSAGE')) ?>
    <?php endif; ?>
    
</div>

<?= @pagination( $vats, array('url' => @route( 'layout=list') ) ) ?>