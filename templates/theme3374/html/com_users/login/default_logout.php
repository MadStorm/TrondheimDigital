<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JFactory::getDocument()->addScriptdeclaration('jQuery(document).bind("ready",function(){jQuery("#jform_profile_dob_spacer-lbl").closest("dt").remove().next("dd").remove()});');
?>
<div class="page_profile page_profile__<?php echo $this->pageclass_sfx?>">
	<?php if (JFactory::getUser()->id == $this->user->id) : ?>
	<ul class="btn-toolbar pull-right">
		<li class="btn-group">
			<a class="btn" href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->user->id);?>"><i class="fa fa-user"></i> <?php echo JText::_('COM_USERS_Edit_Profile'); ?></a>
		</li>
	</ul>
	<?php endif; ?>
	<div class="extra_wrap">
	<?php 
	echo $this->loadTemplate('core'); ?>
	</div>
	<?php if (($this->params->get('logoutdescription_show') == 1 && str_replace(' ', '', $this->params->get('logout_description')) != '')|| $this->params->get('logout_image') != '') : ?>
	<div class="logout-description">
	<?php endif; ?>

		<?php if ($this->params->get('logoutdescription_show') == 1) : ?>
			<?php echo $this->params->get('logout_description'); ?>
		<?php endif; ?>

		<?php if (($this->params->get('logout_image') != '')) :?>
			<img src="<?php echo $this->escape($this->params->get('logout_image')); ?>" class="thumbnail pull-right logout-image" alt="<?php echo JTEXT::_('COM_USER_LOGOUT_IMAGE_ALT')?>"/>
		<?php endif; ?>

	<?php if (($this->params->get('logoutdescription_show') == 1 && str_replace(' ', '', $this->params->get('logout_description')) != '')|| $this->params->get('logout_image') != '') : ?>
	</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.logout'); ?>" method="post">
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary"><span class="fa fa-sign-out"></span> <?php echo JText::_('JLOGOUT'); ?></button>
			</div>
		</div>
		<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('logout_redirect_url', $this->form->getValue('return'))); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>