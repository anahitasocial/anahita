<? defined('KOOWA') or die('Restricted access');?>

<? foreach ($photos as $photo) : ?>
<?= @view('photo')->layout('masonry')->photo($photo)->filter($filter) ?>
<? endforeach; ?>
