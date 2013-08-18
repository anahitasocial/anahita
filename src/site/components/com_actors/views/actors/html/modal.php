<popup:header>&nbsp;</popup:header>
<div class="an-entities" id="an-entities-main">
	<div id="an-actors" class="an-entities">
		<?php foreach($items as $item ) : ?>
            <div class="an-entity an-record dropdown-actions <?= $highlight ?>" data-behavior="BS.Dropdown">
            	<div class="clearfix">
            		<div class="entity-portrait-square">
            			<?= @avatar($item) ?>
            		</div>
            		
            		<div class="entity-container">
            			<h3 class="entity-name">
            				<?= @name($item) ?>
            			</h3>
            	
            			<div class="entity-meta">
            			<?= $item->followerCount ?>
            			<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span> 
            			
            			<?php if($item->isLeadable()): ?>
            			/ <?= $item->leaderCount ?>
            			<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
            			<?php endif; ?>
            			</div>
            		</div>
            	</div>
            </div>			
		<?php endforeach; ?>
	</div>
</div>