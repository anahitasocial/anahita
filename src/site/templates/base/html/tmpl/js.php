<?php defined('KOOWA') or die; ?>

<?php $document =& JFactory::getDocument(); ?>
<?php $path = JURI::root(true).DS.'media'.DS.'lib_anahita'.DS.'js'.DS.'production'.DS; ?>

<?php if(defined('JDEBUG') && JDEBUG ) : ?>

<?php 
@helper('javascript.combine', array(
    'file'   => JPATH_ROOT.'/media/lib_anahita/js/site.js',
  	'output' => JPATH_ROOT.'/media/lib_anahita/js/production/site.uncompressed.js'
)); 

$document->addScript($path.'site.uncompressed.js');    		
?>
<?php else : ?>
<?php $document->addScript($path.'site.js'); ?>
<?php endif; ?>