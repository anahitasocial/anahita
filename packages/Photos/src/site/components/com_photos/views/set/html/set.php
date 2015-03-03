<?php defined('KOOWA') or die ?>

<?php @commands('toolbar') ?>

<?php if( $set->authorize('edit') ) : ?>
<div id="photo-selector"></div>
<?php endif; ?>

<?= @template('photos') ?>