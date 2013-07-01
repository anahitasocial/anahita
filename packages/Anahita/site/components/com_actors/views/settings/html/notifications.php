<?php defined('KOOWA') or die; ?>
<form>
<?php foreach($apps as $app) : ?>
<?= @helper('ui.form', array(
	$app->getName() => @html('select', 'd', array('options'=>array('YES','NO')))
))?>
<?php endforeach;?>
</form>