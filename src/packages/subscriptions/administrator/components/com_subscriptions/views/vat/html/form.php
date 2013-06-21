<? defined('KOOWA') or die('Restricted access'); ?>

<form  action="<?= @route($vat->persisted() ? '&id='.$vat->id : '')?>" method="post" class="-koowa-form" name="adminForm">
	<div class="col width-50">
	<fieldset class="adminform">
		<legend><?= JText::_( 'AN-SB-VAT-COUNTRY' ); ?></legend>
		<table class="admintable">
			<tr>
				<td width="100" align="right" class="key">
					<?= @text('AN-SB-VAT-SELECT-COUNTRY') ?>
				</td>
				<td>
					<?= @helper('selector.country', array('use_country_code'=>true, 'selected'=>$vat->country))?>
				</td>
			</tr>			
							
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?= JText::_( 'AN-SB-VAT-FEDERAL-TAX' ); ?></legend>
		<table class="admintable">
			<?php foreach($vat->getFederalTaxes() as $i => $tax) :?>
			<tr>
				
				<td   class="key">
					<?= @text('AN-SB-VAT-TAX-NAME') ?>
				</td>
				<td>
					<input type="text" name="federal_tax[<?=$i?>][name]" value="<?= $tax->name ?>" size=10 />
				</td>	
				<td   class="key">
					<?= @text('AN-SB-VAT-TAX-AMOUNT') ?>
				</td>
				<td>
					<input type="text" name="federal_tax[<?=$i?>][value]" value="<?= $tax->value * 100 ?>" size=10 />%
				</td>
			</tr>	
			<?php endforeach;?>
			<tr>

				<td   class="key">
					<?= @text('AN-SB-VAT-TAX-NAME') ?>
				</td>
				<td>
					<input type="text"  name="federal_tax[<?=count($vat->getFederalTaxes())?>][name]" value="" size=10 />
				</td>	
				<td   class="key">
					<?= @text('AN-SB-VAT-TAX-AMOUNT') ?>
				</td>
				<td>
					<input type="text" name="federal_tax[<?=count($vat->getFederalTaxes())?>][value]" value="" size=10 />%
				</td>
			</tr>
		</table>		
		
	</fieldset>	
	
	</div>	
	<div class="clr"></div>
	
</form>