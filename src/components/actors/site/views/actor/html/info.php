<?php defined('KOOWA') or die('Restricted access'); ?>

<?php foreach($profile as $header => $values)  : ?>	
	<h4><?= @text($header) ?></h4>
	<?php foreach($values as $label => $value) : ?>
	<dl class="dl-horizontal">
		<dt><?= @text($label) ?></dt>
		<dd><?= @text($value) ?></dd>
	</dl>
	<?php endforeach;?>
<?php endforeach;?>