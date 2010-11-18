<?php

/*
   ----------------------------------------------------------------------
   GLPI - Gestionnaire Libre de Parc Informatique
   Copyright (C) 2003-2008 by the INDEPNET Development Team.

   http://indepnet.net/   http://glpi-project.org/
   ----------------------------------------------------------------------

   LICENSE

   This file is part of GLPI.

   GLPI is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   GLPI is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with GLPI; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
   ------------------------------------------------------------------------
 */

// Original Author of file: David DURIEUX
// Purpose of file:
// ----------------------------------------------------------------------

define('GLPI_ROOT', '../../..');

include (GLPI_ROOT . "/inc/includes.php");

commonHeader($LANG['plugin_fusioninventory']["title"][0],$_SERVER["PHP_SELF"],"plugins","fusioninventory","constructdevice");

PluginFusioninventoryProfile::checkRight("fusinvinventory", "importxml","r");

PluginFusioninventoryMenu::displayMenu("mini");

if (isset($_FILES['importfile']['tmp_name'])) {
   PluginFusioninventoryProfile::checkRight("fusinvinventory", "importxml","w");

   $PluginFusinvinventoryImportXML = new PluginFusinvinventoryImportXML();
   $PluginFusinvinventoryImportXML->importXMLFile($_FILES['importfile']['tmp_name']);

   $_SESSION["MESSAGE_AFTER_REDIRECT"] = $LANG['plugin_fusinvinventory']["importxml"][1];
	glpi_header($_SERVER['HTTP_REFERER']);
}

$PluginFusinvinventoryImportXML = new PluginFusinvinventoryImportXML();
$PluginFusinvinventoryImportXML->showForm();

commonFooter();

?>