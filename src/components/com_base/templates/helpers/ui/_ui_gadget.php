<? defined('KOOWA') or die ?>

<? $url = (strlen($gadget->url)) ? @route($gadget->url) : ''; ?>

<div class="an-gadget" data-url="<?= $url ?>">
    <? if ($gadget->show_title !== false) : ?>
    	<? if (strlen($gadget->title)) : ?>
    	<h3 class="gadget-title">
            <? if ($gadget->title_url) : ?>
            <a href="<?= @route($gadget->title_url) ?>">
                <?= $gadget->title ?>
            </a>
            <? else : ?>
            <?=$gadget->title?>
            <? endif;?>

            <? if (strlen($gadget->action) && $gadget->action_url) : ?>
	        <a class="gadget-action" href="<?= @route($gadget->action_url) ?>">
            <?= $gadget->action ?>
	        </a>
	        <? endif; ?>
    	</h3>
    	<? endif; ?>
	<? endif;?>

	<div class="gadget-content">
	    <?= $gadget->content ?>
	</div>
</div>
