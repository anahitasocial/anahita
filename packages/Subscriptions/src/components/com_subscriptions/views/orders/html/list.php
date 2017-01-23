<? defined('KOOWA') or die('Restricted access'); ?>

<? foreach ($orders as $order): ?>
<?= @view('order')->layout('list')->order($order) ?>
<? endforeach; ?>
