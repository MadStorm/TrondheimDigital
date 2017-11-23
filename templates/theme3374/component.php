<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select($db->quoteName('template'))
->from($db->quoteName('#__template_styles'))
->where($db->quoteName('client_id') . ' = 1 AND ' . $db->quoteName('home') . ' = 1');
$db->setQuery($query);
$adminTemplate = $db->loadResult(); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
  <head>
    <jdoc:include type="head" />
    <link rel="stylesheet" href="media/jui/css/bootstrap.min.css" />
    <link rel="stylesheet" href="administrator/templates/<?php echo $adminTemplate ?>/css/template.css" />
  </head>
  <body class="contentpane modal">
    <jdoc:include type="message" />
    <jdoc:include type="component" />
  </body>
</html>