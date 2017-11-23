<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_tm_parallax
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$doc = JFactory::getDocument();
$document =& $doc;
$document->addScript('modules/mod_tm_parallax/js/jquery.rd-parallax.js');
$document->addStyleSheet('modules/mod_tm_parallax/css/rd-parallax.css');

?>
<div class="parallax parallax__<?php echo $moduleclass_sfx ?>"
    <?php if ($params->get('backgroundimage')) : ?>
        data-url="<?php echo JURI::base(true) . '/' . $params->get('backgroundimage'); ?>"
        data-speed="<?php echo $params->get('speed') ?>"
        data-direction="<?php echo ($params->get('direction') == '1') ? 'true' : 'false'; ?>"
        data-mobile="<?php echo ($params->get('mobile') == '1') ? 'true' : 'false'; ?>"
        data-blur="<?php echo ($params->get('blur') == '1') ? 'true' : 'false'; ?>"
    <?php endif; ?> >
    <?php echo $module->content; ?>
</div>
