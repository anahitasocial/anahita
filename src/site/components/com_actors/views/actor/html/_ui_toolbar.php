<?php if ( count($toolbar->getCommands()) ) : ?>
<?php $commands = $toolbar->getCommands(); ?>
<div class="actor-profile-toolbar btn-toolbar clearfix" data-behavior="BS.Dropdown">
<?php if ( $command = $commands->extract('follow') ) : ?>
    <?= @helper('ui.command', $command->class('btn btn-primary')) ?> 
<?php elseif ( $command = $commands->extract('unfollow') ) : ?>
    <?= @helper('ui.command', $command->class('btn')) ?> 
<?php elseif ( $command = $commands->extract('unblock') ) : ?>
    <?= @helper('ui.command', $command->class('btn btn-primary')) ?>
<?php endif; ?>

<?php if ( $command = $commands->extract('edit') ) : ?>
    <?= @helper('ui.command', $command->class('btn admin')) ?> 
<?php endif; ?>
<?php if ( $commands->count() > 0 || !$viewer->eql($item) ) : ?>
    <div class="btn-group pull-right">
        <?php if ( !$viewer->eql($item) && $item->authorize('access') ) : ?>
        <?php JFactory::getLanguage()->load('com_notifications');  ?>
        <a data-trigger="BS.showPopup" data-bs-showpopup-target="!body #notification-modal" class="btn small notification">
            <i class="icon-exclamation-sign"></i>
             <?= @text('COM-NOTIFICATIONS-EDIT-BUTTON')?>
            </a>
        <?php endif; ?>
        <?= @helper('ui.dropdown', $commands)?>           
    </div>
<?php endif;?>

<?= @template('_notification_modal')?>
</div>
<?php endif; ?>