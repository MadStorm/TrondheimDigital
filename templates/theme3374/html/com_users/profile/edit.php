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
JHtml::_('behavior.tooltip');
$app = JFactory::getApplication('site');
$template = $app->getTemplate(true);
$document = JFactory::getDocument();

JHtml::_('bootstrap.framework');
$document->addScript('templates/'.$template->template.'/js/jquery.validate.min.js');
$document->addScript('templates/'.$template->template.'/js/additional-methods.min.js');
$document->addScriptdeclaration('jQuery(document).bind("ready",function(){jQuery("#member-profile").validate({wrapper:"mark",rules:{"jform[password2]":{equalTo:"#jform_password1"},"jform[email2]":{equalTo:"#jform_email1"}}});var a=jQuery("#jform_profile_dob").closest(".controls").addClass("calendar_wrapper");jQuery("#jform_profile_dob_spacer-lbl").clone().appendTo(a);jQuery("#jform_profile_dob_spacer-lbl:first").closest(".control-group").remove()});');
//load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);
?>
<div class="page-profile__edit page-profile__edit__<?php echo $this->pageclass_sfx?>">
	<div class="page_header">
  		<<?php echo $template->params->get('categoryPageHeading', 'h3'); ?>><?php echo $this->escape($this->params->get('page_heading')); ?></<?php echo $template->params->get('categoryPageHeading', 'h3'); ?>>
	</div>
	<form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
		<?php foreach ($this->form->getFieldsets() as $group => $fieldset):// Iterate through the form fieldsets and display each one.
		$fields = $this->form->getFieldset($group);
		if (count($fields)):?>
		<fieldset>
			<?php 
			foreach ($fields as $field):// Iterate through the fields in the set and display them.
			if ($field->hidden):// If the field is hidden, just display the input.?>
			<div class="control-group">
				<div class="controls">
					<?php echo $field->input;?>
				</div>
			</div>
			<?php else:?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $field->label;
					if (!$field->required && $field->type != 'Spacer'): ?>
					<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL'); ?></span>
					<?php endif; ?>
				</div>
				<div class="controls">
					<?php echo $field->input; ?>
				</div>
			</div>
			<?php endif;
			endforeach;?>
		</fieldset>
		<?php endif;
		endforeach;?>
		<div class="controls">
			<button type="submit" class="btn btn-primary validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
			<a class="btn btn-primary cancel" href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>
			<input type="hidden" name="option" value="com_users">
			<input type="hidden" name="task" value="profile.save">
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>