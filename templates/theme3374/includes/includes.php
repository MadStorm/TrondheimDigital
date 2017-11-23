<?php
defined('_JEXEC') or die;

JHtml::_('jquery.framework', true, null, true);
JHtml::_('bootstrap.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('bootstrap.loadCss', false, $this->direction);
JHtml::_('formbehavior.chosen', 'select');

$client = new JApplicationWebClient();

include_once 'functions.php';

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$this->language = $doc->language;
$this->direction = $doc->direction;
$template = $this->template;
$csspath = 'templates/'.$template.'/css/';
$jspath = 'templates/'.$template.'/js/';
$option = $app->input->getCmd('option', '');
$view = $app->input->getCmd('view', '');
$layout = $app->input->getCmd('layout', '');
$task = $app->input->getCmd('task', '');
$itemid = $app->input->getCmd('Itemid', '');
$sitename = $app->getCfg('sitename');
$menu = JMenu::getInstance('site');
$contentParams = $app->getParams('com_content');
$bodyClass = "body__".$contentParams->get('pageclass_sfx')." option-".$option." view-".$view." task-".$task." itemid-".$itemid;
$viewport = "";
$todesktop = '';
global $params;
$params = $this->params;

$themeLayout = $params->get('themeLayout');

if($themeLayout == 1) {
    $doc->addStyleSheet($csspath.'layout.css');
}
$doc->addStyleSheet($csspath.'template.css');
$doc->addStyleSheet($csspath.'font-awesome.css');
$doc->addStyleSheet($csspath.'fl-puppets.css');
$doc->addStyleSheet('//fonts.googleapis.com/css?family=Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i');

if(($client->platform == JApplicationWebClient::IPHONE || $client->platform == JApplicationWebClient::IPAD) && ((isset($_COOKIE['disableMobile']) && $_COOKIE['disableMobile']=='false') || !isset($_COOKIE['disableMobile']))){
    $doc->addScript($jspath.'ios-orientationchange-fix.js');
}
if($this->params->get('blackandwhite')){
    $doc->addScript($jspath.'jquery.BlackAndWhite.min.js');
    $doc->addScriptdeclaration('(function($,b){$.fn.BlackAndWhite_init=function(){var a=$(this);a.find("img").not(".slide-img").parent().BlackAndWhite({invertHoverEffect:'.$this->params->get("invertHoverEffect").',intensity:1,responsive:true,speed:{fadeIn:'.$this->params->get('fadeIn').',fadeOut:'.$this->params->get('fadeOut').'}})}})(jQuery);jQuery(window).load(function($){jQuery(".item_img a").find("img").not(".lazy").parent().BlackAndWhite_init()});');
}
$doc->addScriptdeclaration('var path = "'.$jspath.'";');
$doc->addScript($jspath.'scripts.js');

if($client->mobile){
    $bodyClass .= ' mobile';
    if($params->get('todesktop')){
        if($client->platform != 5){
            $doc->addScript($jspath.'desktop-mobile.js');
            $todesktop = "<div class=\"col-sm-12\" id=\"to-desktop\">\r\n
            <a href=\"#\">\r\n
                <span class=\"";
            if((isset($_COOKIE['disableMobile']) && $_COOKIE['disableMobile'] == 'false') || !isset($_COOKIE['disableMobile'])){
                $todesktop .= "to_desktop\">".$params->get('todesktop_text');
            }
            else{
                $todesktop .= "to_mobile\">".$params->get('tomobile_text');
            }
            $todesktop .= "</span>\r\n
            </a>\r\n
        </div>\r\n";
        }
        if((isset($_COOKIE['disableMobile']) && $_COOKIE['disableMobile'] == 'false') || !isset($_COOKIE['disableMobile'])){
            $bodyClass .= " mobile_mode";
            $viewport = "<meta id=\"viewport\" name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
        }else{
            $bodyClass .= " desktop_mode";
        }
    } else {
        $viewport = "<meta id=\"viewport\" name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
    }
}

if($option == 'com_joomgallery'){
    $doc->addStylesheet($csspath.'gallery.css');
}

if (class_exists('Komento') && Komento::loadApplication( 'com_content')) $doc->addStyleSheet($csspath.'komento.css');

if($params->get('logoFile')) $logo = $params->get('logoFile');

if($params->get('footerLogoFile')) $footerLogo = $params->get('footerLogoFile');

if($this->direction == 'rtl') $doc->addStyleSheet('media/jui/css/bootstrap-rtl.css');

$ie_warning = "";
if($client->browser == JApplicationWebClient::IE){
    if((int)$client->browserVersion<10){
        $ie_warning = "<div style=\"clear: both; text-align:center; position: relative;\"><a href=\"http://windows.microsoft.com/en-us/internet-explorer/download-ie\"><img src=\"templates/".$template."/images/ie.png\" border=\"0\" height=\"75\" width=\"1170\" alt=\"You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today.\" /></a></div>";
    }
    if((int)$client->browserVersion<10){
        $doc->addScript($jspath.'jquery.placeholder.min.js');
    }
    $doc->addScriptdeclaration('(function($){$(document).ready(function(){var c=$("#jform_profile_dob_img");if(c.length){var h=$("body").outerHeight()+26-c.offset().top;$("head").append("<style>.calendar{top:auto !important;bottom:"+h+"px !important;}</style>")}})})(jQuery);');
}

$hideByView = false;
switch ($view){
    case 'article':
    case 'login':
    case 'search':
    case 'profile':
    case 'registration':
    case 'reset':
    case 'remind':
    case 'form':
        $hideByView = true;
        break;
}

$hideByOption = false;
if($option == 'com_users' && $option == 'com_search') $hideByOption = true;

if($option == 'com_content' && $layout == 'edit') $hideByView = true;

$asideLeftWidth = $this->countModules('aside-left') && !$hideByOption && $view !== 'form' ? $params->get('asideLeftWidth', 3) : 0;
$asideLeftClass = $params->get('content_layout','normal') != 'fullwidth' ? ' class="col-sm-'.$asideLeftWidth.'"' : null;

$asideRightWidth = $this->countModules('aside-right') && !$hideByOption && $view !== 'form' ? $params->get('asideRightWidth', 3) : 0;
$asideRightClass = $params->get('content_layout','normal') != 'fullwidth' ? ' class="col-sm-'.$asideRightWidth.'"' : null;

$mainContentClass = $params->get('content_layout','normal') != 'fullwidth' ? ' class="col-sm-'.(12 - $asideLeftWidth - $asideRightWidth).'"' : '';

function display_position($pos, $inner = null, $layout = null, $class= null){
    $class = $class ? $class : '';
    $html = "<!-- ".$pos." -->
    <div id=\"".$pos."\"".$class.">";
    global $params;
    $layout = $layout ? $layout : $params->get($pos.'_layout','normal');
    if($layout == "normal"){
        $html .= "<div class=\"container\">
        <div class=\"row\">";
        $html .= $inner ? $inner : "<jdoc:include type=\"modules\" name=\"".$pos."\" style=\"themeHtml5\" />";
        $html .= "</div>
        </div>";
    }else if($layout == "fluid"){
        $html .= "<div class=\"container-fluid\">
        <div class=\"row\">";
        $html .= $inner ? $inner : "<jdoc:include type=\"modules\" name=\"".$pos."\" style=\"themeHtml5\" />";
        $html .= "</div>
        </div>";
    }else if($layout == "content"){
        $html .= "<div class=\"row\">";
        $html .= $inner ? $inner : "<jdoc:include type=\"modules\" name=\"".$pos."\" style=\"themeHtml5\" />";
        $html .= "</div>";
    }
    else{
        $html .= $inner ? $inner : "<jdoc:include type=\"modules\" name=\"".$pos."\" style=\"html5nosize\" />";
    }
    $html .= "</div>\n";
    return $html;
}

$privacyMenuLink = $menu->getItem($params->get('privacy_link_menu'));

$privacy_link_url = JRoute::_($privacyMenuLink->link.'&Itemid='.$privacyMenuLink->id);

$termsMenuLink = $menu->getItem($params->get('terms_link_menu'));

$terms_link_url = JRoute::_($termsMenuLink->link.'&Itemid='.$termsMenuLink->id);

$back_top = '';
if($this->params->get('totop') && !$client->mobile){
    $doc->addScriptdeclaration('(function($){$(document).ready(function(){var o=$("#back-top");$(window).scroll(function(){if($(this).scrollTop()>100){o.fadeIn()}else{o.fadeOut()}});var $scrollEl=($.browser.mozilla||$.browser.msie)?$("html"):$("body");o.find("a").click(function(){$scrollEl.animate({scrollTop:0},400);return false})})})(jQuery);');
    $back_top = '<div id="back-top">
        <a href="#"><span></span>'.$this->params->get("totop_text").'</a>
    </div>';
} ?>