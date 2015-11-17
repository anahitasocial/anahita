<?php defined('KOOWA') or die('Restricted access') ?>

<div class="an-entity">
    <h4 class="entity-title">
        <a href="<?= @route($location->getURL()) ?>" title="<?= $location->name ?>">
            <?= $location->name ?>
        </a>
  	</h4>

    <div class="entity-meta">
        <?= @helper('address', $location) ?>
    </div>

    <?php if($location->description) : ?>
    <div class="entity-description">
  		<?= @helper('text.truncate', @content($location->description, array('exclude' => array('syntax', 'video'))), array('length' => 200, 'consider_html' => true)); ?>
  	</div>
    <?php endif; ?>

    <div class="entity-actions">
  		  <?= @helper('ui.commands', @commands('list')) ?>
  	</div>
</div>
