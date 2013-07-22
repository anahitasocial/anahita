<?php defined('KOOWA') or die ?>

<?php if(count($sets)): ?>
<div data-behavior="InfinitScroll" data-infinitscroll-options="{'numColumns':3, 'url':'<?= @route('layout=list') ?>'}" class="an-entities masonry">
<?= @template('list') ?>	
</div>
<?php else: ?>
<?= @message(@text('COM-PHOTOS-SETS-NO-SETS-CREATED')) ?>
<?php endif; ?>