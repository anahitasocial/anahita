<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="rok-quicklinks-customize mc-button">
	<span class="button">
		<a href="<?php echo rokQuickLinksHelper::getConfigureLink(); ?>">customize</a>
	</span>
</div>
<div id="rok-quicklinks">
	<ul>
	<?php foreach ($quicklinks as $ql) : ?>
		<?php if ($ql != null) : ?>
		<li>
			<a href="<?php echo $ql[1]; ?>">
				<span class="rok-quicklink-box">
					<img src="<?php echo rokQuickLinksHelper::getImagePathUrl($ql[0]); ?>" alt="<?php echo $ql[2]; ?>" /><br />
					<strong><?php echo $ql[2]; ?></strong>
				</span>
			</a>
		</li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
</div>
