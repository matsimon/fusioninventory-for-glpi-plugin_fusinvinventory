<?php

/*
   ----------------------------------------------------------------------
   FusionInventory
   Copyright (C) 2003-2008 by the INDEPNET Development Team.

   http://www.fusioninventory.org/   http://forge.fusioninventory.org//
   ----------------------------------------------------------------------

   LICENSE

   This file is part of FusionInventory plugins.

   FusionInventory is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   FusionInventory is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with FusionInventory; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
   ------------------------------------------------------------------------
 */

// Original Author of file: David DURIEUX
// Purpose of file:
// ----------------------------------------------------------------------

define('GLPI_ROOT', '../../..');

include (GLPI_ROOT . "/inc/includes.php");

commonHeader($LANG['plugin_fusioninventory']['title'][0],$_SERVER["PHP_SELF"],"plugins","fusioninventory","fusinvinventory-importxmlfile");

PluginFusioninventoryProfile::checkRight("fusinvinventory", "importxml","r");

PluginFusioninventoryMenu::displayMenu("mini");

if (isset($_FILES['importfile']['tmp_name'])) {
   PluginFusioninventoryProfile::checkRight("fusinvinventory", "importxml","w");

   if ($_FILES['importfile']['tmp_name'] != '') {
      $PluginFusinvinventoryImportXML = new PluginFusinvinventoryImportXML();
      $_SESSION["plugin_fusioninventory_disablelocks"] = 1;
      if ($PluginFusinvinventoryImportXML->importXMLFile($_FILES['importfile']['tmp_name'])) {
         $_SESSION["MESSAGE_AFTER_REDIRECT"] = $LANG['plugin_fusinvinventory']['importxml'][1];
      } else {
         $_SESSION["MESSAGE_AFTER_REDIRECT"] = $LANG['plugin_fusinvinventory']['importxml'][3];
      }
      unset($_SESSION["plugin_fusioninventory_disablelocks"]);
   } else {
      $_SESSION["MESSAGE_AFTER_REDIRECT"] = $LANG['plugin_fusinvinventory']['importxml'][2];
   }
	glpi_header($_SERVER['HTTP_REFERER']);
}

$PluginFusinvinventoryImportXML = new PluginFusinvinventoryImportXML();
$PluginFusinvinventoryImportXML->showForm();

commonFooter();

?>