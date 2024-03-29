<? defined('ANAHITA') or die('Restricted access');?>

<data name="title">
	<?= sprintf(@text('COM-ARTICLES-STORY-ARTICLE-ADD'), @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">	
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
	
    <div class="entity-body">
		<? if ($object->excerpt) : ?>
	    <?= @helper('text.truncate', @body($object->excerpt), array('length' => 200)); ?>
		<? else : ?>
	    <?= @helper('text.truncate', @body($object->body), array('length' => 200, 'read_more' => true, 'consider_html' => true)); ?>
		<? endif; ?>
	</div>
</data>
<? if ($type === 'notification') :?>
<? $commands->insert('view-post', array('label' => @text('COM-ARTICLES-ARTICLE-VIEW')))->href($object->getURL())?>
<? endif;?>
