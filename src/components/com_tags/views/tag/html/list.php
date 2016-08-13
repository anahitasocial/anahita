<? defined('KOOWA') or die; ?>

<div class="an-entity">
    <h3 class="entity-title">
        <a><?= @escape($item->title) ?></a>
    </h3>

    <? if ($item->description): ?>
  	<div class="entity-description">
  	<?= @helper('text.truncate', @content($item->description), array('length' => 500, 'consider_html' => true, 'read_more' => true)); ?>
  	</div>
  	<? endif; ?>
</div>
