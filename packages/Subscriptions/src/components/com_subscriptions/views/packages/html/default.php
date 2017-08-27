<? defined('KOOWA') or die('Restricted access'); ?>

<div class="row">
	<div class="span6">

	    <?= @helper('ui.header'); ?>

		<? if(count($packages)): ?>
        <div class="an-entities">
        <? foreach ($packages as $package) : ?>
            <?= @view('package')->layout('list')->package($package) ?>
        <? endforeach; ?>
        </div>
		<? else : ?>
		<?= @message('COM-SUBSCRIPTIONS-PACKAGE-EMPTY-LIST') ?>
		<? endif; ?>
	</div>
</div>
