<?php defined('KOOWA') or die ?>

<?php @commands('toolbar') ?>

<?php if( $set->authorize('edit') ) : ?>
<div id="photo-selector"></div>
<?php endif; ?>


<div id="set-photos" class="an-entities" data-url="<?= @route($set->getURL()) ?>">
	<div class="media-grid">
    <?= @template('photos') ?>
    </div>
</div>