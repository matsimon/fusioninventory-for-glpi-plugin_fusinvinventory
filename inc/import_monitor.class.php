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
// Original Author of file: MAZZONI Vincent
// Purpose of file: management of communication with agents
// ----------------------------------------------------------------------
/**
 * The datas are XML encoded and compressed with Zlib.
 * XML rules :
 * - XML tags in uppercase
 **/

if (!defined('GLPI_ROOT')) {
	die("Sorry. You can't access directly to this file");
}

/**
 * Class
 **/
class PluginFusinvinventoryImport_Monitor extends CommonDBTM {

   function AddUpdateItem($type, $items_id, $dataSection) {

      foreach($dataSection as $key=>$value) {
         $dataSection[$key] = addslashes_deep($value);
      }

      $monitor = new Monitor();

      $a_monitor = array();

      if ($type == "update") {
         return "";
      }
      // Else (type == "add")
      // Search if a monitor yet exist
      if ((isset($dataSection['SERIAL'])) AND (!empty($dataSection['SERIAL']))) {
         $a_monitors = $monitor->find("`serial`='".$dataSection['SERIAL']."'","", 1);
         if (count($a_monitors) == 0) {
            $a_monitor = array();
         } else {
            foreach($a_monitors as $monitor_id=>$data) {
               $a_monitor = $data;
            }
         }
      }

      if (isset($dataSection['CAPTION'])) {
         $a_monitor['name'] = $dataSection['CAPTION'];
      }
      if ((isset($dataSection['MANUFACTURER']))
              AND (!empty($dataSection['MANUFACTURER']))) {
         $a_monitor['manufacturers_id'] = Dropdown::importExternal('Manufacturer',
                                                                   $dataSection['MANUFACTURER']);
      }
      if (isset($dataSection['SERIAL'])) {
         $a_monitor['serial'] = $dataSection['SERIAL'];
      }
      if (isset($dataSection['DESCRIPTION'])) {
         $a_monitor['comment'] = $dataSection['DESCRIPTION'];
      }

      $a_monitor['is_global'] = 0;

      if (!isset($a_monitor['id'])) {
         $monitor_id = $monitor->add($a_monitor);
      } else {
         $monitor_id = $a_monitor['id'];
      }

      $Computer_Item = new Computer_Item();
      $devID = $Computer_Item->add(array('computers_id' => $items_id,
                                 'itemtype'     => 'Monitor',
                                 'items_id'     => $monitor_id,
                                 '_no_history'  => true));
      return $devID;
   }



   function deleteItem($items_id, $idmachine) {
      $Computer_Item = new Computer_Item();
      $Computer_Item->getFromDB($items_id);
      if ($Computer_Item->fields['computers_id'] == $idmachine) {
         $Computer_Item->delete(array("id" => $items_id,
                                      "itemtype" => "Monitor"));
      }
   }

}

?>