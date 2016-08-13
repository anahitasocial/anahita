<? defined('KOOWA') or die; ?>

<div class="row">
	<div class="span8">
		<?= @template('revision') ?>
	</div>
	<div class="span4">
		<h4 class="block-title">
		<?= @text('COM-ARTICLES-META-ADDITIONAL-INFORMATION') ?>
		</h4>

		<div class="block-content">
    		<ul class="an-meta">
    			<li><span class="label label-info"><?= sprintf(@text('COM-ARTICLES-ARTICLE-REVISION-META-NUMBER'), $revision->revisionNum) ?></span></li>
    			<li><?= sprintf(@text('LIB-AN-ENTITY-AUTHOR'), @date($revision->creationTime, '%B %d %Y - %l:%M %p'), @name($revision->author)) ?></li>
    		</ul>
		</div>

		<?= @helper('ui.gadget', LibBaseTemplateObject::getInstance('revisions', array(
            'title' => @text('COM-ARTICLES-ARTICLE-REVISIONS'),
            'url' => 'view=revisions&layout=gadget&pid='.$revision->article->id.'&oid='.$actor->id,
        ))); ?>
	</div>
</div>
