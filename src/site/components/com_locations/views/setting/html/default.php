<?php defined('KOOWA') or die; ?>

<h3><?= @text('COM-LOCATIONS-PROFILE-SETTING-TITLE') ?></h3>

<div class="btn-toolbar">
    <a href="<?= @route('option=com_locations&view=locations&layout=selector&locatable_id='.$actor->id) ?>" class="btn btn-primary" data-toggle="LocationSelector">
        <?= @text('COM-LOCATIONS-TOOLBAR-LOCATION-NEW') ?>
    </a>
</div>

<div class="an-entities">
    <?php $locations = $actor->locations ?>
    <?php foreach($locations as $location) : ?>
    <div class="an-entity">
        <h4 class="entity-title"><?= @escape($location->name) ?></h4>

        <?php if($location->description): ?>
        <div class="entity-description">
        <?= @escape($location->description) ?>
        </div>
        <?php endif; ?>

        <div class="entity-meta">
            <?= @helper('address', $location) ?>
        </div>

        <div class="entity-actions">
            <ul class="an-actions">
                <li>
                    <a href="<?= @route($actor->getURL()) ?>" data-action="deleteLocation" data-location="<?= $location->id ?>">
                      <?= @text('LIB-AN-ACTION-DELETE') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
  <?php endforeach; ?>
</div>
