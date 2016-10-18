<? defined('KOOWA') or die('Restricted access') ?>

<? foreach ($sets as $set) : ?>
<?= @view('set')->layout('list')->set('set', $set) ?>
<? endforeach; ?>
