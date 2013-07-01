<?php defined('KOOWA') or die ?>

<?php @commands('toolbar') ?>

<?php if( $set->authorize('edit') ) : ?>
<div id="medium-selector" oid="<?= $set->owner->id ?>" set_id="<?= $set->id ?>" ></div>
<?php endif; ?>

<div id="set-mediums-wrapper">
<?= @template('photos') ?>
</div>