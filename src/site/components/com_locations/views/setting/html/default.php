<?php defined('KOOWA') or die; ?>

<h3><?= @text('COM-LOCATIONS-PROFILE-SETTING-TITLE') ?></h3>

<div class="btn-toolbar">
    <a href="<?= @route('option=com_locations&view=location&layout=selector&locatable_id='.$actor->id) ?>" class="btn btn-primary" data-toggle="LocationSelector">
        <?= @text('COM-LOCATIONS-TOOLBAR-LOCATION-NEW') ?>
    </a>
</div>

<div class="an-entities">
    <?php for($i=0; $i<3; $i++) : ?>
    <div class="an-entity">
        <h4 class="entity-title">Location Title</h4>
        <div class="entity-description">
        Location Address goes here
        </div>
        <div class="entity-actions">
            <ul class="an-actions">
                <li>
                    <a href="#">Remove</a>
                </li>
            </ul>
        </div>
    </div>
   <?php endfor; ?>
</div>
