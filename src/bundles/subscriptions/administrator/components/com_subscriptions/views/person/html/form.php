<?php defined('KOOWA') or die('Restricted access');?>

<?php JHTML::_('behavior.calendar');?>

<div class="col width-50">
	<form action="<?= @route('&id='.$person->id)?>" method="post" class="-koowa-form" >
	<fieldset class="adminform">
		<legend><?= JText::_( 'AN-SB-PERSON-DETAIL' ); ?></legend>
		
		<table class="admintable">
			<tr>
				<td width="100" align="right" class="key">
					<?= @text('AN-SB-PERSON-ID') ?>
				</td>
				<td>
					<?= $person->id ?>
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<?= @text('AN-SB-PERSON-NAME') ?>
				</td>
				<td>
					<?= $person->name ?>
				</td>
			</tr>
			<tr>
				<td width="100" align="right" class="key">
					<?= @text('AN-SB-PERSON-USERNAME') ?>
				</td>
				<td>
					<?= $person->username ?>
				</td>
			</tr>							
		</table>
	</fieldset>
	</form>
</div>

<div class="col width-50">
	<fieldset class="adminform">
		<legend><?= JText::_( 'AN-SB-PERSON-SUBSCRIPTIONS' ); ?></legend>
		<table class="admintable">
			<?php foreach($packages as $package) : ?>

			<tr>
				<td width="210" align="right" class="key">
					<?= $package->name ?>
				</td>
				<td>
					<?php $sub = $person->subscribedTo($package) ?>
					<form action="index.php?option=com_subscriptions&view=subscription<?=$sub ? '&id='.$person->subscription->id : ''?>" method="post">
						<input type="hidden" name="user_id"  value="<?=$person->userId?>" />
						<input type="hidden" name="package_id" value="<?=$package->id?>" />
						<input type="hidden" name="action" value="edit" />
						
						<input type="checkbox" name="" onchange="this.form.action.value=this.checked ? 'add' : 'delete';this.form.submit()" <?= $sub ? 'checked' : ''?> />
						
						<?php if ( $sub && !$package->recurring ) : ?>
							<?php $date =  $person->subscription->getEndDate()->getDate('%Y-%m-%d') ?>
							<input type="text" value="<?=$date?>" name="end_date" id="end_date_<?=$package->id?>">
							<input type="image" src="templates/system/images/calendar.png" onclick="return showCalendar('end_date_<?=$package->id?>','%Y-%m-%d');" />
							<input type="submit" data-trigger="Request" value="<?= @text('SAVE')?>" />
						<?php endif; ?>
					</form>
				</td>
				<td>
					
				</td>					
			</tr>
			<?php endforeach; ?>
		</table>			
	</fieldset>	
</div>
<div class="clr"></div>
<?= @template('_transactions') ?>