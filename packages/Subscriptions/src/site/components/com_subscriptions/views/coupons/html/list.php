<?php defined('KOOWA') or die('Restricted access');?>

<div class="an-entities">

	<?php foreach( $coupons as $coupon ) : ?>
	<?= @view('coupon')->layout('list')->coupon( $coupon ) ?>
	<?php endforeach; ?>
    
    <?php if( count( $coupons ) == 0 ): ?>
	<?= @message(@text('COM-SUBSCRIPTIONS-COUPONS-EMPTY-LIST-MESSAGE')) ?>
    <?php endif; ?>
    
</div>

<?= @pagination( $coupons, array('url' => @route( 'layout=list') ) ) ?>