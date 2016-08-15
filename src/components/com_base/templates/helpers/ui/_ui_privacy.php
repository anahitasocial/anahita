<? defined('KOOWA') or die('Restricted access'); ?>

<? if ($auto_submit) : ?>
<form class="privacy" action="<?= @route($entity->getURL()) ?>" method="post">
<input type="hidden" name="action" value="setprivacy" />
<input type="hidden" name="privacy_name" value="<?= $name ?>" />
<? else : ?>
<input type="hidden" name="privacy_name[]" value="<?= $name ?>" />
<? endif; ?>
	<select class="input-large <?= ($auto_submit) ? 'autosubmit' : '' ?>" name="<?= $name ?>">
			<?= @html('options', $options, $selected)?>
	</select>
<? if ($auto_submit) : ?>
</form>
<? endif;?>
