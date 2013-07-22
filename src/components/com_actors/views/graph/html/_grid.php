<?php defined('KOOWA') or die ?>

<div class="media-grid">
	<?php foreach($actors as $actor) : ?>
	<div><?= @avatar($actor) ?></div>	
	<?php endforeach; ?>
</div>