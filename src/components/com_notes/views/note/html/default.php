<? defined('KOOWA') or die ?>

<? @title(sprintf(@text('COM-NOTES-META-TITLE-NOTE'), $actor->name).' - '.@date($note->creationTime)) ?>
<? @description(@helper('text.truncate', strip_tags($note->body), array('length' => 156))) ?>

<div class="row">
	<div class="span8">
	<?= @helper('ui.header') ?>
	<?= @template('note') ?>
	<?= @helper('ui.comments', $note) ?>
	</div>

	<div class="span4">
			<? if(count($note->locations) || $note->authorize('edit')): ?>
			<h4 class="block-title">
				<?= @text('LIB-AN-ENTITY-LOCATIONS') ?>
			</h4>

			<div class="block-content">
			<?= @location($note) ?>
			</div>
			<? endif; ?>
	</div>
</div>
