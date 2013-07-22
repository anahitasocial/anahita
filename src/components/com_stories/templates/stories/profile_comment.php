<?php defined('KOOWA') or die('Restricted access');?>
<?php if ($is_notification) : ?>
<data name="title">
<?=sprintf(@$name == 'story_add' ? @text('COM-STORIES-TITLE-COMMENT-ON-STATUS') :  @text('COM-STORIES-TITLE-COMMENT-ON-PUBLIC-MESSAGE')
	,@name($subject),@possessive($target), @$story_url)?>
</data>

<data name="body">
<?= @$body ?>
</data>
<?php endif; ?>
