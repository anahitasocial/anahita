<? defined('KOOWA') or die('Restricted access'); ?>

<? foreach ($profile as $header => $values)  : ?>
	<h4><?= @text($header) ?></h4>
	<? foreach ($values as $label => $value) : ?>
	<dl class="dl-horizontal">
		<dt><?= @text($label) ?></dt>
		<dd><?= @text($value) ?></dd>
	</dl>
	<? endforeach;?>
<? endforeach;?>
