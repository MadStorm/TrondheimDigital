<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_tm_parallax
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/helper.php';

if ($params->def('prepare_content', 1))
{
	JPluginHelper::importPlugin('content');
	$module->content = JHtml::_('content.prepare', $module->content, '', 'mod_tm_parallax.content');
}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$containerClass = ModTMParallaxHelper::getContainer($params);
$rowClass = ModTMParallaxHelper::getRow($params);
require JModuleHelper::getLayoutPath('mod_tm_parallax', $params->get('layout', 'default'));
