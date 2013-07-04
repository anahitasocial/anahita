<?php
$tabs
    ->sort(array('profile','account','avatar')) //puts profile account avatar at begingin
    ->sort(array('delete'), false) //puts delete at the end
 ;
 
?>
<?= @previous() ?>