<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
$app = JFactory::getApplication('site');
$template = $app->getTemplate(true);
$document = JFactory::getDocument();
JHtml::_('bootstrap.framework');
$document->addScript('templates/'.$template->template.'/js/jquery.validate.min.js');
$document->addScript('templates/'.$template->template.'/js/additional-methods.min.js');
$document->addScriptdeclaration('jQuery(document).bind("ready",function(){validator=jQuery("#login-form").validate({wrapper:"mark"})});');
?>
<div class="page-login page-login__<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page_header">
  		<<?php echo $template->params->get('categoryPageHeading', 'h3'); ?>><?php echo $this->escape($this->params->get('page_heading')); ?></<?php echo $template->params->get('categoryPageHeading', 'h3'); ?>>
	</div>
	<?php endif;

	if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	<div class="login-description">
	<?php endif;

		if($this->params->get('logindescription_show') == 1) :
		echo $this->params->get('login_description');
		endif;

		if (($this->params->get('login_image') != '')) :?>
		<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JTEXT::_('COM_USER_LOGIN_IMAGE_ALT')?>">
		<?php endif;

	if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	</div>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" id="login-form" novalidate>
		<fieldset class="">
			<?php foreach ($this->form->getFieldset('credentials') as $field):
			if (!$field->hidden):
			echo $field->renderLayout; ?>
			<div class="control-group">
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on">
							<i class="fa fa-<?php if($field->fieldname == 'username'){echo 'user';}elseif($field->fieldname == 'password'){echo 'lock';} ?> hasTooltip" title="<?php echo ucfirst($field->fieldname); ?>"></i>
						</span>
						<?php echo str_replace('/>', ' placeholder="'.ucfirst($field->fieldname).'">', $field->input); ?>
					</div>
				</div>
			</div>
			<?php endif;
			endforeach;
			if ($this->tfa): ?>
				<div class="control-group">
						<?php echo $this->form->getField('secretkey')->label; ?>
					<div class="controls">
						<?php echo $this->form->getField('secretkey')->input; ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary login"><?php echo JText::_('JLOGIN'); ?></button>
					<?php $usersConfig = JComponentHelper::getParams('com_users');
					if ($usersConfig->get('allowUserRegistration')) : ?>
					<a class="btn btn-primary register" href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"><?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
			<div  class="control-group remember">
				<div class="controls">
				<br>
					<p>
						<input id="remember" type="checkbox" name="remember" class="inputbox" value="yes">
						<label for="remember"><?php echo JText::_('COM_USERS_LOGIN_REMEMBER_ME') ?></label>
					</p>
				</div>
			</div>
			<?php endif; ?>
			<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>"><?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?></a> /
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>"><?php echo JText::_('COM_USERS_LOGIN_RESET'); ?></a>
	</form>
</div>