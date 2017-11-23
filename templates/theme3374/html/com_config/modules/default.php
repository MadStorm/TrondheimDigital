<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.framework', true);
JHtml::_('behavior.combobox');
JHtml::_('formbehavior.chosen', 'select');

$hasContent = empty($this->item['module']) || $this->item['module'] == 'custom' || $this->item['module'] == 'mod_custom';

// If multi-language site, make language read-only
if (JLanguageMultilang::isEnabled())
{
	$this->form->setFieldAttribute('language', 'readonly', 'true');
}

JFactory::getDocument()->addScriptDeclaration('(function($){$(document).ready(function(){$(".radio.btn-group label").addClass("btn");$(".btn-group label:not(.active)").click(function(){var a=$(this);var b=$("#"+a.attr("for"));if(!b.prop("checked")){a.closest(".btn-group").find("label").removeClass("active btn-success btn-danger btn-primary");if(b.val()==""){a.addClass("active btn-primary")}else if(b.val()==0){a.addClass("active btn-danger")}else{a.addClass("active btn-success")}b.prop("checked",true)}});$("fieldset#jform_published label[for*=jform_published]").each(function(){$(this).attr("title",$(this).text()).tooltip()});$("fieldset#jform_published label[for*=jform_published]").wrapInner("<span/>");$("fieldset#jform_published label[for=jform_published0]").prepend("<i class=\"fa fa-check\"/>");$("fieldset#jform_published label[for=jform_published1]").prepend("<i class=\"fa fa-times-circle\"/>");$("fieldset#jform_published label[for=jform_published2]").prepend("<i class=\"fa fa-trash\"/>");$(".btn-group input[checked]").each(function(){if($(this).val()==""){$("label[for="+$(this).attr("id")+"]").addClass("active btn-primary");}else if($(this).val()==0){$("label[for="+$(this).attr("id")+"]").addClass("active btn-danger");}else{$("label[for="+$(this).attr("id")+"]").addClass("active btn-success")}})})})(jQuery);
	Joomla.submitbutton=function(a){if(a=="config.cancel.modules"||document.formvalidator.isValid(document.getElementById("modules-form"))){Joomla.submitform(a,document.getElementById("modules-form"))}}');
?>

<form
	action="<?php echo JRoute::_('index.php?option=com_config'); ?>"
	method="post" name="adminForm" id="modules-form"
	class="form-validate">

	<div class="row-fluid">

		<!-- Begin Content -->
		<div class="span12">

			<div class="btn-toolbar">
				<div class="btn-group">
					<button type="button" title="<?php echo JText::_('JAPPLY') ?>" class="btn btn-default btn-primary hasTooltip"
						onclick="Joomla.submitbutton('config.save.modules.apply')">
						<i class="fa fa-pencil-square-o"></i>
						<span><?php echo JText::_('JAPPLY') ?></span>
					</button>
				</div>
				<div class="btn-group">
					<button type="button" title="<?php echo JText::_('JSAVE') ?>" class="btn btn-default hasTooltip"
						onclick="Joomla.submitbutton('config.save.modules.save')">
						<i class="fa fa-floppy-o"></i>
						<span><?php echo JText::_('JSAVE') ?></span>
					</button>
				</div>
				<div class="btn-group">
					<button type="button" title="<?php echo JText::_('JCANCEL') ?>" class="btn btn-default hasTooltip"
						onclick="Joomla.submitbutton('config.cancel.modules')">
						<i class="fa fa-times-circle"></i>
						<span><?php echo JText::_('JCANCEL') ?></span>
					</button>
				</div>
			</div>

			<hr class="hr-condensed" />

			<legend><?php echo JText::_('COM_CONFIG_MODULES_SETTINGS_TITLE'); ?></legend>

			<div>
				<?php echo JText::_('COM_CONFIG_MODULES_MODULE_NAME') ?>
				<span class="label label-default"><?php echo $this->item['title'] ?></span>
				&nbsp;&nbsp;
				<?php echo JText::_('COM_CONFIG_MODULES_MODULE_TYPE') ?>
				<span class="label label-default"><?php echo $this->item['module'] ?></span>
			</div>
			<hr />

			<div class="row-fluid">
				<div class="span12">
					<fieldset class="form-horizontal">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('title'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('title'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('showtitle'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('showtitle'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('position'); ?>
							</div>
							<div class="controls">
								<?php echo $this->loadTemplate('positions'); ?>
							</div>
						</div>

						<hr />

						<?php
						if (JFactory::getUser()->authorise('core.edit.state', 'com_modules.module.' . $this->item['id'])): ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('published'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('published'); ?>
							</div>
						</div>
						<?php endif ?>

						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('publish_up'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('publish_up'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('publish_down'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('publish_down'); ?>
							</div>
						</div>

						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('access'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('access'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('ordering'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('ordering'); ?>
							</div>
						</div>
	
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('language'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('language'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('note'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('note'); ?>
							</div>
						</div>

						<hr />

						<div id="options">
							<?php echo $this->loadTemplate('options'); ?>
						</div>

						<?php if ($hasContent): ?>
							<div class="tab-pane" id="custom">
								<?php echo $this->form->getInput('content'); ?>
							</div>
						<?php endif; ?>
					</fieldset>
				</div>

				<input type="hidden" name="id" value="<?php echo $this->item['id'];?>" />
				<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->get('return', null, 'base64');?>" />
				<input type="hidden" name="task" value="" />
				<?php echo JHtml::_('form.token'); ?>

			</div>

		</div>
		<!-- End Content -->
	</div>

</form>
