<?php defined('KOOWA') or die('Restricted access');?>

<div class="row">
	<div class="span2">
		<ul id="setting-tabs" class="nav nav-pills nav-stacked" >
			<li class="nav-header">
		          <?= @text('COM-ACTORS-PROFILE-EDIT') ?>
		    </li>    
		<?php foreach($tabs as $tab) : ?>
			<li class="<?= $tab->active ? 'active' : ''?>">        
				<a href="<?=@route($tab->url)?>">            
		            <?= $tab->label ?>
		        </a>
			</li>
		<?php endforeach;?>
		</ul>
	</div>
	
	<div class="span6">
		<?= @helper('ui.header', array()) ?>
	
		<div class="actor-settings">
			<?= $content ?>
		</div>
	</div>
</div>