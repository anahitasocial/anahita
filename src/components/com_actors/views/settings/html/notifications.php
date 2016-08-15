<? defined('KOOWA') or die; ?>
<form>
<? foreach ($apps as $app) : ?>
<?= @helper('ui.form', array(
    $app->getName() => @html('select', 'd', array('options' => array('YES', 'NO'))),
))?>
<? endforeach;?>
</form>
