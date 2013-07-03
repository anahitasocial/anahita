<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
<?php if ( $type != 'notification' ) : ?>
<?=sprintf(@text('COM-ACTORS-TITLE-FOLLOW-ACTIVITY'),@name($subject),@name($target, 2))?>
<?php else : ?>
<?=sprintf(@text('COM-ACTORS-TITLE-FOLLOW-ACTIVITY-NOTIFICATION'),@name($subject),@possessive($target))?>
<?php endif; ?>
</data>

<?php if ( $type == 'story') : ?>
<data name="body">
<div class="media-grid">
<?php 
	$targets = is_array($target) ? $target : array($target);	
	foreach($targets as $target) : ?>
	<div><?= @avatar($target) ?></div>	
<?php endforeach; ?>
</div>
</data>
<?php else : ?>
<?php
$commands->insert('follow', array('label'=>sprintf(@text('COM-ACTORS-VIEW-PROFILE'), $subject->name)))->href($subject->getURL())
?>
<data name="email_body">
	<?= $subject->followerCount ?>
	<span><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span>
	<?php if($subject->isLeadable()): ?>
	/ <?= $subject->leaderCount ?>
	<span><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
	<?php endif; ?>
	<?php if ($subject->isLeadable() && $target->isLeadable() ) : ?>
    	<?php 
    	    $common               = $subject->getCommonLeaders($target);
    	    $common_leaders_total = $common->getTotal();	
    	?>
    	<?php if ( $common_leaders_total > 0 ) : ?>
    	/ <?= $common_leaders_total ?>
    	<span><?= @text('COM-ACTORS-SOCIALGRAPH-COMMON') ?></span>
    	<?php $common->limit(44)->disableChain()->fetchSet();?>	    
    	    <div style="padding-top:10px">
    	    <?php foreach($common as $actor) : ?>
               <span><?=@avatar($actor, 20)?></span>
    	    <?php endforeach; ?>
    	    </div>
    	<?php endif;?>
	<?php endif;?>
</data>
<?php endif;?>