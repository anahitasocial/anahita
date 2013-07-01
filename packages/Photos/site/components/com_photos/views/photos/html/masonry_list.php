<?php defined('KOOWA') or die('Restricted access');?>	
	
<?php foreach( $photos as $photo) : ?>
<?= @view('photo')->layout('masonry')->photo($photo)->filter($filter) ?>
<?php endforeach; ?>