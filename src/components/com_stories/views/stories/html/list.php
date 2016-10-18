<? defined('KOOWA') or die('Restricted access') ?>

<?
$view = @view('story')->layout('list');

if (isset($actor)) {
    $view->actor($actor);
}
?>

<? foreach ($stories as $story) : ?>
<?= $view->item($story) ?>
<? endforeach; ?>
