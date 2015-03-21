<?php defined('KOOWA') or die; ?>

<?php $document =& JFactory::getDocument(); ?>
<?php $path = JURI::root().'media/lib_anahita/js/production/' ?>

<?php $document->addScript(JURI::root().'templates/base/js/base.js'); ?>

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