<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Bootstrap_3
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// Запрет прямого доступа.
defined('_JEXEC') or die;

class plgSystemBootstrap_3 extends JPlugin
{

	public function onAfterInitialise()
	{
		$app = JFactory::getApplication();
		if ($app->isAdmin() || ($app->input->getCmd('view') == 'form' && $app->input->getCmd('layout') == 'edit')) return;
		include 'bootstrap.php';
	}

}	
