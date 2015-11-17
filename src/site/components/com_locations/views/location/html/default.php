<?php defined('KOOWA') or die('Restricted access') ?>

<div class="row">
  <div class="span12">
  <?= @helper('ui.header', array()) ?>
  </div>
</div>

<div class="an-entity">
    <h2 class="entity-title">
  		<?= @escape($location->name) ?>
  	</h2>

    <div class="entity-meta">
        <?= @helper('address', $location) ?>
    </div>
</div>
