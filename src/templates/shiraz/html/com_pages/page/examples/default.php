<? defined('KOOWA') or die('Restricted access'); ?>

<? @title('Page Examples')?>
<? @description('The following are some examples of html pages that you can create for the com_pages.')?>

<div class="row">
	<div class="span8">

	<h1>Page Examples</h1>

	<p>The HTML component in anahita can be used for rendering custom php landing pages of all sorts while you get to use Anahita function calls and methods within this pages.</p>

	<p>
	The following are some examples of html pages that you can create for the com_pages
	</p>

	<ol>
		<li>
			<a href="<?= @route('option=com_pages&view=page&layout=examples/article') ?>">
				Simple Article
			</a>
		</li>

		<li>
			<a href="<?= @route('option=com_pages&view=page&layout=examples/landing') ?>">
				Landing Page
			</a>
		</li>

		<li>
			<a href="<?= @route('option=com_pages&view=page&layout=examples/actor_gadget') ?>">
				Actor Gadget
			</a>
		</li>

		<li>
			<a href="<?= @route('option=com_pages&view=page&layout=examples/nonavbar') ?>">
				No Navbar
			</a>
		</li>
	</ol>

	</div>
</div>
