<?php if ( count($toolbar->getCommands()) ) : ?>

<?php 
$commands = $toolbar->getCommands(); 
$entity = $toolbar->getController()->getItem();
$voted = $entity->votedUp(get_viewer()) ? 'active' : '';
?>

<div class="btn-toolbar clearfix" data-behavior="BS.Dropdown">
    <?php if ( $new = $commands->extract('new') ) :?>
         <?= @html('tag', 'a', $new->label, $new->getAttributes())->class('btn btn-primary') ?> 
    <?php endif;?>
    
    <?php if($voted) : ?>
        <?php $unvote = $commands->extract('unvote'); ?>
        <?= @html('tag', 'a', $unvote->label, $unvote->getAttributes())->class('action-unvote btn') ?>
    <?php else : ?>
        <?php $vote = $commands->extract('vote'); ?>
        <?= @html('tag', 'a', $vote->label, $vote->getAttributes())->class('action-vote btn') ?>
    <?php endif; ?>
    
    <?php if ( $vote = $commands->extract('vote') ) : ?>
    <?php 
    $entity = $toolbar->getController()->getItem();
    $voted = $entity->votedUp(get_viewer()) ? 'active' : ''; 
    ?>
    <?= @html('tag', 'a', $vote->label, $vote->getAttributes())->class('action-vote btn '.$active) ?>
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