<?php

/*
 * @version $Id$
 -------------------------------------------------------------------------
 FusionInventory
 Copyright (C) 2003-2010 by the INDEPNET Development Team.

 http://www.fusioninventory.org/   http://forge.fusioninventory.org/
 -------------------------------------------------------------------------

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
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: DURIEUX David
// Purpose of file:
// ----------------------------------------------------------------------

function plugin_fusinvinventory_getAddSearchOptions($itemtype) {
   global $LANG;

   $sopt = array();
   if ($itemtype == 'Computer') {
         $sopt[1071]['table']     = 'glpi_plugin_fusinvinventory_antivirus';
         $sopt[1071]['field']     = 'name';
         $sopt[1071]['linkfield'] = '';
         $sopt[1071]['name']      = 'Antivirus name';
         $sopt[1071]['datatype']  = 'text';

         $sopt[1072]['table']     = 'glpi_plugin_fusinvinventory_antivirus';
         $sopt[1072]['field']     = 'version';
         $sopt[1072]['linkfield'] = '';
         $sopt[1072]['name']      = 'Antivirus version';
         $sopt[1072]['datatype']  = 'text';

         $sopt[1073]['table']     = 'glpi_plugin_fusinvinventory_antivirus';
         $sopt[1073]['field']     = 'is_active';
         $sopt[1073]['linkfield'] = '';
         $sopt[1073]['name']      = 'Antivirus activé';
         $sopt[1073]['datatype']  = 'bool';

         $sopt[1074]['table']     = 'glpi_plugin_fusinvinventory_antivirus';
         $sopt[1074]['field']     = 'uptodate';
         $sopt[1074]['linkfield'] = '';
         $sopt[1074]['name']      = 'Antivirus à jour';
         $sopt[1074]['datatype']  = 'bool';
   }
   return $sopt;
}



function plugin_fusinvinventory_giveItem($type,$id,$data,$num) {
	return "";
}



// Define Dropdown tables to be manage in GLPI :
function plugin_fusinvinventory_getDropdown() {
   return array ();
}



/* Cron */
function cron_plugin_fusinvinventory() {

   return 1;
}



function plugin_fusinvinventory_install() {
	global $DB, $LANG, $CFG_GLPI;

   include (GLPI_ROOT . "/plugins/fusinvinventory/install/install.php");
   pluginFusinvinventoryInstall();

   return true;
}



// Uninstall process for plugin : need to return true if succeeded
function plugin_fusinvinventory_uninstall() {
   include (GLPI_ROOT . "/plugins/fusinvinventory/install/install.php");
   pluginFusinvinventoryUninstall();
}



/**
* Check if Fusinvinventory need to be updated
*
* @param
*
* @return 0 (no need update) OR 1 (need update)
**/
function plugin_fusinvinventory_needUpdate() {
   $version = "2.3.0";
   include (GLPI_ROOT . "/plugins/fusinvinventory/install/update.php");
   $version_detected = pluginFusinvinventoryGetCurrentVersion($version);
   if ((isset($version_detected)) AND ($version_detected != $version)) {
      return 1;
   } else {
      return 0;
   }
}



// Define headings added by the plugin //
function plugin_get_headings_fusinvinventory($item,$withtemplate) {
   global $LANG;

   switch (get_class($item)) {
      case 'Computer' :
         $array = array ();
         if ($_GET['id'] > 0) {
            $array[1] = $LANG['plugin_fusioninventory']['title'][1]." ".$LANG['plugin_fusioninventory']['xml'][0];
            $array[2] = $LANG['plugin_fusinvinventory']['antivirus'][0];
         }
         return $array;
         break;
   }


}



// Define headings actions added by the plugin
function plugin_headings_actions_fusinvinventory($item) {

   switch (get_class($item)) {
   case 'Computer' :
      $array = array ();
      $array[1] = "plugin_headings_fusinvinventory_xml";
      $array[2] = "plugin_headings_fusinvinventory_antivirus";
      return $array;
      break;
   }

}


