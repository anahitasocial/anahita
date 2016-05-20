<? defined('KOOWA') or die; ?>

<? if ($sort == $field): ?>
<u><?= @text($label) ?></u>
<? else: ?>
<a href="<?= @route($url) ?>">
  <?= @text($label) ?>
</a>
<? endif; ?>
