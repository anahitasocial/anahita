<? defined('KOOWA') or die; ?>

<? if ($sort == $field): ?>
<u><?= @text($label) ?></u>
<? else: ?>
<a href="<?= @route('sort='.$field) ?>">
  <?= @text($label) ?>
</a>
<? endif; ?>
