<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

$n = count($list);

if($n<$columns){
  $columns = $n;
}

$bootcalc = 12;
$bootclass = 'col-sm-'.$bootcalc/$columns;
$rows = ceil($n/$columns);

$app    = JFactory::getApplication(); 
$doc = JFactory::getDocument();
$document =& $doc;
$template = $app->getTemplate();

JFactory::getLanguage()->load('com_content', JPATH_SITE, null, true); ?>
<div class="mod-newsflash-adv mod-newsflash-adv__<?php echo $moduleclass_sfx; ?> cols-<?php echo $columns; ?>" id="module_<?php echo $module->id; ?>">
  <?php if ($params->get('pretext')): ?>
  <div class="pretext">
    <?php echo $params->get('pretext') ?>
  </div>
  <?php endif;
  if($params->get('masonry')) : ?>
  <div class="masonry row" id="mod-newsflash-adv__masonry<?php echo $module->id; ?>">
  <?php else: ?>
  <div class="row">
  <?php endif;
  for ($i = 0, $n; $i < $n; $i ++) :
    $item = $list[$i];

    $class="";
    if($item->featured){
      $class .= "featured";
    }

    if($i == $n-1){
      $class .=" lastItem";
    }

    if($rows > 1 && $i !== 0 && $i % $columns == 0 && !$params->get('masonry')){
      echo '</div><div class="row">';
    }
  ?>
  <article class="<?php echo $bootclass; ?> item item_num<?php echo $i; ?> item__module  <?php echo $class; ?>" id="item_<?php echo $item->id.'_'.$module->id; ?>">
    <?php require JModuleHelper::getLayoutPath('mod_articles_news_adv', '_pricing-table'); ?>
  </article>
  <?php endfor; ?>
  </div> 
  <div class="clearfix"></div>

  <?php if($params->get('mod_button') == 1): ?>   
  <div class="mod-newsflash-adv_custom-link">
    <?php 
      $menuLink = $menu->getItem($params->get('custom_link_menu'));

        switch ($params->get('custom_link_route')) 
        {
          case 0:
            $link_url = $params->get('custom_link_url');
            break;
          case 1:
            $link_url = JRoute::_($menuLink->link.'&Itemid='.$menuLink->id);
            break;            
          default:
            $link_url = "#";
            break;
        }
        echo '<a class="btn btn-info" href="'. $link_url .'">'. $params->get('custom_link_title') .'</a>';
    ?>
  </div>
  <?php endif; ?>
</div>