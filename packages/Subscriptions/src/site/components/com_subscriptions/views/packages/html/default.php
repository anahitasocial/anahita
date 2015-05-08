<?php defined('KOOWA') or die('Restricted access');?>

<div class="row">
	<div class="span6">
        <div class="an-entities">
        <?php foreach($packages as $package) : ?>
        	<div class="an-entity">
        		<h2 class="entity-title">
        			<?= @escape($package->name) ?>
        		</h2>   
	
        		<div class="entity-description">
        		    <dl>
                        <dt><?= @text('COM-SUB-BILLING-PERIOD') ?></dt>
                        <dd><?= ($package->recurring) ? @text('COM-SUB-BILLING-PERIOD-RECURRING-'.$package->billingPeriod) : @text('COM-SUB-BILLING-PERIOD-'.$package->billingPeriod) ?></dd>

            			<dt><?= @text('COM-SUB-PACKAGE-DURATION') ?>:</dt>
            			<dd><?= AnHelperDate::secondsTo('day', $package->duration)?> <?= @text('COM-SUB-PACKAGE-DAYS') ?></dd>
            
            			<dt><?= @text('COM-SUB-PACKAGE-PRICE') ?>:</dt> 
            			<dd><?= $package->price.' '.get_config_value('subscriptions.currency','US') ?></dd>
            		</dl>
        		</div>
        		
        		<div class="entity-description">
        		    <?= $package->description ?>
        		</div>
        		
        		<?php if ( $package->authorize('upgradepackage') ) : ?>
        		<div class=entity-actions>
        			<a href="<?=@route('view=signup&id='.$package->id)?>" class="btn btn-block btn-warning">
        				<?= @text('COM-SUB-PACKAGE-ACTION-UPGRADE-NOW') ?>
        			</a>
        		</div>
        		<?php elseif ( $package->authorize('subscribepackage') ) : ?>
        		<div class="entity-actions clearfix">
        			<a href="<?=@route('view=signup&id='.$package->id)?>" class="btn btn-block pull-right">
        				<?= @text('COM-SUB-PACKAGE-ACTION-SUBSCRIBE-NOW') ?>
        			</a>
        		</div>
        		<?php endif; ?>
        	</div>
        <?php endforeach;?>
        </div>
	</div>
</div>
