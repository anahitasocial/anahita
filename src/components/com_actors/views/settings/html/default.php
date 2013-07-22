<?php defined('KOOWA') or die('Restricted access');?>


<module position="sidebar-b" style="simple">
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
</module>

<div class="actor-settings">
<?= $content ?>
</div>