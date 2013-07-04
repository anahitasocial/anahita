<?php defined('KOOWA') or die('Restricted access') ?>

<?php if ( $navigation->getPageTitle() ) : ?>
<h1 class="an-page-header"><?=$navigation->getPageTitle()?></h1>
	<?php if ( !$navigation->getTitle()) : ?>
		<?php if( count($navigation->getNavigationItems()) ) :?>
		<ul class="toolbar">
		<?php foreach($navigation->getNavigationItems() as $item) : ?>
			<li><?= $helper->addAction($item)?></li>
		<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	<?php endif?>
<?php endif;?>

<?php if ( $navigation->getTitle() ) : ?>
<div class="an-media-header">	
	<div class="clearfix">	
		<?php if ( $navigation->getThumbnail() ) : ?>
		<div class="avatar">
			<?php if (  $navigation->getThumbnailURL() ) : ?>
				<a href="<?=  $navigation->getThumbnailURL()?>">
					<img src="<?=$navigation->getThumbnail()?>" />
				</a>
			<?php else : ?>
				<img src="<?=$navigation->getThumbnail()?>" />
			<?php endif?>
		</div>
		<?php endif;?>
		
		<?php $class = ($navigation->getThumbnail()) ? 'has-thumbnail' : ''; ?>
		<div class="info <?= $class ?>">
			<?php if ( $navigation->getTitle()  ) : ?>
			<h2 class="title"><?= $navigation->getTitle() ?></h2>
			<?php endif; ?>
			
			<?php if( $navigation->getDescription() ) : ?>
			<div class="description"><?= $navigation->getDescription() ?></div>
			<?php endif; ?>
			
			<?php if( count($navigation->getNavigationItems()) ) :?>
			<ul class="toolbar">
			<?php foreach($navigation->getNavigationItems() as $item) : ?>
				<li><?= $helper->addAction($item)?></li>
			<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php endif;?>