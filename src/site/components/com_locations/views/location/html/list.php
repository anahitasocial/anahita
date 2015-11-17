<?php defined('KOOWA') or die('Restricted access') ?>

<div class="an-entity">
    <h3 class="entity-title">
        <a href="<?= @route($location->getURL()) ?>" title="<?= $location->name ?>">
            <?= $location->name ?>
        </a>
  	</h3>

    <div class="entity-meta">
        <?= @helper('address', $location) ?>
    </div>

    <div class="entity-actions">
  		<?= @helper('ui.commands', @commands('list')) ?>
  	</div>
</div>
