<?php defined('KOOWA') or die; ?>

<div class="row">
    <div class="span8">
      <div class="well">
          <?= @map($location) ?>
      </div>
      
      <?= @template('form') ?>
    </div>
</div>
