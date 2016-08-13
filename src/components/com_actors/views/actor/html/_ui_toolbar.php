<? defined('KOOWA') or die ?>

<? $commands = $toolbar->getCommands() ?>

<? if (count($commands)): ?>
    <div class="actor-profile-toolbar btn-toolbar clearfix">
    	<? if ($command = $commands->extract('addrequest')): ?>
            <?= @helper('ui.command', $command->class('btn btn-primary')) ?>
        <? elseif ($command = $commands->extract('deleterequest')): ?>
            <?= @helper('ui.command', $command->class('btn btn-primary')) ?>
        <? elseif ($command = $commands->extract('follow')): ?>
            <?= @helper('ui.command', $command->class('btn btn-primary')) ?>
        <? elseif ($command = $commands->extract('unfollow')): ?>
            <?= @helper('ui.command', $command->class('btn')) ?>
        <? elseif ($command = $commands->extract('unblock')): ?>
            <?= @helper('ui.command', $command->class('btn btn-primary')) ?>
        <? endif ?>

        <? if ($commands->count() > 0): ?>
            <div class="btn-group">
                <?= @helper('ui.dropdown', $commands) ?>
            </div>
        <? endif ?>
    </div>
<? endif ?>
