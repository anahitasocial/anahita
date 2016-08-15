<? defined('KOOWA') or die; ?>

<?= @map_api(array()) ?>

<div class="row">
    <div class="span8">
      <div class="well">
          <?= @map($location) ?>
      </div>

      <?= @template('_form') ?>
    </div>
</div>
