<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php 
defined('_JEXEC') or die('Restricted access'); 

class RokNavMenusHelper extends MenusHelper {
	
	function Target( &$row )
	{
		$click[] = JHTML::_('select.option',  '0', JText::_( 'Parent Window With Browser Navigation' ) );
		$click[] = JHTML::_('select.option',  '1', JText::_( 'New Window With Browser Navigation' ) );
		$click[] = JHTML::_('select.option',  '2', JText::_( 'New Window Without Browser Navigation' ) );
		JPluginHelper::importPlugin('roknavmenu');
		$dispatcher	   =& JDispatcher::getInstance();
		$dispatcher->trigger('onRokNavMenuRegisterDisplayType', array (&$click));
		$target = JHTML::_('select.genericlist',   $click, 'browserNav', 'class="inputbox" size="4"', 'value', 'text', intval( $row->browserNav ) );
		return $target;
	}
	
	function getPluginParams(&$item) {
		JPluginHelper::importPlugin('roknavmenu');
		$param_files = array();
		$param_sets = array();
		$dispatcher	   =& JDispatcher::getInstance();
		$dispatcher->trigger('onRokNavMenuRegisterParamsFile', array (&$param_files, $item->id));
		reset($param_files);
		while (list($key, $value) = each($param_files)) {
			$param_file =& $param_files[$key]; 
			$set_params = new JParameter($item->params);
			if (file_exists( $param_file ))
			{
				$xml =& JFactory::getXMLParser('Simple');
				if ($xml->loadFile($param_file)){
					$document =& $xml->document;
					$set_params->setXML( $document->params[0] );
				}
			}
			$param_sets[$key]=$set_params;
		}	
		return $param_sets;
	}
}
?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	var form = document.adminForm;
	var type = form.type.value;

	if (pressbutton == 'cancelItem') {
		submitform( pressbutton );
		return;
	}
	if ( (type != "separator") && (trim( form.name.value ) == "") ){
		alert( "<?php echo JText::_( 'Item must have a title', true ); ?>" );
	}
	<?php if( $this->item->type == 'component' && isset($this->item->linkparts['option']) && $this->item->linkparts['option'] == 'com_newsfeeds' && isset($this->item->linkparts['view']) && $this->item->linkparts['view'] == 'category' ){ ?>
	else if( document.getElementById('urlparamsid').value == 0 ){
 		alert( "<?php echo JText::_('Please select a Category', true ); ?>" );
	} <?php } ?>
	<?php if( $this->item->type == 'component' && isset($this->item->linkparts['option']) && $this->item->linkparts['option'] == 'com_weblinks' && isset($this->item->linkparts['view']) && $this->item->linkparts['view'] == 'category' ){ ?>
	else if( document.getElementById('urlparamsid').value == 0 ){
 		alert( "<?php echo JText::_('Please select a Category', true ); ?>" );
	} <?php } ?>
	<?php if( $this->item->type == 'component' && isset($this->item->linkparts['option']) && $this->item->linkparts['option'] == 'com_newsfeeds' && isset($this->item->linkparts['view']) && $this->item->linkparts['view'] == 'newsfeed' ){ ?>
	else if( document.getElementById('urlparamsid').value == 0 ){
 		alert( "<?php echo JText::_('Please select a Feed', true ); ?>" );
	} <?php } ?>
	<?php if( $this->item->type == 'component' && isset($this->item->linkparts['option']) && $this->item->linkparts['option'] == 'com_content' && isset($this->item->linkparts['view']) && $this->item->linkparts['view'] == 'category' ){ ?>
	else if( document.getElementById('urlparamsid').value == 0 ){
 		alert( "<?php echo JText::_('Please select a Category', true ); ?>" );
	} <?php } ?>
	<?php if( $this->item->type == 'component' && isset($this->item->linkparts['option']) && $this->item->linkparts['option'] == 'com_content' && isset($this->item->linkparts['view']) && $this->item->linkparts['view'] == 'section' ){ ?>
	else if( document.getElementById('urlparamsid').value == 0 ){
 		alert( "<?php echo JText::_('Please select a Section', true ); ?>" );
	} <?php } ?>
	<?php if( $this->item->type == 'component' && isset($this->item->linkparts['option']) && $this->item->linkparts['option'] == 'com_poll' && isset($this->item->linkparts['view']) && $this->item->linkparts['view'] == 'poll' ){ ?>
	else if( document.getElementById('urlparamsid').value == 0 ){
 		alert( "<?php echo JText::_('Please select a Poll', true ); ?>" );
	} <?php } ?>
	<?php if( $this->item->type == 'component' && isset($this->item->linkparts['option']) && $this->item->linkparts['option'] == 'com_content' && isset($this->item->linkparts['view']) && $this->item->linkparts['view'] == 'article' && !isset($this->item->linkparts['layout']) ){ ?>
	else if( document.getElementById('id_id').value == 0 ){
		alert( "<?php echo JText::_('Please select an Article', true ); ?>" );
	} <?php } ?> else {
		submitform( pressbutton );
	}
}
//-->
</script>
<form action="index.php" method="post" name="adminForm">
	<table class="admintable" width="100%">
		<tr valign="top">
			<td width="60%">
				<!-- Menu Item Type Section -->
				<fieldset>
					<legend>
						<?php echo JText::_( 'Menu Item Type' ); ?>
					</legend>
					<div style="float:right">
						<button type="button" onclick="location.href='index.php?option=com_menus&amp;task=type&amp;menutype=<?php echo $this->item->menutype;?><?php echo $this->item->expansion; ?>&amp;cid[]=<?php echo $this->item->id; ?>';">
							<?php echo JText::_( 'Change Type' ); ?></button>
					</div>
					<h2><?php echo $this->name; ?></h2>
					<div>
						<?php echo $this->description; ?>
					</div>
				</fieldset>
				<!-- Menu Item Details Section -->
				<fieldset>
					<legend>
						<?php echo JText::_( 'Menu Item Details' ); ?>
					</legend>
					<table width="100%">
						<?php if ($this->item->id) { ?>
						<tr>
							<td class="key" width="20%" align="right">
								<?php echo JText::_( 'ID' ); ?>:
							</td>
							<td width="80%">
								<strong><?php echo $this->item->id; ?></strong>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<td class="key" align="right">
								<?php echo JText::_( 'Title' ); ?>:
							</td>
							<td>
								<input class="inputbox" type="text" name="name" size="50" maxlength="255" value="<?php echo $this->item->name; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key" align="right">
								<?php echo JText::_( 'Alias' ); ?>:
							</td>
							<td>
								<input class="inputbox" type="text" name="alias" size="50" maxlength="255" value="<?php echo $this->item->alias; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key" align="right">
								<?php echo JText::_( 'Link' ); ?>:
							</td>
							<td>
								<input class="inputbox" type="text" name="link" size="50" maxlength="255" value="<?php echo $this->item->link; ?>" <?php echo $this->lists->disabled;?> />
							</td>
						</tr>
						<tr>
							<td class="key" align="right">
								<?php echo JText::_( 'Display in' ); ?>:
							</td>
							<td>
								<?php echo JHTML::_('select.genericlist',   $this->menutypes, 'menutype', 'class="inputbox" size="1"', 'menutype', 'title', $this->item->menutype );?>
							</td>
						</tr>
						<tr>
							<td class="key" align="right" valign="top">
								<?php echo JText::_( 'Parent Item' ); ?>:
							</td>
							<td>
								<?php echo MenusHelper::Parent( $this->item ); ?>
							</td>
						</tr>
						<tr>
							<td class="key" valign="top" align="right">
								<?php echo JText::_( 'Published' ); ?>:
							</td>
							<td>
								<?php echo $this->lists->published ?>
							</td>
						</tr>
						<tr>
							<td class="key" valign="top" align="right">
								<?php echo JText::_( 'Ordering' ); ?>:
							</td>
							<td>
								<?php echo JHTML::_('menu.ordering', $this->item, $this->item->id ); ?>
							</td>
						</tr>
						<tr>
							<td class="key" valign="top" align="right">
								<?php echo JText::_( 'Access Level' ); ?>:
							</td>
							<td>
								<?php echo JHTML::_('list.accesslevel',  $this->item ); ?>
							</td>
						</tr>
						<tr>
							<td class="key" valign="top" align="right">
								<?php echo JText::_( 'On Click, Open in' ); ?>:
							</td>
							<td>
								<?php echo RokNavMenusHelper::Target( $this->item ); ?>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
			<!-- Menu Item Parameters Section -->
			<td width="40%">
				<?php
					echo $this->pane->startPane("menu-pane");
					echo $this->pane->startPanel(JText :: _('Parameters - Basic'), "param-page");
					echo $this->urlparams->render('urlparams');
					if(count($this->params->getParams('params'))) :
						echo $this->params->render('params');
					endif;

					if(!count($this->params->getNumParams('params')) && !count($this->urlparams->getNumParams('urlparams'))) :
						echo "<div style=\"text-align: center; padding: 5px; \">".JText::_('There are no parameters for this item')."</div>";
					endif;
					echo $this->pane->endPanel();

					if($params = $this->advanced->render('params')) :
						echo $this->pane->startPanel(JText :: _('Parameters - Advanced'), "advanced-page");
						echo $params;
						echo $this->pane->endPanel();
					endif;

					if ($this->comp && ($params = $this->comp->render('params'))) :
						echo $this->pane->startPanel(JText :: _('Parameters - Component'), "component-page");
						echo $params;
						echo $this->pane->endPanel();
					endif;
					
					$param_sets = RokNavMenusHelper::getPluginParams( $this->item );
					if (count($param_sets) > 0) :
						reset($param_sets);
						while (list($key, $value) = each($param_sets)) :
							$plugin_params =& $param_sets[$key];
							if ($params = $plugin_params->render('params')) :
								echo $this->pane->startPanel(JText::_($key), "$key-page");
								echo $params;
								echo $this->pane->endPanel();
							endif;
						endwhile;
					endif;
					
					if ($this->sysparams && ($params = $this->sysparams->render('params'))) :
						echo $this->pane->startPanel(JText :: _('Parameters - System'), "system-page");
						echo $params;
						echo $this->pane->endPanel();
					endif;
					echo $this->pane->endPane();
				?>
			</td>
		</tr>
	</table>

	<?php echo $this->item->linkfield; ?>

	<input type="hidden" name="option" value="com_menus" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="componentid" value="<?php echo $this->item->componentid; ?>" />
	<input type="hidden" name="type" value="<?php echo $this->item->type; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

