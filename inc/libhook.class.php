<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2010 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

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
class PluginFusinvinventoryLibhook {
    /**
    * Disable instance
    * @access private
    *
    */
    private function __construct()
    {
    }

    /**
    * create a new machine in an application
    * @access public
    * @return int $externalId Id to match application data with the library
    */
    public static function createMachine() {

       $Computer = new Computer;
       $input = array();
       $input['is_deleted'] = 0;
       $input['autoupdatesystems_id'] = Dropdown::importExternal('AutoUpdateSystem', 'FusionInventory');
       return $Computer->add($input);
    }

    /**
    * add a new section to the machine in an application
    * @access public
    * @param int $externalId
    * @param string $sectionName
    * @param array $dataSection
    * @return int $sectionId
    */
    public static function addSections($data, $idmachine) {
      echo "section added";

      $Computer = new Computer;
      $sectionsId = array();
      $Computer->getFromDB($idmachine);

      $i = -1;
      foreach($data as $section) {
         $i++;
         $dataSection = unserialize($section['dataSection']);
         switch ($section['sectionName']) {

            case 'BIOS':
               if (isset($dataSection['SMANUFACTURER'])) {
                  $Manufacturer = new Manufacturer;
                  $Computer->fields['manufacturers_id'] = $Manufacturer->import($Manufacturer->processName($dataSection['SMANUFACTURER']));
               }
               if (isset($dataSection['SMODEL'])) {
                  $ComputerModel = new ComputerModel;
                  $Computer->fields['computermodels_id'] = $ComputerModel->import(array('name'=>$dataSection['SMODEL']));
               }
               if (isset($dataSection['SSN']))
                  $Computer->fields['serial'] = $dataSection['SSN'];

               break;

            case 'HARDWARE':
               if (isset($dataSection['NAME']))
                  $Computer->fields['name'] = $dataSection['NAME'];
               if (isset($dataSection['OSNAME'])) {
                  $OperatingSystem = new OperatingSystem;
                  $Computer->fields['operatingsystems_id'] = $OperatingSystem->import(array('name'=>$dataSection['OSNAME']));
               }
               if (isset($dataSection['OSVERSION'])) {
                  $OperatingSystemVersion = new OperatingSystemVersion;
                  $Computer->fields['operatingsystemversions_id'] = $OperatingSystemVersion->import(array('name'=>$dataSection['OSVERSION']));
               }
               if (isset($dataSection['WINPRODID'])) {
                  $Computer->fields['os_licenseid'] = $dataSection['WINPRODID'];
               }
               if (isset($dataSection['WINPRODKEY'])) {
                  $Computer->fields['os_license_number'] = $dataSection['WINPRODKEY'];
               }
               break;

         }
      }

      $Computer->update($Computer->fields);
      $j = 0;

      foreach($data as $section) {
         $dataSection = unserialize($section['dataSection']);
         switch ($section['sectionName']) {

            case 'CPUS':

               $DeviceProcessor = new DeviceProcessor();
               $Computer_Device = new Computer_Device('DeviceProcessor');

               $input = array();
               $input['designation'] = $dataSection['NAME'];
               $input['frequence'] = $dataSection['SPEED'];
               $Manufacturer = new Manufacturer;

               $input["manufacturers_id"] = Dropdown::importExternal('Manufacturer',
                                                                          $dataSection['MANUFACTURER']);
               $input['specif_default'] = $dataSection['SPEED'];

               $proc_id = $DeviceProcessor->import($input);
               $input = array();
               $input['computers_id'] = $idmachine;
               $input['deviceprocessors_id'] = $proc_id;
               $input['specificity'] = $dataSection['SPEED'];
               $input['_itemtype'] = 'DeviceProcessor';
               $id_link_device = $Computer_Device->add($input);

               array_push($sectionsId,$section['sectionName']."/".$id_link_device);
               break;

            case 'DRIVES':
               $ComputerDisk = new ComputerDisk;
               $id_disk = 0;
               $disk=array();
               $disk['computers_id']=$idmachine;
               // totalsize 	freesize
               if (isset($dataSection['LABEL'])) {
                  $disk['name']=$dataSection['LABEL'];
               } else if ((!isset($dataSection['VOLUMN'])) AND (isset($dataSection['LETTER']))) {
                  $disk['name']=$dataSection['LETTER'];
               } else {
                  $disk['name']=$dataSection['TYPE'];
               }
               if (isset($dataSection['VOLUMN'])) {
                  $disk['device']=$dataSection['VOLUMN'];
               }
               if (isset($dataSection['MOUNTPOINT'])) {
                  $disk['mountpoint'] = $dataSection['MOUNTPOINT'];
               } else if (isset($dataSection['LETTER'])) {
                  $disk['mountpoint'] = $dataSection['LETTER'];
               } else if (isset($dataSection['TYPE'])) {
                  $disk['mountpoint'] = $dataSection['TYPE'];
               }
               $disk['filesystems_id']=Dropdown::importExternal('Filesystem', $dataSection["FILESYSTEM"]);
               if (isset($dataSection['TOTAL'])) {
                  $disk['totalsize']=$dataSection['TOTAL'];
               }
               if (isset($dataSection['FREE'])) {
                  $disk['freesize']=$dataSection['FREE'];
               }
               if (isset($disk['name']) && !empty($disk["name"])) {
                  $id_disk = $ComputerDisk->add($disk);
               }
               array_push($sectionsId,$section['sectionName']."/".$id_disk);
               break;

            case 'MEMORIES':
               $CompDevice = new Computer_Device('DeviceMemory');
               if (!empty ($dataSection["CAPACITY"])) {
                  $ram = array();
                  $ram["designation"]="";
                  if ($dataSection["TYPE"]!="Empty Slot" && $dataSection["TYPE"] != "Unknown") {
                     $ram["designation"]=$dataSection["TYPE"];
                  }
                  if ($dataSection["DESCRIPTION"]) {
                     if (!empty($ram["designation"])) {
                        $ram["designation"].=" - ";
                     }
                     $ram["designation"] .= $dataSection["DESCRIPTION"];
                  }
                  if (!is_numeric($dataSection["CAPACITY"])) {
                     $dataSection["CAPACITY"]=0;
                  }

                  $ram["specif_default"] = $dataSection["CAPACITY"];
                  
                  $ram["frequence"] = $dataSection["SPEED"];
                  $ram["devicememorytypes_id"]
                        = Dropdown::importExternal('DeviceMemoryType', $dataSection["TYPE"]);

                  $DeviceMemory = new DeviceMemory();
                  $ram_id = $DeviceMemory->import($ram);
                  if ($ram_id) {
                     $devID = $CompDevice->add(array('computers_id' => $idmachine,
                                                     '_itemtype'     => 'DeviceMemory',
                                                     'devicememories_id'     => $ram_id,
                                                     'specificity'  => $dataSection["CAPACITY"]));
                     array_push($sectionsId,$section['sectionName']."/".$devID);
                  } else {
                     array_push($sectionsId,$section['sectionName']."/".$j);
                     $j++;
                  }
               } else {
                  array_push($sectionsId,$section['sectionName']."/".$j);
                  $j++;
               }
               break;

            case 'NETWORKS':
               $NetworkPort = new NetworkPort();
               $network = array();
               $network['items_id']=$idmachine;
               $network['itemtype'] = 'Computer';
               $network['name'] = addslashes($dataSection["DESCRIPTION"]);
               $network['ip'] = $dataSection["IPADDRESS"];
               $network['mac'] = $dataSection["MACADDR"];
               if (isset($dataSection["TYPE"])) {
                  $network["networkinterfaces_id"]
                              = Dropdown::importExternal('NetworkInterface', $dataSection["TYPE"]);
               }
               if (isset($dataSection["IPMASK"]))
                  $network['netmask'] = $dataSection["IPMASK"];
               if (isset($dataSection["IPGATEWAY"]))
                  $network['gateway'] = $dataSection["IPGATEWAY"];
               if (isset($dataSection["IPSUBNET"]))
                  $network['subnet'] = $dataSection["IPSUBNET"];

               $devID = $NetworkPort->add($network);

               array_push($sectionsId,$section['sectionName']."/".$devID);
               break;

            case 'SOFTWARES':

               // Add software name
               // Add version of software
               // link version with computer : glpi_computers_softwareversions
               $PluginFusinvinventorySoftwares = new PluginFusinvinventorySoftwares;
               if (isset($dataSection['VERSION'])) {
                  $Computer_SoftwareVersion_id = $PluginFusinvinventorySoftwares->addSoftware($idmachine, array('name'=>$dataSection['NAME'],
                                                                              'version'=>$dataSection['VERSION']));
               } else {
                  $Computer_SoftwareVersion_id = $PluginFusinvinventorySoftwares->addSoftware($idmachine, array('name'=>$dataSection['NAME'],
                                                                              'version'=>''));
               }
               array_push($sectionsId,$section['sectionName']."/".$Computer_SoftwareVersion_id);
               break;

//              case 'INPUTS':
//                 $Peripheral = new Peripheral;
//
//
//                 break;

//            case 'VERSIONCLIENT':
//               // Verify agent is created
//               $PluginFusioninventoryAgent = new PluginFusioninventoryAgent;
//               $a_agent = $PluginFusioninventoryAgent->InfosByKey($section['sectionName']);
//               if (count($a_agent) == '0') {
//                  // TODO : Create agent
//
//               }
//               $PluginFusioninventoryAgent->getFromDB($a_agent['id']);
//               $PluginFusioninventoryAgent->fields['items_id'] = $idmachine;
//               $PluginFusioninventoryAgent->fields['itemtype'] = 'Computer';
//               $PluginFusioninventoryAgent->update($PluginFusioninventoryAgent->fields);
//               break;

            case 'BIOS':
               array_push($sectionsId,$section['sectionName']."/".$idmachine);
               break;


            case 'HARDWARE':
               array_push($sectionsId,$section['sectionName']."/".$idmachine);
               break;

            default:
               array_push($sectionsId,$section['sectionName']."/".$j);
               $j++;
               break;

         }
      }
       
      return $sectionsId;
    }

    /**
    * remove a machine's section in an application
    * @access public
    * @param int $externalId
    * @param string $sectionName
    * @param array $dataSection
    */
    public static function removeSections($idsections, $idmachine)
    {
        echo "section removed";

       logInFile("removesection", print_r($idsections, true));

        $sectionsId = array();
        return $sectionsId;
    }


    
    public static function updateSections($data, $idmachine) {

      foreach($data as $section) {
         $dataSection = unserialize($section['dataSection']);
         $array = explode("/", $section['sectionId']);
         $items_id = $array[1];
         $sectionName = $array[0];

         switch ($sectionName) {

            case 'DRIVES':
               $PluginFusinvinventoryImport_Drive = new PluginFusinvinventoryImport_Drive();
               $PluginFusinvinventoryImport_Drive->AddUpdateItem("update", $items_id, $dataSection);
               
               break;

         }
      }



       logInFile("updatesection", print_r($data, true));
    }
}

?>