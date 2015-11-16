<?php defined('KOOWA') or die; ?>

<?php foreach($locations as $location) : ?>
<div class="an-entity">
    <div class="entity-title">
        <a data-action="addLocation" data-location="<?= $location->id ?>" href="<?= @route($locatable->getURL()) ?>">
            <?= $location->name ?>
        </a>
    </div>

    <div class="entity-meta">
        <?= @helper('address', $location) ?>
    </div>
</div>
<?php endforeach; ?>

<?php if(count($locations) == 0) : ?>
<div class="an-entity">
    <a class="btn" href="#" data-action="location-form-show">
      <?= @text('COM-LOCATIONS-ACTION-ADD') ?>
    </a>
</div>
<?php endif; ?>
