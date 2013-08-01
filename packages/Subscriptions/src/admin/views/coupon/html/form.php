<?php defined('KOOWA') or die('Restricted access'); ?>
<?php JHTML::_('behavior.calendar');?>
<script>

</script>
<form data-behavior="FormValidator" action="<?= @route($coupon->persisted() ? array('id'=>$coupon->id) : '')?>" method="post" class="-koowa-form">
	<div class="col width-50">
	
		<fieldset class="adminform">
			<legend><?= JText::_( 'AN-SB-COUPON' ); ?></legend>
			
			<table class="admintable">
								
				<tr>
					<td width="100" align="right" class="key">
						<?= @text('AN-SB-COUPON-DISCOUNT') ?>
					</td>
					<td>
						<input type="text" value="<?=$coupon->discount * 100?>" size=6 name="discount" />%
					</td>
				</tr>
				
				<tr>
					<td width="100" align="right" class="key">
						<?= @text('AN-SB-COUPON-LIMIT') ?>
					</td>
					<td>
						<input type="text" value="<?=$coupon->limit?>" size=5 name="limit"></input>
					</td>
				</tr>
				<?php if ( $coupon->persisted() ) : ?>
				<tr>
					<td width="100" align="right" class="key">
						<?= @text('AN-SB-COUPON-USED') ?>
					</td>
					<td>
						<?= $coupon->usage ?>
					</td>
				</tr>				
				<?php endif;?>
				
				<tr>
					<td width="100" align="right" class="key">
						<?= @text('AN-SB-COUPON-CODE') ?>
					</td>
					<td>
						<?php if ( $coupon->id ) : ?>
						<?=$coupon->code?>
						<? else : ?>
						<input type="text" value="<?=$coupon->code?>" size=60 name="code"></input>
						<?php endif; ?>
					</td>
				</tr>				
								
				<tr>
					<td width="100" align="right" class="key">
						<?= @text('AN-SB-COUPON-VALID-UNTIL') ?>
					</td>
					<td>
						<input type="text" value="<?=($coupon->expiresOn ? $coupon->expiresOn->getDate('%Y-%m-%d') : '')?>" id="expiration_date" name="expiresOn"></input>
						<input type="image" src="templates/system/images/calendar.png" onclick="return showCalendar('expiration_date','%Y-%m-%d');" />
					</td>
				</tr>
							
								
			</table>
			
		</fieldset>
	
	</div>
	
		
</form>