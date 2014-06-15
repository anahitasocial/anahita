<?php defined('KOOWA') or die ?>

<?php $commands = $toolbar->getCommands() ?>

<?php if(count($commands)): ?>
<div class="hashtag-profile-toolbar btn-toolbar clearfix" data-behavior="BS.Dropdown">
	<?php if($command = $commands->extract('edit')): ?>
	<?= @helper('ui.command', $command->class('btn admin')) ?> 
	<?php endif ?>
</div>
<?php endif ?>