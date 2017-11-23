<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

if( $system->config->get( 'enable_likes' ) ) {
	JHtml::_('bootstrap.framework');
	$doc = JFactory::getDocument();
	$doc->addScriptdeclaration('jQuery(function($){$(".kmt-like.kmt-disabled").popover({placement:"top"})});') ?>
	<span class="kmt-like-wrap">
		<!-- Likes counter -->
		<b class="likesCounter kmt-like-counter"><span><?php echo $row->likes; ?></span></b>

	<?php if ($system->my->allow( 'like_comment') ) { ?>
		<!-- Like/Unlike button -->
		<?php if( $row->liked ) { ?>
			<a class="likeButton kmt-btn kmt-like cancel hasTooltip" href="javascript:void(0);" title="<?php echo JText::_( 'COM_KOMENTO_COMMENT_UNLIKE' ); ?>"><i class="fa fa-heart"></i></a>
		<?php } else { ?>
			<a class="likeButton kmt-btn kmt-like hasTooltip" href="javascript:void(0);" title="<?php echo JText::_( 'COM_KOMENTO_COMMENT_LIKE' ); ?>"><i class="fa fa-heart"></i></a>
		<?php } ?>
	<?php } else { ?>
		<span class="kmt-btn kmt-like kmt-disabled hasTooltip" title="<?php echo JText::_( 'TPL_KOMENTO_COMMENT_LOGIN_TO_LIKE' ); ?>" data-toggle="popover"><i class="fa fa-heart"></i></span>
	<?php } ?>
	</span>
<?php }
