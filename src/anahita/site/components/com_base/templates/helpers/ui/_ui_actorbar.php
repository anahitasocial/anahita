<?php if ( $actorbar->getTitle() && $actorbar->getActor() ) : ?>
<div class="an-media-header">	
	<div class="clearfix">			
		<div class="avatar">
			<?= @avatar($actorbar->getActor())?>
		</div>
		
		<div class="info">			
			<h2 class="title"><?= $actorbar->getTitle() ?></h2>			
			<?php if( $actorbar->getDescription() ) : ?>
			<div class="description"><?= $actorbar->getDescription() ?></div>
			<?php endif; ?>
		</div>
	</div>
	
	<ul class="toolbar">
	<?php foreach($actorbar->getCommands() as $command) : ?>
		<li><?= @helper('ui.command', $command) ?></li>
	<?php endforeach; ?>
		<li class="profile">
			<a href="<?=@route($actorbar->getActor()->getURL())?>">
			<?= @text('COM-ACTORS-BACK-TO-PROFILE') ?>
			</a>
		</li>
	</ul>	
</div>
<?php endif;?>