<?php
/**
 * @package Module TM Ajax Contact Form for Joomla! 3.x
 * @version 2.0.0: mod_tm_ajax_contact_form.php
 * @author TemplateMonster http://www.templatemonster.com
 * @copyright Copyright (C) 2012 - 2014 Jetimpex, Inc.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 
**/
defined('_JEXEC') or die;
class modTmAjaxContactFormHelper{
	public static function recaptchaAjax(){
		JPluginHelper::importPlugin('captcha', 'recaptcha');
		$dispatcher = JEventDispatcher::getInstance();
		$res = $dispatcher->trigger('onCheckAnswer');
		if(!$res[0]){
			$result = 'e';
		  } else {
		    $result = "s";
		}
		return $result;
	}
	public static function getAjax(){
		JFactory::getLanguage()->load('com_contact');
		$input 		 = JFactory::getApplication()->input;
		$mail 		 = JFactory::getMailer();
		$inputs 	 = $input->get('data', array(), 'ARRAY');
		$formcontent = '';
		$key 		 = 0;
		foreach ($inputs as $input){
			if(strpos($input['name'], 'captcha') !== false) continue; 
			if($input['name'] == 'module_id'){
				$db 			= JFactory::getDbo();
				$query 			= $db->getQuery(true);
				$query->select($db->quoteName('params'));
				$query->from($db->quoteName('#__modules'));
				$query->where($db->quoteName('id').' = '.$db->quote($input['value']));
				$db->setQuery($query);
				$params 		= $db->loadResult();
				$params 		= json_decode($params);
				$failed 		= $params->failure_notify;
				$recipient 		= $params->admin_email;
				$cc_email 		= $params->cc_email;
				$bcc_email 		= $params->bcc_email;
				$fields_list	= json_decode($params->fields_list);
				$labels 		= $fields_list->label;
			}
			else{
				if($input['name'] == 'email'){
					$email = $input['value'];
				}
				if($input['name'] == 'subject'){
					$subject = $input['value'];
				}
				$label = isset($labels[$key]) ? $labels[$key] : $input['name'];
				$formcontent .= "<p><strong>".$label.":</strong> ".nl2br($input['value'])."</p>";
				$key++;
			}
		}
		if(isset($recipient)){
			$sender = array();
			if(isset($email)) $mail->addReplyTo($email);
			$config = JFactory::getConfig();
			$global_sender = array(
			    $config->get('mailfrom'),
			    $config->get('fromname') 
			);
			$mail->setSender($global_sender);
			$mail->addRecipient($recipient);
			if(isset($cc_email)) $mail->addCC($cc_email);
			if(isset($bcc_email)) $mail->addBCC($bcc_email);
			if(isset($subject)) $mail->setSubject($subject);
			$mail->isHTML(true);
			$mail->Encoding = 'base64';
			$mail->setBody($formcontent);
			$send = $mail->Send();
			if (isset($email)){
				$mail 			= JFactory::getMailer();
				$formcontent    = '<p>'.JText::sprintf('COM_CONTACT_COPYTEXT_OF', $config->get('fromname'), $_SERVER['HTTP_HOST']).'</p>'.$formcontent;
				if(isset($subject)){
					$copysubject = JText::sprintf('COM_CONTACT_COPYSUBJECT_OF', $subject);
					$mail->setSubject($copysubject);
				}
				$mail->addReplyTo($email);
				$mail->addRecipient($email);
				$mail->setSender($global_sender);
				$mail->isHTML(true);
				$mail->Encoding = 'base64';
				$mail->setBody($formcontent);
				$send = $mail->Send();
			}
			if ($send !== true){
			   	return '<span>'.$send->__toString().'</span>';
			} else {
			  	return "s";
			}
		}
		else{
			return "e";
		}
	}
}