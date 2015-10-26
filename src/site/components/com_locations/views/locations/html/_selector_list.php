<?php defined('KOOWA') or die; ?>

<?php foreach($locations as $locatin) : ?>
<div class="an-entity">
    <div class="entity-title">
        <a data-action="add-location" data-geolocatable-id="<?= $locatable->id ?>" href="<?= $location->getURL() ?>">
            <?= $location->name ?>
        </a>
    </div>
</div>
<?php endforeach; ?>
