<?php defined('KOOWA') or die('Restricted access') ?>

<?php if ( count($pages) > 1 ) : ?>
<div class="pagination" data-behavior="Pagination" data-pagination-options="<?= htmlspecialchars(json_encode($options), ENT_QUOTES, 'UTF-8') ?>">
	<ul>	    	
		<li class="prev <?= $paginator['offset'] == 0 ? 'disabled' : ''?>">
			<a href="<?= $prev_page ?>">
				<?= @text('PREV') ?>
			</a>
		</li>			
		<?php foreach($pages as $page) : ?>
			<li class="<?= $page['current'] ? 'active' : ''?>">			
				<a href="<?=$page['url']?>">
					<?= $page['number'] ?>
				</a>			
			</li>				
		<?php endforeach; ?>
		<li class="next <?= ($paginator['total'] - $paginator['offset'] > $paginator['limit']) ? '' : 'disabled'?>">
			<a href="<?= $next_page ?>">
				<?= @text('NEXT') ?>
			</a>
		</li>						
	</ul>
	
	<div class="an-meta">
		<?= sprintf(@text('LIB-AN-RECORDS-AVAILABLE'), number_format($total)) ?>
	</div>
</div>
<?php endif; ?>