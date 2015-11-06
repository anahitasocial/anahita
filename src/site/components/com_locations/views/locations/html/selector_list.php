<?php defined('KOOWA') or die; ?>

<?php foreach($locations as $location) : ?>
<div class="an-entity">
    <div class="entity-title">
        <a data-action="addLocation" data-location="<?= $location->id ?>" href="<?= @route($locatable->getURL()) ?>">
            <?= $location->name ?>
        </a>
    </div>
</div>
<?php endforeach; ?>
