<?php if ( count($toolbar->getCommands()) ) : ?>
<?php 
$commands = $toolbar->getCommands();
foreach($commands as $command) 
{
    if ( $href = $command->getAttribute('href') ) {
        $command->setAttribute('href', @route($href));
    } 
}
?>
<div class="btn-toolbar clearfix" data-behavior="BS.Dropdown">
    <?php if ( $new = $commands->extract('new') ) :?>
         <?= @html('tag', 'a', $new->label, $new->getAttributes())->class('btn btn-primary') ?> 
    <?php endif;?>
    <?php if ( $vote = $commands->extract('vote') ) :?>
         <?php $unvote = $commands->extract('unvote') ?>
         <span><?= @html('tag', 'a', $vote->label, $vote->getAttributes())->class('btn') ?></span>
         <span><?= @html('tag', 'a', $unvote->label, $unvote->getAttributes())->class('btn btn-primary') ?></span> 
    <?php endif;?> 
        
    <?php if ( $commands->count() > 1 ) : ?>
    <div class="btn-group pull-right">
        <?= @helper('ui.dropdown', $commands)?>   
    </div>    
    <?php elseif ( $commands->count() == 1 ) : ?>
    <?php $command = $commands->extract(); ?>
    <div class="pull-right">    
    <span><?= @html('tag', 'a', $command->label, $command->getAttributes())->class('btn') ?></span>
    </div>
    <?php endif;?>
</div>
<?php endif;?>