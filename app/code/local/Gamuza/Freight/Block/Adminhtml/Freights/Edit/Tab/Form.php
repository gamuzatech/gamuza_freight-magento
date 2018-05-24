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

class Gamuza_Freight_Block_Adminhtml_Freights_Edit_Tab_Form
extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$configModel = Mage::getModel ('utils/config');
		$carriersModel = Mage::getModel ('utils/carriers');
		
		$websites = $configModel->getAllWebsites ();
		foreach ($websites as $_website) $wsOptions [$_website->getId ()] = $_website->getName ();
		$wsOptions ['-1'] = $this->__('All');
		
		$stores = $configModel->getAllStores ();
		foreach ($stores as $_store) $stOptions [$_store->getId ()] = $_store->getName ();
		$stOptions ['-1'] = $this->__('All');
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset("freight_form", array("legend"=>Mage::helper("freight")->__("Item Information")));

		$fieldset->addField("carrier_id", "select", array(
		"label" => Mage::helper("freight")->__("Carrier Name"),
		"class" => "required-entry",
		"required" => true,
		"name" => "carrier_id",
		"options" => $configModel->toOptions ($carriersModel->getCollection()->load(), array ('id' => 'description')),
		));
		$fieldset->addField("website_id", "select", array(
		"label" => Mage::helper("freight")->__("Website"),
		"class" => "required-entry",
		"required" => true,
		"name" => "website_id",
		"options" => $wsOptions,
		));
		$fieldset->addField("store_id", "select", array(
		"label" => Mage::helper("freight")->__("Store"),
		"class" => "required-entry",
		"required" => true,
		"name" => "store_id",
		"options" => $stOptions,
		));
		$fieldset->addField("begin_zip", "text", array(
		"label" => Mage::helper("freight")->__("Begin ZIP Code"),
		"class" => "required-entry",
		"required" => true,
		"name" => "begin_zip",
		));
		$fieldset->addField("end_zip", "text", array(
		"label" => Mage::helper("freight")->__("End ZIP Code"),
		"class" => "required-entry",
		"required" => true,
		"name" => "end_zip",
		));
		$fieldset->addField("begin_weight", "text", array(
		"label" => Mage::helper("freight")->__("Begin Package Weight"),
		"class" => "required-entry",
		"required" => true,
		"name" => "begin_weight",
		));
		$fieldset->addField("end_weight", "text", array(
		"label" => Mage::helper("freight")->__("End Package Weight"),
		"class" => "required-entry",
		"required" => true,
		"name" => "end_weight",
		));
		$fieldset->addField("delivery_type", "select", array(
		"label" => Mage::helper("freight")->__("Delivery Type"),
		"class" => "required-entry",
		"required" => true,
		"name" => "delivery_type",
		"options" => Mage::getModel ('freight/system_config_source_delivery_type')->toArray (),
		));
		$fieldset->addField("delivery_time", "text", array(
		"label" => Mage::helper("freight")->__("Delivery Time"),
		"class" => "required-entry",
		"required" => true,
		"name" => "delivery_time",
		));
		$fieldset->addField("delivery_price", "text", array(
		"label" => Mage::helper("freight")->__("Delivery Price"),
		"class" => "required-entry",
		"required" => true,
		"name" => "delivery_price",
		));

		if (Mage::getSingleton("adminhtml/session")->getFreightData())
		{
			$form->setValues(Mage::getSingleton("adminhtml/session")->getFreightData());
			Mage::getSingleton("adminhtml/session")->setFreightData(null);
		} 
		elseif(Mage::registry("freight_data"))
		{
		    $form->setValues(Mage::registry("freight_data")->getData());
		}

		return parent::_prepareForm();
	}
}

