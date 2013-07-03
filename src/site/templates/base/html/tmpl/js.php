<?php if(defined('JDEBUG') && JDEBUG ) : ?>
	<?php @helper('javascript.combine', array(
  				'file'   => JPATH_ROOT.'/media/lib_anahita/js/site.js',
  				'output' => JPATH_ROOT.'/media/lib_anahita/js/production/site.uncompressed.js'
  			)) ?>
<script src="media://lib_anahita/js/production/site.uncompressed.js"></script>
<?php else : ?>
<script src="media://lib_anahita/js/production/site.js"></script>
<?php endif; ?>