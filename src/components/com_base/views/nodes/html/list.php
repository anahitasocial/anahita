<? defined('ANAHITA') or die('Restricted access');?>

<? $view = @view('node')->layout('list'); ?>
<? foreach ($items as $item): ?>
<?= $view->item($item) ?>
<? endforeach; ?>
