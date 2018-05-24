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

class Gamuza_Freight_Block_Adminhtml_Freights_Grid
extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();

		$this->setId("freightsGrid");
		$this->setDefaultSort("id");
		$this->setDefaultDir("ASC");
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel("freight/freights")->getCollection();
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$configModel = Mage::getModel ('utils/config');
		$carriersModel = Mage::getModel ('utils/carriers');
		$websites = $configModel->getAllWebsites ();
		foreach ($websites as $_website) $wsOptions [$_website->getId ()] = $_website->getName ();
		$wsOptions ['-1'] = $this->__('All');
		
		$stores = $configModel->getAllStores ();
		foreach ($stores as $_store) $stOptions [$_store->getId ()] = $_store->getName ();
		$stOptions ['-1'] = $this->__('All');
		
		$this->addColumn("id", array(
		"header" => Mage::helper("freight")->__("ID"),
		"align" =>"right",
		"width" => "50px",
		"index" => "id",
		));
		$this->addColumn("carrier_id", array(
		"header" => Mage::helper("freight")->__("Carrier Name"),
		"align" =>"left",
		"index" => "carrier_id",
		"type" => "options",
		"options" => $configModel->toOptions ($carriersModel->getCollection()->load(), array ('id' => 'description')),
		));
		$this->addColumn("website_id", array(
		"header" => Mage::helper("freight")->__("Website"),
		"align" =>"left",
		"index" => "website_id",
		"type" => "options",
		"options" => $wsOptions,
		"renderer" => "utils/adminhtml_widget_grid_column_renderer_website",
		));
		$this->addColumn("store_id", array(
		"header" => Mage::helper("freight")->__("Store"),
		"align" =>"left",
		"index" => "store_id",
		"type" => "options",
		"options" => $stOptions,
		"renderer" => "utils/adminhtml_widget_grid_column_renderer_store",
		));
		$this->addColumn("begin_zip", array(
		"header" => Mage::helper("freight")->__("Begin ZIP Code"),
		"align" =>"left",
		"index" => "begin_zip",
		));
		$this->addColumn("end_zip", array(
		"header" => Mage::helper("freight")->__("End ZIP Code"),
		"align" =>"left",
		"index" => "end_zip",
		));
		$this->addColumn("begin_weight", array(
		"header" => Mage::helper("freight")->__("Begin Package Weight"),
		"align" =>"left",
		"index" => "begin_weight",
		));
		$this->addColumn("end_weight", array(
		"header" => Mage::helper("freight")->__("End Package Weight"),
		"align" =>"left",
		"index" => "end_weight",
		));
		$this->addColumn("delivery_type", array(
		"header" => Mage::helper("freight")->__("Delivery Type"),
		"align" =>"left",
		"index" => "delivery_type",
		"type" => "options",
		"options" => Mage::getModel ('freight/system_config_source_delivery_type')->toArray (),
		));
		$this->addColumn("delivery_time", array(
		"header" => Mage::helper("freight")->__("Delivery Time"),
		"align" =>"left",
		"index" => "delivery_time",
		));
		$this->addColumn("delivery_price", array(
		"header" => Mage::helper("freight")->__("Delivery Price"),
		"align" =>"left",
		"index" => "delivery_price",
		));

		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
	   return $this->getUrl("*/*/edit", array("id" => $row->getId()));
	}
}

