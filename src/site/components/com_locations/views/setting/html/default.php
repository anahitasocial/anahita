<?php defined('KOOWA') or die; ?>

<h3><?= @text('COM-LOCATIONS-PROFILE-SETTING-TITLE') ?></h3>

<div class="btn-toolbar">
    <a href="<?= @route('option=com_locations&view=locations&layout=selector&locatable_id='.$actor->id) ?>" class="btn btn-primary" data-toggle="LocationSelector">
        <?= @text('COM-LOCATIONS-TOOLBAR-LOCATION-NEW') ?>
    </a>
</div>

<?php $url = 'option=com_locations&view=locations&layout=list&locatable_id='.$actor->id ?>
<div class="an-entities" data-trigger="InfiniteScroll" data-url="<?= @route($url) ?>"></div>
