<? defined('KOOWA') or die('Restricted access') ?>

<? if ($menubar->getTitle()) : ?>
<h1 class="an-page-header"><?=$menubar->getTitle()?></h1>
<? endif;?>

<? if (count($menubar->getCommands())) :?>
<ul class="toolbar inline">
<? foreach ($menubar->getCommands() as $command) : ?>
	<li><?= @html('tag', 'a', $command->label, $command->getAttributes()) ?></li>
<? endforeach; ?>
</ul>
<? endif; ?>
