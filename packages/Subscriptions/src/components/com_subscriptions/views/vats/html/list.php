<? defined('KOOWA') or die('Restricted access');?>

<div class="an-entities">

	<? foreach ($vats as $vat) : ?>
	<?= @view('vat')->layout('list')->vat($vat) ?>
	<? endforeach; ?>

    <? if (count($vats) == 0): ?>
	<?= @message(@text('COM-SUBSCRIPTIONS-VATS-EMPTY-LIST-MESSAGE')) ?>
    <? endif; ?>

</div>

<?= @pagination($vats, array('url' => @route('layout=list'))) ?>
