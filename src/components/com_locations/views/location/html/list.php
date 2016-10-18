<? defined('KOOWA') or die('Restricted access') ?>

<div class="an-entity">
    <h4 class="entity-title">
        <a href="<?= @route($location->getURL()) ?>" title="<?= $location->name ?>">
            <i class="icon-map-marker"></i>
            <?= $location->name ?>
        </a>
  	</h4>

    <div class="entity-meta">
        <?= @helper('address', $location) ?>
    </div>

    <div class="entity-actions">
  		  <?= @helper('ui.commands', @commands('list')) ?>
  	</div>
</div>
