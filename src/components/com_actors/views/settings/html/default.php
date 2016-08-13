<? defined('KOOWA') or die('Restricted access');?>

<div class="row">

	<div class="span4">
		<ul id="setting-tabs" class="nav nav-pills nav-stacked" >
			<li class="nav-header">
		          <?= @text('COM-ACTORS-PROFILE-EDIT') ?>
		  </li>
		<? foreach ($tabs as $tab) : ?>
			<li class="<?= $tab->active ? 'active' : ''?>">
				<a href="<?=@route($tab->url)?>">
		            <?= $tab->label ?>
		        </a>
			</li>
		<? endforeach;?>
		</ul>
	</div>

	<div class="span8">
        <?= @helper('ui.header') ?>
        <div class="actor-settings">
            <?= $content ?>
        </div>
    </div>
</div>
