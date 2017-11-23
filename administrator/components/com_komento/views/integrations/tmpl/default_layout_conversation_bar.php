<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>

	<div class="row">
		<div class="col-md-6">
			<fieldset class="panel form-horizontal">
				<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_CONVERSATION_BAR' ); ?></div>
				<div class="panel-body">
					<!-- Enable Conversation Bar -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_CONVERSATION_BAR_ENABLE', 'enable_conversation_bar' ); ?>

					<!-- Max Authors -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_CONVERSATION_BAR_MAX_AUTHORS', 'conversation_bar_max_authors', 'input' ); ?>

					<!-- Include Guest -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_CONVERSATION_BAR_INCLUDE_GUEST', 'conversation_bar_include_guest' ); ?>
				</div>
			</fieldset>
		</div>
	</div>

