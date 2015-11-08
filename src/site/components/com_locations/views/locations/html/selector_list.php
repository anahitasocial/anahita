<?php defined('KOOWA') or die; ?>

<?php foreach($locations as $location) : ?>
<div class="an-entity">
    <div class="entity-title">
        <a data-action="addLocation" data-location="<?= $location->id ?>" href="<?= @route($locatable->getURL()) ?>">
            <?= $location->name ?>
        </a>
    </div>

    <div class="entity-meta">
    <?php //@todo create a helper for outputting an address ?>
    <?= @escape($location->address) ?>, <?= $location->city ?>, <?= $location->state_province ?>, <?= $location->country ?>
    </div>
</div>
<?php endforeach; ?>
