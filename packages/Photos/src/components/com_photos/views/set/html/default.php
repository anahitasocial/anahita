<? defined('KOOWA') or die ?>

<? if ($set->authorize('edit')) : ?>

<? if (defined('ANDEBUG') && ANDEBUG) : ?>
<script src="com_photos/js/organizer.js" />
<? else: ?>
<script src="com_photos/js/min/organizer.min.js" />
<? endif; ?>

<? endif; ?>

<div class="row">
	<div class="span8">
	<?= @helper('ui.header') ?>
    <?= @template('set') ?>
    <?= @helper('ui.comments', $set) ?>
	</div>

	<div class="span4 visible-desktop">
    	<h4 class="block-title">
    	    <?= @text('LIB-AN-META') ?>
    	</h4>

    	<div class="block-content">
        	<ul class="an-meta">
        		<li><?= sprintf(@text('LIB-AN-ENTITY-AUTHOR'), @date($set->creationTime), @name($set->author)) ?></li>
        		<li><?= sprintf(@text('LIB-AN-ENTITY-EDITOR'), @date($set->updateTime), @name($set->editor)) ?></li>
        		<li><?= sprintf(@text('COM-PHOTOS-SET-META-PHOTOS'), $set->getPhotoCount()) ?></li>
        		<li><?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $set->numOfComments) ?></li>
        	</ul>
    	</div>

			<? if(count($set->locations) || $set->authorize('edit')): ?>
			<h4 class="block-title">
				<?= @text('LIB-AN-ENTITY-LOCATIONS') ?>
			</h4>

			<div class="block-content">
			<?= @location($set) ?>
			</div>
			<? endif; ?>
	</div>
</div>
