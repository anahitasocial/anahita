<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php if ($this->showMessage) : ?>
<?php echo $this->loadTemplate('message'); ?>
<?php endif; ?>
<?php echo $this->loadTemplate('form'); ?>
