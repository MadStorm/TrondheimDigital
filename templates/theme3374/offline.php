<?php
/**
 * @package     Joomla.Site
 * @subpackage  Template.system
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');

include_once('includes/includes.php');

require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';

$twofactormethods = UsersHelper::getTwoFactorMethods();

if($this->params->get('countdown_time')){
  $timestamp = JHtml::_('date', $this->params->get('countdown_time'), 'U') - date('Z') - date('U');
} ?>
 
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
  <head>
    <?php echo $viewport; ?>
    <jdoc:include type="head" />
    <?php
      if(isset($timestamp) && $timestamp>0){
        $doc->addStyleSheet($csspath.'flipclock.css');
        $doc->addScript($jspath.'flipclock.min.js');
      }
      $doc->addScript($jspath.'jquery.validate.min.js');
      $doc->addScript($jspath.'additional-methods.min.js');
      $js = '(function($){$("#form-login").validate({wrapper:"mark"});';
      if(isset($timestamp) && $timestamp>0){
        $js .= '
        $(document).ready(function(){
        var clock = $(".countdown").FlipClock('.$timestamp.',{countdown:true,clockFace:"DailyCounter"});
        });';
      }
      $js .= '})(jQuery)';
      $doc->addScriptdeclaration($js);
    ?>
  </head>
  <body>
    <?php echo $ie_warning; ?>
    <div class="offline_container">
      <div class="container">
        <div class="row">
          <div class="span12">
            <div class="well">
              <?php if ($app->getCfg('offline_image')) : ?>
              <img src="<?php echo $app->getCfg('offline_image'); ?>" alt="<?php echo htmlspecialchars($app->getCfg('sitename')); ?>" />
              <?php endif; ?>
              <div id="logo">
                <a href="<?php echo JURI::base(); ?>">
                  <?php if(isset($logo)) : ?>
                  <img src="<?php echo $logo;?>" alt="<?php echo $sitename; ?>">
                  <h1><?php echo $sitename; ?></h1>
                  <?php else : ?><h1><?php echo wrap_chars_with_span($sitename); ?></h1><?php endif; ?>
                </a>
              </div>
              <?php if ($app->getCfg('display_offline_message', 1) == 1 && str_replace(' ', '', $app->getCfg('offline_message')) != ''): ?>
              <p class="offline_message"><?php echo $app->getCfg('offline_message'); ?></p>
              <?php elseif ($app->getCfg('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) != ''): ?>
              <p><?php echo JText::_('JOFFLINE_MESSAGE'); ?></p>
              <?php  endif; 
              if($timestamp>0) : ?>
              <div class="countdown"></div>
              <?php endif; ?>
              <jdoc:include type="message" />
              <form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login" novalidate>
                <fieldset class="input">
                  <div id="form-login-username">
                    <input name="username" id="username" type="text" class="inputbox" size="18" placeholder="<?php echo JText::_('JGLOBAL_USERNAME') ?>" required>
                  </div>
                  <div id="form-login-password">
                    <input type="password" name="password" class="inputbox" size="18" id="passwd" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" required>
                  </div>
                  <?php if (count($twofactormethods) > 1) : ?>
                  <div id="form-login-secretkey">
                    <input type="text" name="secretkey" class="inputbox" autocomplete="off" size="18" id="secretkey" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY') ?>">
                  </div>
                  <?php endif; ?>
                  <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
                  <div id="form-login-remember">
                    <label class="checkbox" for="remember">
                      <input type="checkbox" name="remember" class="inputbox" value="yes" alt="<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>" id="remember" />
                      <?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>
                    </label>
                  </div>
                  <?php endif; ?>
                  <button type="submit" name="Submit" class="btn btn-primary" value="<?php echo JText::_('JLOGIN') ?>"><?php echo JText::_('JLOGIN') ?></button>
                  <input type="hidden" name="option" value="com_users">
                  <input type="hidden" name="task" value="user.login">
                  <input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>">
                  <?php echo JHtml::_('form.token'); ?>
                </fieldset>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <jdoc:include type="modules" name="fixed-sidebar-left" style="none" />
  </body>
</html>