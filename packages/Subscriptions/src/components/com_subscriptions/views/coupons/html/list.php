<? defined('KOOWA') or die('Restricted access');?>

<div class="an-entities">

	<? foreach ($coupons as $coupon) : ?>
	<?= @view('coupon')->layout('list')->coupon($coupon) ?>
	<? endforeach; ?>

    <? if (count($coupons) == 0): ?>
	<?= @message(@text('COM-SUBSCRIPTIONS-COUPONS-EMPTY-LIST-MESSAGE')) ?>
    <? endif; ?>

</div>

<?= @pagination($coupons, array('url' => @route('layout=list'))) ?>
