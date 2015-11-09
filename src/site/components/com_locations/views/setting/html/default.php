<?php defined('KOOWA') or die; ?>

<h3><?= @text('COM-LOCATIONS-PROFILE-SETTING-TITLE') ?></h3>

<div class="btn-toolbar">
    <a href="<?= @route('option=com_locations&view=locations&layout=selector&locatable_id='.$actor->id) ?>" class="btn btn-primary" data-toggle="LocationSelector">
        <?= @text('COM-LOCATIONS-TOOLBAR-LOCATION-NEW') ?>
    </a>
</div>

<div class="an-entities setting-locations" data-url="<?= @route('option=com_locations&view=locations&layout=list&locatable_id='.$actor->id) ?>">
    <?= @view('locations')->layout('list')->locatable($actor)->locations($actor->locations) ?>
</div>
