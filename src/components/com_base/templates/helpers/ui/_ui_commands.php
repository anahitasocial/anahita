<? defined('KOOWA') or die ?>

<? if (!empty($commands)) : ?>
<ul class="an-actions">
<? foreach ($commands as $command) : ?>
<li><?= $helper->command($command) ?></li>
<? endforeach; ?>
</ul>
<? endif;?>
