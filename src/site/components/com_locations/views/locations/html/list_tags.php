<?php defined('KOOWA') or die; ?>

<?php foreach($locations as $location) : ?>
<li>
    <a href="<?= @route($location->getURL()) ?>">
      <?= @escape($location->name) ?>
    </a>
</li>
<?php endforeach; ?>
<?php $selector_url = 'option=com_locations&view=locations&layout=selector&locatable_id='.$locatable->id; ?>
<li>
    <a href="<?= @route($selector_url) ?>" data-toggle="LocationSelector">
      + <?= @text('LIB-AN-ACTION-ADD-LOCATION') ?>
    </a>
</li>
