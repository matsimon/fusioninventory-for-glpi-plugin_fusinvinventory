<?php
/*
 * @version $Id$
 ----------------------------------------------------------------------
 FusionInventory
 Coded by the FusionInventory Development Team.

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

// ----------------------------------------------------------------------
// Original Author of file: David DURIEUX
// Purpose of file:
// ----------------------------------------------------------------------

include_once ("hook.php");

// inc files 

foreach (glob(GLPI_ROOT.'/plugins/fusinvinventory/inc/*.php') as $file) {
   include_once($file);
}

?>