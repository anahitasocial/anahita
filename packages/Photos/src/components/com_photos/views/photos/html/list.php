<? defined('KOOWA') or die('Restricted access');?>

<? foreach ($photos as $photo) : ?>
<?= @view('photo')->layout('list')->photo($photo)->filter($filter) ?>
<? endforeach; ?>
