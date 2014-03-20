<?php
/*
 * Gamuza Freight - This module offers freights functionality.
 * Copyright (C) 2013 Gamuza Technologies (http://www.gamuza.com.br/)
 * Author: Eneias Ramos de Melo <eneias@gamuza.com.br>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Library General Public
 * License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Library General Public License for more details.
 *
 * You should have received a copy of the GNU Library General Public
 * License along with this library; if not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
 * Boston, MA  02110-1301, USA.
 */

/*
 * See the AUTHORS file for a list of people on the Gamuza Team.
 * See the ChangeLog files for a list of changes.
 * These files are distributed with Gamuza_Freight at http://code.google.com/p/gamuzaopen/.
 */

class Gamuza_Freight_Model_Config
extends Mage_Core_Model_Abstract
{
	public function getShippingPrice ($carrierName, $postCode, $packageWeight)
	{
		$carrier = $this->_getCarrier ($carrierName);
		$carrier_id = $carrier->getData ('id');
		$website_id = $this->_getActualWebsiteId ();
		$store_id = $this->_getActualStoreId ();

$sqlBlock = <<<SQLBLOCK
carrier_id = $carrier_id
AND (website_id = $website_id OR website_id = -1)
AND (store_id = $store_id OR store_id = -1)
AND $postCode BETWEEN begin_zip AND end_zip
AND $packageWeight BETWEEN begin_weight AND end_weight
SQLBLOCK;

		$collection = Mage::getModel ('freight/freights')->getCollection();
		$collection->getSelect ()->where ($sqlBlock);
		$collection->load ();
	
		return $collection->count () ? $collection->getFirstItem () : null;
	}

	public function importFreights ($carrierName)
	{
		$request = Mage::app()->getRequest ();
		$utilsConfig = Mage::getModel ('utils/config');
		
		$website = $request->getParam ('website');
		$store = $request->getParam ('store');
		
		$website = $utilsConfig->getWebsite ('code', $website);
		$website_id = !empty ($website) ? $website->getId () : -1;
		$store = $utilsConfig->getStore ('code', $store);
		$store_id = !empty ($store) ? $store->getId () : -1;
		
		if (!isset ($_FILES['groups'])) return;
	
		$carrier = $this->_getCarrier ($carrierName);
		$carrier_id = $carrier->getData ('id');
	
		if (empty ($carrier_id)) return;
	
		$csvFile = $_FILES['groups']['tmp_name'][$carrierName]['fields']['import']['value'];
		if (empty ($csvFile)) return;
	
		$csvContent = str_replace (chr(32), chr(0), file_get_contents ($csvFile));
		if (empty ($csvContent)) return;
	
		$csvLines = explode ("\n", $csvContent);
	
		$line = explode (',', array_shift($csvLines));
		if (strcmp (strtolower ($line [0]), 'beginzip')
		|| strcmp (strtolower ($line [1]), 'endzip')
		|| strcmp (strtolower ($line [2]), 'beginweight')
		|| strcmp (strtolower ($line [3]), 'endweight')
		|| strcmp (strtolower ($line [4]), 'deliverytime')
		|| strcmp (strtolower ($line [5]), 'deliveryprice')) return;
	
		$resource = Mage::getSingleton ('core/resource');
		$write = $resource->getConnection ('core_write');
		$table = $resource->getTableName('freight/freights');
	
		$count = 0;
		$write->beginTransaction ();
		try
		{
			$condition = array ("carrier_id = {$carrier_id} AND website_id = {$website_id} AND store_id = {$store_id}");
			$write->delete ($table, $condition);
			$write->commit ();
		}
		catch (Exception $e)
		{
			$write->rollback ();
			Mage::throwException ($e->getMessage ());
		}
	
		$write->beginTransaction ();
		try
		{
		foreach ($csvLines as $id => $value)
		{
			$line = explode (',', $value);
		
			$errors = 0;
			foreach ($line as $item) if (!is_numeric ($item)) ++ $errors;
		
			if ($errors) continue;
		
			$data = array ('carrier_id' => $carrier_id,
					'website_id' => $website_id,
					'store_id' => $store_id,
					'begin_zip' => $line [0],
					'end_zip' => $line [1],
					'begin_weight' => $line [2],
					'end_weight' => $line [3],
					'delivery_time' => $line [4],
					'delivery_price' => $line [5]);
		
			$write->insert ($table, $data);
		
			++ $count;
		}
		$write->commit ();
		}
		catch (Exception $e)
		{
			$write->rollback ();
			Mage::throwException ($e->getMessage ());
		}
	
		return $count > 0;
	}
	
	private function _getCarrier ($carrierName)
	{
	    return Mage::getModel ('utils/config')->getCarrier ($carrierName);
	}
	
	private function _getActualWebsiteId ()
	{
	    return Mage::getModel ('utils/config')->getActualWebsite ()->getId ();
	}

	private function _getActualStoreId ()
	{
	    return Mage::getModel ('utils/config')->getActualStore ()->getId ();
	}
}
