<? defined('KOOWA') or die('Restricted access'); ?>

<div class="row">
	<div class="span6">

	    <?= @helper('ui.header'); ?>

        <div class="an-entities">
        <? foreach ($packages as $package) : ?>
            <?= @view('package')->layout('list')->package($package) ?>
        <? endforeach; ?>
        </div>
	</div>
</div>
