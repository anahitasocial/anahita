<?php defined('KOOWA') or die('Restricted access'); ?>

<?php 
$menustyle = isset($menustyle) ? $menustyle : '';
$data_behaviour = ($menustyle == '') ? 'data-behavior="BS.Dropdown"' : ''; 
?>

<ul class="nav <?= $menustyle ?>" <?= $data_behaviour ?>>
<?= @template('list') ?>
</ul>
