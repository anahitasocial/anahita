<? defined('KOOWA') or die; ?>

<? foreach($locations as $location) : ?>
<div class="an-entity">
    <div class="entity-title">
        <a data-action="add-location" data-location="<?= $location->id ?>" href="<?= @route($locatable->getURL()) ?>">
            <i class="icon-map-marker"></i>
            <?= $location->name ?>
        </a>
    </div>

    <div class="entity-meta">
        <?= @helper('address', $location) ?>
    </div>
</div>
<? endforeach; ?>
