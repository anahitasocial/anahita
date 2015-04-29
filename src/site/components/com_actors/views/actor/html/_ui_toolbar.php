<?php defined('KOOWA') or die ?>

<?php $commands = $toolbar->getCommands() ?>

<?php if(count($commands)): ?>
    <div class="actor-profile-toolbar btn-toolbar clearfix">
    	<?php if ($command = $commands->extract('addrequest')): ?>
            <?= @helper('ui.command', $command->class('btn btn-primary')) ?>
        <?php elseif ($command = $commands->extract('deleterequest')): ?>
            <?= @helper('ui.command', $command->class('btn btn-primary')) ?>    
        <?php elseif ($command = $commands->extract('follow')): ?>
            <?= @helper('ui.command', $command->class('btn btn-primary')) ?> 
        <?php elseif ($command = $commands->extract('unfollow')): ?>
            <?= @helper('ui.command', $command->class('btn')) ?> 
        <?php elseif ($command = $commands->extract('unblock')): ?>
            <?= @helper('ui.command', $command->class('btn btn-primary')) ?>
        <?php endif ?>
        
        <?php if($commands->count() > 0): ?>
            <div class="btn-group">
                <?= @helper('ui.dropdown', $commands) ?>
            </div>
        <?php endif ?>
    </div>
<?php endif ?>