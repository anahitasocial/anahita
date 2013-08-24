<?php defined('KOOWA') or die('Restricted access'); ?>

<?php @title('Landing Page')?>
<?php @description('An example of a landing page.') ?>

<div class="hero-unit">
	<h1>Landing Page</h1>
	<p>An example of a landing page.</p>
</div>

<div class="row">
	<div class="span8">
		<blockquote class="pull-right">
		<p>"Lorem ipsum dolor sit amet, an quidam patrioque his. Iudico eleifend rationibus at quo. Dolorem euripidis sed ut."</p>
		<small>Ei quando platonem ullamcorper mel</small>
		</blockquote>
	</div>
	
	<div class="span4">
	<?php $actors = KService::get('repos:actors.actor')->getQuery()->disableChain()->limit(2)->fetchSet(); ?>
	<ul class="thumbnails">
	<?php foreach($actors as $actor): ?>
	<li><?= @avatar($actor) ?></li>
	<?php endforeach; ?>
	</ul>
	</div>
</div>

<div class="row">
	<div class="span4">
		<h3>Features 1</h3>
		<p>Lorem ipsum dolor sit amet, an quidam patrioque his. Iudico eleifend rationibus at quo. Dolorem euripidis sed ut. Commodo ponderum percipitur ne vis, eos sale scripta atomorum ne. Quem petentium an cum, nec et verear mentitum rationibus. Ei quando platonem ullamcorper mel, liber pertinax pro et.</p>
	</div>
	
	<div class="span4">
		<h3>Features 2</h3>
		<p>Lorem ipsum dolor sit amet, an quidam patrioque his. Iudico eleifend rationibus at quo. Dolorem euripidis sed ut. Commodo ponderum percipitur ne vis, eos sale scripta atomorum ne. Quem petentium an cum, nec et verear mentitum rationibus. Ei quando platonem ullamcorper mel, liber pertinax pro et.</p>
	</div>
	
	<div class="span4">
		<h3>Features 3</h3>
		<p>Lorem ipsum dolor sit amet, an quidam patrioque his. Iudico eleifend rationibus at quo. Dolorem euripidis sed ut. Commodo ponderum percipitur ne vis, eos sale scripta atomorum ne. Quem petentium an cum, nec et verear mentitum rationibus. Ei quando platonem ullamcorper mel, liber pertinax pro et.</p>
	</div>
</div>

<div class="row">
	<div class="span4">
		<h3>Benefit 1</h3>
		<p>Lorem ipsum dolor sit amet, an quidam patrioque his. Iudico eleifend rationibus at quo. Dolorem euripidis sed ut. Commodo ponderum percipitur ne vis, eos sale scripta atomorum ne. Quem petentium an cum, nec et verear mentitum rationibus. Ei quando platonem ullamcorper mel, liber pertinax pro et.</p>
	</div>
	
	<div class="span4">
		<h3>Benefit 1</h3>
		<p>Lorem ipsum dolor sit amet, an quidam patrioque his. Iudico eleifend rationibus at quo. Dolorem euripidis sed ut. Commodo ponderum percipitur ne vis, eos sale scripta atomorum ne. Quem petentium an cum, nec et verear mentitum rationibus. Ei quando platonem ullamcorper mel, liber pertinax pro et.</p>
	</div>
	
	<div class="span4">
		<h3>Benefit 1</h3>
		<p>Lorem ipsum dolor sit amet, an quidam patrioque his. Iudico eleifend rationibus at quo. Dolorem euripidis sed ut. Commodo ponderum percipitur ne vis, eos sale scripta atomorum ne. Quem petentium an cum, nec et verear mentitum rationibus. Ei quando platonem ullamcorper mel, liber pertinax pro et.</p>
	</div>
</div>