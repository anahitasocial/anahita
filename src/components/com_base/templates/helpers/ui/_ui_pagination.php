<? defined('KOOWA') or die('Restricted access') ?>

<? if (count($pages) > 1) : ?>
<div class="pagination" data-behavior="pagination" data-pagination-options="<?= htmlspecialchars(json_encode($options), ENT_QUOTES) ?>">
	<ul>
		<li class="prev <?= $paginator['offset'] == 0 ? 'disabled' : ''?>">
			<a href="<?= $prev_page ?>">
				<?= @text('LIB-AN-ACTION-PREVIOUS') ?>
			</a>
		</li>
		<? foreach ($pages as $page) : ?>
			<li class="<?= $page['current'] ? 'active' : ''?>">
				<a href="<?=$page['url']?>">
					<?= $page['number'] ?>
				</a>
			</li>
		<? endforeach; ?>
		<li class="next <?= ($paginator['total'] - $paginator['offset'] > $paginator['limit']) ? '' : 'disabled'?>">
			<a href="<?= $next_page ?>">
				<?= @text('LIB-AN-ACTION-NEXT') ?>
			</a>
		</li>
	</ul>

	<div class="an-meta">
		<?= sprintf(@text('LIB-AN-RECORDS-AVAILABLE'), number_format($total)) ?>
	</div>
</div>
<? endif; ?>
