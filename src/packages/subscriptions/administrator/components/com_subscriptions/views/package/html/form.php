<?php defined('KOOWA') or die('Restricted access');?>

<form action="<?= @route($package->getURL())?>" method="post" class="-koowa-form" data-behavior="FormValidator" >
	<div class="col width-60">
	
		<fieldset class="adminform">
			<legend><?= JText::_( 'AN-SB-PACKAGE-DETAIL' ); ?></legend>
			
			<table class="admintable">
				<tr>
					<td width="100" align="right" class="key">
						<?= @text('AN-SB-PACKAGE-NAME') ?>
					</td>
					<td>
						<input type="text" data-validators="required" value="<?= @escape($package->name) ?>" size="30" name="name" />
					</td>
				</tr>
				
				<tr>
					<td width="100" align="right" class="key">
						<?= @text('AN-SB-PACKAGE-PRICE') ?>						
					</td>
					<td>						
						<input data-validators="required validate-numeric" type="text" value="<?=$package->price?>" size="10" name="price" />
						<?= get_config_value('subscriptions.currency','US') ?>
					</td>
				</tr>
				
				<tr>
					<td width="100" align="right" class="key" nowrap="nowrap">
						<?= @text('AN-SB-PACKAGE-BILLING-PERIOD') ?>
					</td>
					<td>						
						<select name="billingPeriod">
						<?php 
						$period_options = array(
							array(ComSubscriptionsDomainEntityPackage::BILLING_PERIOD_YEAR,		@text('AN-SB-PACKAGE-BILLING-PERIOD-YEAR')),
							array(ComSubscriptionsDomainEntityPackage::BILLING_PERIOD_MONTH,	@text('AN-SB-PACKAGE-BILLING-PERIOD-MONTH')),
							array(ComSubscriptionsDomainEntityPackage::BILLING_PERIOD_WEEK, 	@text('AN-SB-PACKAGE-BILLING-PERIOD-WEEK')),
							array(ComSubscriptionsDomainEntityPackage::BILLING_PERIOD_DAY, 		@text('AN-SB-PACKAGE-BILLING-PERIOD-DAY'))
						);
						?>
						<?= @helper('html.options', $period_options, $package->billing_period) ?>
						</select>				
					</td>
				</tr>
				
				<tr>
					<td width="100" align="right" class="key">
						<?= @text('AN-SB-PACKAGE-RECURRING') ?>						
					</td>
					<td>						
						<input <?= $package->recurring ? 'checked' : '' ?> type="checkbox" value="1" name="recurring" />
					</td>
				</tr>	
				
				<?php if ( $package->ordering > 1 ) : ?>
				<tr>
					<td width="100" align="right" class="key">
						<?= @text('AN-SB-PACKAGE-UPGRADE-DISCOUNT') ?>
					</td>
					<td>
						<input type="text" value="<?= $package->getUpgradeDiscount() * 100 ?>" size="10" name="upgrade_discount" />%
					</td>
				</tr>											
				<?php endif; ?>	
			</table>
			
		</fieldset>
		
		<fieldset class="adminform">
			<legend><?= JText::_( 'AN-SB-PACKAGE-DESCRIPTION' ); ?></legend>
			<?= JFactory::getEditor()->display( 'description',  htmlspecialchars($package->description, ENT_QUOTES), '500', '300', '60', '20', array('pagebreak', 'readmore') ); ?>	
		</fieldset>	
	
	</div>
	
	<div class="col width-40">
		<?php foreach($plugins as $plugin) : ?>
		<fieldset class="adminform">
			<legend><?=ucfirst($plugin->name)?></legend>
			<?= @helper('renderParams', $plugin, $package) ?>
		</fieldset>	
		<?php endforeach; ?>
	</div>
	
	<div class="clr"></div>		
</form>

<?= JHTML::_('behavior.keepalive') ?>