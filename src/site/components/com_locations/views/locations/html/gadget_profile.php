<?php defined('KOOWA') or die; ?>

<div style="margin: 10px 0; border: 1px solid #fff;">
<?= @map($locations) ?>
</div>

<?php foreach($locations as $location) : ?>
<?= @template('list_gadget') ?>
<?php endforeach; ?>
