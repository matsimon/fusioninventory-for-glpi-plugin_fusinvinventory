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

require_once GLPI_ROOT.'/plugins/fusinvsnmp/inc/communicationsnmp.class.php';

/**
 * Class 
 **/
class PluginFusinvinventoryInventory extends PluginFusinvsnmpCommunicationSNMP {
//   private $sxml, $deviceId, $ptd, $type='', $logFile;


   /**
    * Import data
    *
    *@param $p_DEVICEID XML code to import
    *@param $p_CONTENT XML code to import
    *@return "" (import ok) / error string (import ko)
    **/
   function import($p_DEVICEID, $p_CONTENT) {
      global $LANG;

      $this->setXML($p_CONTENT);
      $errors = '';

      $glpi_id = "3005";
      // Criteria to find right device

      // if found, update fields
      $this->parseSections();


      return $errors;
   }


   function parseSections() {
      //foreach
      
   }


   function sendLib($p_DEVICEID, $p_CONTENT, $p_xml) {

      // check if we can load XML (for example if not UTF-8)

      libxml_use_internal_errors(true);

      $doc = simplexml_load_string($p_xml);

      if (!$doc) {
          $errors = libxml_get_errors();

          foreach ($errors as $error) {
              	logInFile('fusinvinventory',display_xml_error($error, $xml));
          }

          libxml_clear_errors();
      }


      require_once GLPI_ROOT ."/plugins/fusioninventory/lib/libfusioninventory-server-php/Classes/FusionLibServer.class.php";
      require_once GLPI_ROOT ."/plugins/fusioninventory/lib/libfusioninventory-server-php/Classes/MyException.class.php";
      require_once GLPI_ROOT ."/plugins/fusioninventory/lib/libfusioninventory-server-php/Classes/Logger.class.php";

      $config = array();

      $config['storageEngine'] = "Directory";
      $config['storageLocation'] = "/../../../../../../../files/_plugins/fusinvinventory";

      // criterias available: "motherboardSerial", "assetTag", "msn",
      // "ssn", "baseboardSerial", "macAddress", "uuid", "winProdKey",
      // "biosSerial","enclosureSerial","smodel","storagesSerial","drivesSerial"
      $config['criterias'][] = "uuid";
      $config['criterias'][] = "ssn";

      $config['maxFalse'] = 0;

      $config['filter'] = 0;
      $config['printError'] = 1;

      $config['sections'][] = "DRIVES";
      $config['sections'][] = "NETWORKS";


      define("LIBSERVERFUSIONINVENTORY_LOG_FILE",GLPI_PLUGIN_DOC_DIR.'/fusioninventory/logs');
      define("LIBSERVERFUSIONINVENTORY_STORAGELOCATION",GLPI_PLUGIN_DOC_DIR.'/fusioninventory');
      define("LIBSERVERFUSIONINVENTORY_HOOKS_CLASSNAME","PluginFusinvinventoryLibhook");

      $log = new Logger('../../../../../../files/_plugins/fusioninventory/logs');

      $action = ActionFactory::createAction("inventory");

      $action->checkConfig("../../../../../fusinvinventory/inc", $config);
      $action->startAction(simplexml_load_string($p_xml));
   }
   
}

?>