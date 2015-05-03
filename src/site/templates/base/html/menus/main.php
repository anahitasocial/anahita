<?php defined('KOOWA') or die;?>

<?php 

//Override this layout in your custom template 

?>

<?php 

// remove this if statement for the menu to render

if(false): ?>
<ul class="nav">
	<li>
		<a href="<?= @route('option=com_html&view=content&layout=examples/article') ?>">
		<?= @text('Article') ?>
		</a>
	</li>			
	<li>
		<a href="<?= @route('option=com_html&view=content&layout=examples/actor-gadget') ?>">
		<?= @text('Actor Gadget') ?>
		</a>
	</li>
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		<?= @text('About') ?> <b class="caret"></b>				
		</a>
		<ul class="dropdown-menu">							
			<li>
				<a href="<?= @route('option=com_html&view=content&layout=examples/landing') ?>">
				<?= @text('Landing') ?>
				</a>
			</li>						
			<li>
				<a href="<?= @route('option=com_html&view=content&layout=examples/nonavbar') ?>">
				<?= @text('No Navbar') ?>
				</a>
			</li>						
		</ul>			
	</li>
	<li>
		<a href="http://www.example.com" target="_blank">
		<?= @text('External Site') ?>
		</a>
	</li>	
		
</ul>
<?php endif; ?>