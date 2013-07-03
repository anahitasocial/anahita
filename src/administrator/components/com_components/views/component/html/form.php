<?php defined('KOOWA') or die('Restricted access'); ?>

<form id="" action="<?= @route(array('id'=>$component->id))?>" method="post" class="-koowa-form" data-token-name="_token" data-token-value="<?=JUtility::getToken()?>">
	<input type="hidden" name="action" value="assign" />
	<div class="col width-50">
	
		<fieldset class="adminform">
			<legend><?= JText::_( 'AN-APPS-APP-DETAILS' ); ?></legend>
			
			<table class="admintable">
				<tr>
					<td width="100" align="right" class="key">
						<?= @text('AN-APPS-APP-NAME') ?>
					</td>
					<td><?= $component->name ?></td>
				</tr>
				
				<tr>
					<td width="100" align="right" class="key">
						<?= @text('AN-APPS-APP-COMPONENT') ?>
					</td>
					<td><?= $component->component ?></td>
				</tr>			
			</table>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'AN-APPS-APP-ACTOR-ACCESS-CONTROL-LIST' ); ?></legend>
		
			<table class="admintable">				
				<?php foreach($actor_identifiers as $identifier) : ?>
				<tr>
					<td align="right" class="key"><?= ucfirst($identifier->name) ?></td>
					<td>					    
					    <?php if ( $component->getAssignmentOption() & ComComponentsDomainBehaviorAssignable::OPTION_OPTIONAL ) : ?>
    						<?= @html('select', 'identifiers['.$identifier.']', array('options'=>array(
    								0 => @text('AN-APPS-APP-ACTOR-ACCESS-OPTIONAL'),
    								1 => @text('AN-APPS-APP-ACTOR-ACCESS-ALWAYS'),
    								2 => @text('AN-APPS-APP-ACTOR-ACCESS-NEVER')
    							), 'selected'=>$component->getAssignmentForIdentifier($identifier)) )?>
						<?php else : ?>
    						<?= @html('select', 'identifiers['.$identifier.']', array('options'=>array(    								
    								1 => @text('AN-APPS-APP-ACTOR-ACCESS-ALWAYS'),
    								2 => @text('AN-APPS-APP-ACTOR-ACCESS-NEVER')
    							), 'selected'=>$component->getAssignmentForIdentifier($identifier)) )?>						
						<?php endif;?>
					</td>
				</tr>
				<?php endforeach;?>				
			</table>	
	</fieldset>
	
	</div>
	<div class="col width-40">
		&nbsp;
	</div>
	<div class="clr"></div>
</form>

<?= JHTML::_('behavior.keepalive') ?>