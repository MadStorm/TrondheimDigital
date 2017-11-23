<?php
/**
 * @package Module TM Style Switcher for Joomla! 3.x
 * @version 1.0.2: mod_tm_style_switcher.php
 * @author TemplateMonster http://www.templatemonster.com
 * @copyright Copyright (C) 2012 - 2015 Jetimpex, Inc.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 
**/

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$user = JFactory::getUser();
$template = $app->getTemplate();
$color_schemes = JPATH_SITE.'/templates/'.$template.'/css/color_schemes/';
if(file_exists($color_schemes)){
$getColorScheme = $app->getTemplate(true)->params->get('color_scheme', 'color_scheme_1');
$host='livedemo00.template-help.com';
if($user->authorise('core.edit', 'com_templates') || $_SERVER['HTTP_HOST']==$host){
    $doc->addStyleSheet('modules/mod_tm_style_switcher/css/style.css');
    $doc->addScript('modules/mod_tm_style_switcher/js/jquery.cookies.js');
    $doc->addScriptDeclaration('jQuery(function($){$("#style_switcher .toggler").click(function(){$("#style_switcher").toggleClass("shown")});$("#style_switcher").style_switcher("'.JURI::base(true).'/templates/'.$template.'/css/color_schemes/","' .JURI::base(true).'")});');
    $color_schemes_array = array();
    $key = 0;
    foreach (new DirectoryIterator($color_schemes) as $file){
        if($file->isDot()) continue;
        if($file->getExtension()=='css'){
            $color_schemes_array[$key] = $file->getBasename('.css');
            $key++;
        }
    }
    sort($color_schemes_array); ?>
<div id="style_switcher">
    <div class="toggler"></div>
    <p><?php echo JText::_('MOD_TM_STYLE_SWITCHER_BOX_LABEL'); ?></p>
<?php $html = '';
if ($user->authorise('core.edit', 'com_templates')) {
    $doc->addScript('modules/mod_tm_style_switcher/js/style_switcher.js');
    $html = '
    <p><span>'.JText::_("MOD_TM_STYLE_SWITCHER_BOX_DESC").'</span></p>
    <form id="style_switcher_form">
    <input type="hidden" name="color_scheme" id="style_switcher_input">
    <button class="button btn" id="style_switcher_button" disabled>'.JText::_("JSAVE").'</button>
    </form>'; ?>
<?php } else if($_SERVER['HTTP_HOST']==$host){
    if(isset($_COOKIE['color_scheme']) && $_COOKIE['color_scheme']!=''){
        $getColorScheme = $_COOKIE['color_scheme'];
    }
    $doc->addScript('modules/mod_tm_style_switcher/js/style_switcher_demo.js'); ?>
<?php } ?>
    <ul id="color-box">
    <?php foreach ($color_schemes_array as $file){ ?>
        <li<?php if($getColorScheme==$file) echo ' class="active"'; ?>><div class="color_scheme <?php echo $file; ?>" data-scheme="<?php echo $file; ?>">&nbsp;</div></li>
    <?php } ?>
    </ul>
    <?php echo $html; ?>
</div>
<?php 
}
$doc->addStyleSheet('templates/'.$template.'/css/color_schemes/'.$getColorScheme.'.css', 'text/css', null, array('id'=>'color_scheme'));
} ?>