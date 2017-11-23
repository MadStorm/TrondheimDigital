<?php
/**
 * @package Module TM Ajax Contact Form for Joomla! 3.x
 * @version 1.2.0: mod_tm_ajax_contact_form.php
 * @author TemplateMonster http://www.templatemonster.com
 * @copyright Copyright (C) 2012 - 2014 Jetimpex, Inc.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 
**/

defined('_JEXEC') or die;
jimport( 'joomla.filter.filteroutput' );

$fields = json_decode($params->get('fields_list', false));

$html = '';

if(!empty($fields->name)){
	if($params->get('admin_email')) {
		$js = '(function($){$(document).ready(function(){var v=$("#contact-form_'.$module->id.'").validate({wrapper:"mark",submitHandler:function(f){';
		if($captcha) {
			$js .= '$(f).ajaxcaptcha();';
		}
		else {
			$js .= '$(f).ajaxsendmail();';
		}
		$js .= 'return false}});';
		if($params->get('reset_publish')){
			$js .= '$("#clear_'.$module->id.'").click(function(){$("#contact-form_'.$module->id.'").trigger("reset");v.resetForm();';
			if($labels_pos){
			    $js .= 'if(!$.support.placeholder){$(".mod_tm_ajax_contact_form *[placeholder]").each(function(n){$(this).parent().find(">.form_placeholder").show()})}';
			}
			$js .= 'return false});';
		}
?>
<?php if($params->get('pretext')){?>
<div class="pretext">
	<?php echo $params->get('pretext');?>
</div>
<?php }?>
<div id="contact_<?php echo $module->id; ?>">
	<form class="mod_tm_ajax_contact_form" id="contact-form_<?php echo $module->id; ?>" novalidate>
		<input type="hidden" id="module_id" name="module_id" value="<?php echo $module->id; ?>">
		<div class="mod_tm_ajax_contact_form_message" id="message_<?php echo $module->id; ?>">
			<span class="s"><?php echo $success; ?></span>
			<span class="e"><?php echo $error; ?></span>
			<span class="c"><?php echo $captcha_error; ?></span>
		</div>
		<fieldset>
			<div class="row-fluid">
				<?php foreach($fields->type as $key => $type){
					$label = $fields->label[$key];
					$id = ' id="'.JFilterOutput::stringURLUnicodeSlug($fields->name[$key]).'_'.$key.'"';
					$placeholder = ' placeholder="'.$label.'"';
					$name = ' name="'.JFilterOutput::stringURLUnicodeSlug($fields->name[$key]).'"';
					if($fields->name[$key] =='') $name = ' name="'.JFilterOutput::stringURLUnicodeSlug($fields->label[$key]).'"';
					$class = ' class="mod_tm_ajax_contact_form_'.$type.' hasTooltip"';
					$req = $fields->req[$key] ? ' required' : '';
					if($type=='tel' && $fields->req[$key]){
						$js .= '$("#'.JFilterOutput::stringURLSafe($fields->name[$key])."_".$key.'").rules("add", {number: true});';
					}
					$title = $fields->title[$key] ? ' title="'.$fields->title[$key].'"' : ' title="'.$label.'"';
					$html .= '<div class="control-group control-group-input span'.$fields->bootstrap_size[$key].'">';
					if(!$labels_pos){
						$html .= '<label for="'.JFilterOutput::stringURLSafe($fields->name[$key]).'_'.$key.'"'.$title.' class="hasTooltip">'.$label.'</label>';
						$placeholder = '';
					}
					$html .= '<div class="controls">';
					switch ($type) {
						case 'textarea':
							$html .= '<textarea'.$name.$placeholder.$id.$class.$req.$title.'></textarea>';
							if($fields->req[$key])
							$js .= '$("#'.JFilterOutput::stringURLSafe($fields->name[$key])."_".$key.'").rules("add", {minlength: '.$params->get('textarea_minlength').'});';
							break;
						case 'select':
							JHtml::_('formbehavior.chosen', 'select');
							$html .= '<select'.$name.$id.$class.$req.$title.'>';
								if($labels_pos){
									$html .= '<option value="test" disabled selected>'.$label.'</option>';
								}
								$options_array = json_decode($fields->options_list[$key]);
								$options = $options_array->option_name;
								foreach ($options as $i => $option){
									$value = $options_array->option_value[$i] !='' ? $options_array->option_value[$i] : $option;
								$html .= '<option value="'.$value.'">'.$option.'</option>';
							}
							$html .= '</select>';
							break;
						default:
							$html .= '<input type="'.$type.'"'.$placeholder.$name.$id.$class.$req.$title.'>';
							break;
					}
					$html .= '</div>
					</div>';
				}
				echo $html;
				if($captcha){ ?>
				<!-- Captcha Field -->
				<div class="control-group control-group-captcha span12">
					<div class="controls">
						<div class="captcha">
					       <?php $params2 = new JRegistry(JPluginHelper::getPlugin('captcha', 'recaptcha')->params); ?>
					       <?php echo $captcha_html;
					       $dispatcher->trigger('onInit','captcha_'.$module->id);?>
					       <?php if($params2->get('version')=='2.0'){?>
					       <div class="g-recaptcha"
					       data-sitekey="<?php echo $params2->get('public_key', ''); ?>"
					       data-theme="<?php echo $params2->get('theme2', 'light')?>"
					       data-size="<?php echo $params2->get('size', 'normal')?>"
					       ></div>
						<?php }?>
					    </div>
					</div>
				</div>
				<?php } ?>
				<!-- Submit Button -->
				<div class="control-group control-group-button span12">
					<div class="controls">
						<button type="submit" class="btn btn-primary mod_tm_ajax_contact_form_btn"><?php echo $params->get('bs_name');?></button>
					<?php if($params->get('reset_publish')) { ?>
						<button type="reset" id="clear_<?php echo $module->id; ?>" class="btn btn-primary mod_tm_ajax_contact_form_btn"><?php echo $params->get('br_name');?></button>
					<?php } ?>
					</div>
				</div>
			</div>
		</fieldset>
	</form>
</div>
<?php 
$js .= '})})(jQuery);';
$document->addScriptDeclaration($js);
} else { ?>
<p><?php echo JText::_('MOD_TM_AJAX_CONTACT_FORM_ENTER_ADMIN_EMAIL'); ?></p>
<?php }
} ?>