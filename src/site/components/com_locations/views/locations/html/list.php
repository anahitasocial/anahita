<?php defined('KOOWA') or die; ?>

<?php foreach($locations as $location) : ?>
<div class="an-entity">
    <div class="entity-title">
        <a href="<?= @route($location->getURL()) ?>" title="<?= $location->name ?>">
            <?= $location->name ?>
        </a>
    </div>

    <div class="entity-meta">
        <?= @helper('address', $location) ?>
    </div>

    <?php if( false && $commands = @commands('list') && count($commands) > 0) : ?>
    <div class="entity-actions">
  		  <?= @helper('ui.commands', $commands) ?>
  	</div>
  <?php endif; ?>
</div>
<?php endforeach; ?>
