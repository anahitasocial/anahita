<?php defined('KOOWA') or die ?>
<?php if (!empty($commands)) : ?>
<ul class="an-actions">
<?php foreach($commands as $command) : ?>
<li><?= $helper->command($command) ?></li> 
<?php endforeach; ?>
</ul>
<?php endif;?>