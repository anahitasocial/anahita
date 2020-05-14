<? defined('ANAHITA') or die; ?>

<? foreach($locations as $location) : ?>
<li>
    <a href="<?= @route($location->getURL()) ?>">
      <i class="icon-map-marker"></i>
      <?= @escape($location->name) ?>
    </a>
    <? if($taggable->authorize('edit')) : ?>
    - <a data-action="delete-location" href="<?= @route($taggable->getURL()) ?>" data-location="<?= $location->id ?>">
      <small><?= @text('LIB-AN-ACTION-REMOVE') ?></small>
    </a>
  <? endif; ?>
</li>
<? endforeach; ?>