function plugin_headings_fusinvinventory_xml($item) {
   global $LANG;

   $id = $item->getField('id');

   $folder = substr($id,0,-1);
   if (empty($folder)) {
      $folder = '0';
   }
   if (file_exists(GLPI_PLUGIN_DOC_DIR."/fusinvinventory/".$folder."/".$id)) {
      $xml = file_get_contents(GLPI_PLUGIN_DOC_DIR."/fusinvinventory/".$folder."/".$id);
      $xml = str_replace("<", "&lt;", $xml);
      $xml = str_replace(">", "&gt;", $xml);
      $xml = str_replace("\n", "<br/>", $xml);
      echo "<table class='tab_cadre_fixe' cellpadding='1'>";
      echo "<tr>";
      echo "<th>".$LANG['plugin_fusioninventory']['title'][1]." ".$LANG['plugin_fusioninventory']['xml'][0];
      echo " (".$LANG['common'][26]."&nbsp;: " . convDateTime(date("Y-m-d H:i:s", filemtime(GLPI_PLUGIN_DOC_DIR."/fusinvinventory/".$folder."/".$id))).")";
      echo "</th>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td width='130'>";
      echo "<pre width='130'>".$xml."</pre>";
      echo "</td>";
      echo "</tr>";
      echo "</table>";
   }
}



function plugin_headings_fusinvinventory_antivirus($item) {
   global $LANG;

   $items_id = $item->getField('id');

   $PluginFusinvinventoryAntivirus = new PluginFusinvinventoryAntivirus();
   $PluginFusinvinventoryAntivirus->showForm($items_id);
}



function plugin_headings_fusinvinventory($type,$id,$withtemplate=0) {
	global $CFG_GLPI;

}



function plugin_fusinvinventory_MassiveActions($type) {
	global $LANG;

}



function plugin_fusinvinventory_MassiveActionsDisplay($type, $action) {
	global $LANG, $CFG_GLPI, $DB;


}



function plugin_fusinvinventory_MassiveActionsProcess($data) {
	global $LANG;

}



// Massive Action functions
function plugin_fusinvinventory_MassiveActionsFieldsDisplay($type,$table,$field,$linkfield) {
	global $LINK_ID_TABLE,$LANG;


}



function plugin_fusinvinventory_addSelect($type,$id,$num) {
	return "";
}



function plugin_fusinvinventory_forceGroupBy($type) {
    return false;
}



function plugin_fusinvinventory_addLeftJoin($itemtype,$ref_table,$new_table,$linkfield,&$already_link_tables) {
   
   if ($itemtype == 'Computer') {
      return " LEFT JOIN `$new_table` ON (`$ref_table`.`id` = `$new_table`.`computers_id`) ";
   }

	return "";
}



function plugin_fusinvinventory_addOrderBy($type,$id,$order,$key=0) {
	return "";
}



function plugin_fusinvinventory_addWhere($link,$nott,$type,$id,$val) {
	return "";
}



function plugin_pre_item_purge_fusinvinventory($item) {
   
   switch (get_class($item)) {

      case 'Computer' :
         $PluginFusinvinventoryLib = new PluginFusinvinventoryLib();
         $PluginFusinvinventoryLib->removeExternalid($item->getField('id'));
         break;

   }

}



function plugin_pre_item_delete_fusinvinventory($parm) {
	return $parm;
}



/**
 * Hook after updates
 *
 * @param $parm
 * @return nothing
 *
**/
function plugin_item_update_fusinvinventory($parm) {



}



function plugin_item_add_fusinvinventory($parm) {
}

function plugin_fusinvinventory_registerMethods() {
   global $WEBSERVICES_METHOD;
   
   $WEBSERVICES_METHOD['fusioninventory.test'] = array('PluginfusioninventoryWebservice','methodTest');
}

?>