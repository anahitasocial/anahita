<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); ?>

<?php
	$cid = JRequest::getVar( 'cid', array(0) );
	$edit		= JRequest::getVar('edit',true);
	$text = intval($edit) ? JText::_( 'Edit' ) : JText::_( 'New' );

	JToolBarHelper::title( JText::_( 'User' ) . ': <small><small>[ '. $text .' ]</small></small>' , 'user.png' );
	JToolBarHelper::save();
	JToolBarHelper::apply();
	if ( $edit ) {
		// for existing items the button is renamed `close`
		JToolBarHelper::cancel( 'cancel', 'Close' );
	} else {
		JToolBarHelper::cancel();
	}
	JToolBarHelper::help( 'screen.users.edit' );	
?>

<?php
	// clean item data
	JFilterOutput::objectHTMLSafe( $this->user, ENT_QUOTES, '' );

	if ($this->user->get('lastvisitDate') == "0000-00-00 00:00:00") {
		$lvisit = JText::_( 'Never' );
	} else {
		$lvisit	= JHTML::_('date', $this->user->get('lastvisitDate'), '%Y-%m-%d %H:%M:%S');
	}
?>
<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&]", "i");

		// do field validation
		if (trim(form.name.value) == "") {
			alert( "<?php echo JText::_( 'You must provide a name.', true ); ?>" );
		} else if (form.username.value == "") {
			alert( "<?php echo JText::_( 'You must provide a user login name.', true ); ?>" );
		} else if (r.exec(form.username.value) || form.username.value.length < 2) {
			alert( "<?php echo JText::_( 'WARNLOGININVALID', true ); ?>" );
		} else if (trim(form.email.value) == "") {
			alert( "<?php echo JText::_( 'You must provide an email address.', true ); ?>" );
		} else if (form.gid.value == "") {
			alert( "<?php echo JText::_( 'You must assign user to a group.', true ); ?>" );
		} else if (((trim(form.password.value) != "") || (trim(form.password2.value) != "")) && (form.password.value != form.password2.value)){
			alert( "<?php echo JText::_( 'Password do not match.', true ); ?>" );
		} else if (form.gid.value == "29") {
			alert( "<?php echo JText::_( 'WARNSELECTPF', true ); ?>" );
		} else if (form.gid.value == "30") {
			alert( "<?php echo JText::_( 'WARNSELECTPB', true ); ?>" );
		} else {
			submitform( pressbutton );
		}
	}

	function gotocontact( id ) {
		var form = document.adminForm;
		form.contact_id.value = id;
		submitform( 'contact' );
	}
</script>
<form action="index.php" method="post" name="adminForm" autocomplete="off">
	<div class="col width-45">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'User Details' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tr>
					<td width="150" class="key">
						<label for="name">
							<?php echo JText::_( 'Name' ); ?>
						</label>
					</td>
					<td>
						<input type="text" name="name" id="name" class="inputbox" size="40" value="<?php echo $this->user->get('name'); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="username">
							<?php echo JText::_( 'Username' ); ?>
						</label>
					</td>
					<td>
						<input type="text" name="username" id="username" class="inputbox" size="40" value="<?php echo $this->user->get('username'); ?>" autocomplete="off" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="email">
							<?php echo JText::_( 'Email' ); ?>
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="email" id="email" size="40" value="<?php echo $this->user->get('email'); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="password">
							<?php echo JText::_( 'New Password' ); ?>
						</label>
					</td>
					<td>
						<?php if(!$this->user->get('password')) : ?>
							<input class="inputbox disabled" type="password" name="password" id="password" size="40" value="" disabled="disabled" />
						<?php else : ?>
							<input class="inputbox" type="password" name="password" id="password" size="40" value=""/>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="password2">
							<?php echo JText::_( 'Verify Password' ); ?>
						</label>
					</td>
					<td>
						<?php if(!$this->user->get('password')) : ?>
							<input class="inputbox disabled" type="password" name="password2" id="password2" size="40" value="" disabled="disabled" />
						<?php else : ?>
							<input class="inputbox" type="password" name="password2" id="password2" size="40" value=""/>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						<label for="gid">
							<?php echo JText::_( 'Group' ); ?>
						</label>
					</td>
					<td>
						<?php echo $this->lists['gid']; ?>
					</td>
				</tr>
				<?php if ($this->me->authorize( 'com_users', 'block user' )) { ?>
				<tr>
					<td class="key">
						<?php echo JText::_( 'Block User' ); ?>
					</td>
					<td>
						<?php echo $this->lists['block']; ?>
					</td>
				</tr>
				<?php } if ($this->me->authorize( 'com_users', 'email_events' )) { ?>
				<tr>
					<td class="key">
						<?php echo JText::_( 'Receive System Emails' ); ?>
					</td>
					<td>
						<?php echo $this->lists['sendEmail']; ?>
					</td>
				</tr>
				<?php } if( $this->user->get('id') ) { ?>
				<tr>
					<td class="key">
						<?php echo JText::_( 'Register Date' ); ?>
					</td>
					<td>
						<?php echo JHTML::_('date', $this->user->get('registerDate'), '%Y-%m-%d %H:%M:%S');?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo JText::_( 'Last Visit Date' ); ?>
					</td>
					<td>
						<?php echo $lvisit; ?>
					</td>
				</tr>
				<?php } ?>
			</table>
		</fieldset>
	</div>
	<div class="col width-55">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Parameters' ); ?></legend>
			<table class="admintable">
				<tr>
					<td>
						<?php
							$params = $this->user->getParameters(true);
							echo $params->render( 'params' );
						?>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" value="<?php echo $this->user->get('id'); ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $this->user->get('id'); ?>" />
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="contact_id" value="" />
	<?php if (!$this->me->authorize( 'com_users', 'email_events' )) { ?>
	<input type="hidden" name="sendEmail" value="0" />
	<?php } ?>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
