<? defined('KOOWA') or die ?>

<?
$subject = is_array($subject) ? array_shift($subject) : $subject;

if (!is_array($item->target) && !$item->target->eql($item->subject)) {
    $target_to_show = $item->target;
} else {
    $target_to_show = null;
}
?>

<div class="an-story an-entity">
	<div class="clearfix">
	    <div class="entity-portrait-square">
	        <?= @avatar($subject) ?>
	    </div>

    	<div class="entity-container">
    		<? if (!empty($title)): ?>
    		<h4 class="story-title">
    			<?= $title ?>
    		</h4>
    		<? else: ?>
    		<h4 class="author-name">
    			<?= @name($subject) ?>
    		</h4>
    		<? endif; ?>

    		<ul class="an-meta inline">
    		    <li><?= @date($timestamp) ?></li>
    			<? if ($target_to_show): ?>
				<li>
					<a href="<?= @route($target_to_show->getURL()) ?>">
					    <?= @name($target_to_show) ?>
					</a>
				</li>
				<? endif; ?>
    		</ul>
    	</div>
    </div>

    <? if (!empty($body)) : ?>
    <div class="story-body">
    	<?= $body ?>
    </div>
    <? endif; ?>

    <?
    $votable_item = null;

    if (!$item->aggregated() && $item->object && $item->object->isVotable()) {
        $votable_item = $item->object;
    }
    ?>

    <? if ($votable_item): ?>
	<div class="vote-count-wrapper entity-meta" id="vote-count-wrapper-<?= $votable_item->id ?>">
        <?= @helper('ui.voters', $votable_item); ?>
	</div>
    <? endif; ?>

    <div class="entity-actions">
    	<? $can_comment = $commands->offsetExists('comment'); ?>
        <?= @helper('ui.commands', $commands)?>
    </div>

	<? if (!empty($comments) || $can_comment) : ?>
    <?= @helper('ui.comments', $item->object, array('comments' => $comments, 'can_comment' => $can_comment, 'content_filter_exclude' => array('gist'), 'pagination' => false, 'show_guest_prompt' => false, 'truncate_body' => array('length' => 220, 'consider_html' => true, 'read_more' => true))) ?>
    <? endif;?>

    <? if (!empty($comments) && $can_comment): ?>
    <div class="comment-overtext-box">
    	<a class="action-comment-overtext" storyid="<?= $item->id ?>" href="<?= @route($item->object->getURL()) ?>">
        	<?= @text('COM-STORIES-ADD-A-COMMENT') ?>
        </a>
    </div>
    <? endif; ?>
</div>
