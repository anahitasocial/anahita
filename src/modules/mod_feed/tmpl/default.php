<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div style="direction: <?php echo $rssrtl ? 'rtl' :'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right' :'left'; ?> ! important">
<?php
if( $feed != false )
{
	//image handling
	$iUrl 	= isset($feed->image->url)   ? $feed->image->url   : null;
	$iTitle = isset($feed->image->title) ? $feed->image->title : null;
	?>
	<div class="feed <?php echo $params->get('moduleclass_sfx'); ?>">
	<?php
	// feed description
	if (!is_null( $feed->title ) && $params->get('rsstitle', 1)) {
		?>
		<a href="<?php echo str_replace( '&', '&amp', $feed->link ); ?>" target="_blank">
			<h3 class="feed-title"><?php echo $feed->title; ?></h3>
		</a>
		<?php
	}

	// feed description
	if ($params->get('rssdesc', 1)) {
	?>
		<div class="feed-description">
			<?php echo $feed->description; ?>
		</div>
		<?php
	}

	// feed image
	if ($params->get('rssimage', 1) && $iUrl) {
	?>
		<div class="feed-image">
			<img src="<?php echo $iUrl; ?>" alt="<?php echo @$iTitle; ?>"/>
		</div>
	<?php
	}

	$actualItems = count( $feed->items );
	$setItems    = $params->get('rssitems', 5);

	if ($setItems > $actualItems) {
		$totalItems = $actualItems;
	} else {
		$totalItems = $setItems;
	}
	?>

		<ul class="newsfeed<?php echo $params->get( 'moduleclass_sfx'); ?>"  >
		<?php
		$words = $params->def('word_count', 0);
		for ($j = 0; $j < $totalItems; $j ++)
		{
			$currItem = & $feed->items[$j];
			// item title
			?>
			<li>
			<?php
			if ( !is_null( $currItem->get_link() ) ) {
			?>
				<a href="<?php echo $currItem->get_link(); ?>" target="_blank">
				<?php echo $currItem->get_title(); ?></a>
			<?php
			}

			// item description
			if ($params->get('rssitemdesc', 1))
			{
				// item description
				$text = $currItem->get_description();
				$text = str_replace('&apos;', "'", $text);

				// word limit check
				if ($words)
				{
					$texts = explode(' ', $text);
					$count = count($texts);
					if ($count > $words)
					{
						$text = '';
						for ($i = 0; $i < $words; $i ++) {
							$text .= ' '.$texts[$i];
						}
						$text .= '...';
					}
				}
				?>
				<div style="text-align: <?php echo $params->get('rssrtl', 0) ? 'right': 'left'; ?> ! important" class="newsfeed_item<?php echo $params->get( 'moduleclass_sfx'); ?>"  >
					<?php echo $text; ?>
				</div>
				<?php
			}
			?>
			</li>
			<?php
		}
		?>
		</ul>
	</div>
<?php } ?>
</div>
