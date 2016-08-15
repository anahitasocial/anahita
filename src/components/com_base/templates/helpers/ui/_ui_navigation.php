<? defined('KOOWA') or die('Restricted access') ?>

<? if ($navigation->getPageTitle()) : ?>
<h1 class="an-page-header"><?=$navigation->getPageTitle()?></h1>
	<? if (!$navigation->getTitle()) : ?>
		<? if (count($navigation->getNavigationItems())) :?>
		<ul class="toolbar">
		<? foreach ($navigation->getNavigationItems() as $item) : ?>
			<li><?= $helper->addAction($item)?></li>
		<? endforeach; ?>
		</ul>
		<? endif; ?>
	<? endif?>
<? endif;?>

<? if ($navigation->getTitle()) : ?>
<div class="an-media-header">
	<div class="clearfix">
		<? if ($navigation->getThumbnail()) : ?>
		<div class="avatar">
			<? if ($navigation->getThumbnailURL()) : ?>
				<a href="<?=  $navigation->getThumbnailURL()?>">
					<img src="<?=$navigation->getThumbnail()?>" />
				</a>
			<? else : ?>
				<img src="<?=$navigation->getThumbnail()?>" />
			<? endif?>
		</div>
		<? endif;?>

		<? $class = ($navigation->getThumbnail()) ? 'has-thumbnail' : ''; ?>
		<div class="info <?= $class ?>">
			<? if ($navigation->getTitle()) : ?>
			<h2 class="title"><?= $navigation->getTitle() ?></h2>
			<? endif; ?>

			<? if ($navigation->getDescription()) : ?>
			<div class="description"><?= $navigation->getDescription() ?></div>
			<? endif; ?>

			<? if (count($navigation->getNavigationItems())) :?>
			<ul class="toolbar inline">
			<? foreach ($navigation->getNavigationItems() as $item) : ?>
				<li><?= $helper->addAction($item)?></li>
			<? endforeach; ?>
			</ul>
			<? endif; ?>
		</div>
	</div>
</div>
<? endif;?>
