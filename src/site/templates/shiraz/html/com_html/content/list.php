<?php defined('KOOWA') or die('Restricted access'); ?>

<?php @title('Page Examples')?>
<?php @description('The following are some examples of html pages that you can create for the com_html.')?>


<h1>Page Examples</h1>

<p>
The following are some examples of html pages that you can create for the com_html
</p>

<ol>
	<li>
		<a href="<?= @route('option=com_html&view=content&layout=examples/article') ?>">
			Simple Article
		</a>
	</li>
	
	<li>
		<a href="<?= @route('option=com_html&view=content&layout=examples/landing') ?>">
			Landing Page
		</a>
	</li>
	
	<li>
		<a href="<?= @route('option=com_html&view=content&layout=examples/actor_gadget') ?>">
			Actor Gadget
		</a>
	</li>
	
	<li>
		<a href="<?= @route('option=com_html&view=content&layout=examples/nonavbar') ?>">
			No Navbar
		</a>
	</li>
</ol>


