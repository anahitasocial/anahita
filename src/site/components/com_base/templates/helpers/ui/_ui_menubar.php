<?php defined('KOOWA') or die('Restricted access') ?>

<?php if ( $menubar->getTitle() ) : ?>
<h1 class="an-page-header"><?=$menubar->getTitle()?></h1>
<?php endif;?>

<?php if( count($menubar->getCommands()) ) :?>
<ul class="toolbar">
<?php foreach($menubar->getCommands() as $command) : ?>
	<li><?= @html('tag', 'a', $command->label, $command->getAttributes()) ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>