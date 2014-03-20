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

class Gamuza_Freight_Adminhtml_FreightsController
extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$this->loadLayout()->_setActiveMenu("freight/freights")->_addBreadcrumb(Mage::helper("adminhtml")->__("freights  Manager"),Mage::helper("adminhtml")->__("freights Manager"));

		return $this;
	}

	public function indexAction() 
	{
		$this->_initAction();
		$this->renderLayout();
	}

	public function editAction()
	{
		$brandsId = $this->getRequest()->getParam("id");
		$brandsModel = Mage::getModel("freight/freights")->load($brandsId);
		if ($brandsModel->getId() || $brandsId == 0)
		{
			Mage::register("freight_data", $brandsModel);

			$this->loadLayout();
			$this->_setActiveMenu("freight/freights");
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("freights Manager"), Mage::helper("adminhtml")->__("freights Manager"));
			$this->_addBreadcrumb(Mage::helper("adminhtml")->__("freights Description"), Mage::helper("adminhtml")->__("freights Description"));
			$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock("freight/adminhtml_freights_edit"))->_addLeft($this->getLayout()->createBlock("freight/adminhtml_freights_edit_tabs"));
			$this->renderLayout();
		} 
		else
		{
			Mage::getSingleton("adminhtml/session")->addError(Mage::helper("freight")->__("Item does not exist."));

			$this->_redirect("*/*/");
		}
	}

	public function newAction()
	{
		$id   = $this->getRequest()->getParam("id");
		$model  = Mage::getModel("freight/freights")->load($id);

		$data = Mage::getSingleton("adminhtml/session")->getFormData(true);
		if (!empty($data)) $model->setData($data);

		Mage::register("freight_data", $model);

		$this->loadLayout();
		$this->_setActiveMenu("freight/freights");
		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("freights Manager"), Mage::helper("adminhtml")->__("freights Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("freights Description"), Mage::helper("adminhtml")->__("freights Description"));
		$this->_addContent($this->getLayout()->createBlock("freight/adminhtml_freights_edit"))->_addLeft($this->getLayout()->createBlock("freight/adminhtml_freights_edit_tabs"));
		$this->renderLayout();
	}

	public function saveAction()
	{
		$post_data=$this->getRequest()->getPost();
		if ($post_data)
		{
			try
			{
				$brandsModel = Mage::getModel("freight/freights")
				->addData($post_data)
				->setId($this->getRequest()->getParam("id"))
				->save();

				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("freights was successfully saved"));
				Mage::getSingleton("adminhtml/session")->setfreightsData(false);

				if ($this->getRequest()->getParam("back"))
				{
					$this->_redirect("*/*/edit", array("id" => $brandsModel->getId()));

					return;
				}

				$this->_redirect("*/*/");

				return;
			} 
			catch (Exception $e)
			{
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
				Mage::getSingleton("adminhtml/session")->setfreightsData($this->getRequest()->getPost());

				$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));

				return;
			}
		}

		$this->_redirect("*/*/");
	}

	public function deleteAction()
	{
		if( $this->getRequest()->getParam("id") > 0 )
		{
			try
			{
				$brandsModel = Mage::getModel("freight/freights");
				$brandsModel->setId($this->getRequest()->getParam("id"))->delete();

				Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));

				$this->_redirect("*/*/");
			} 
			catch (Exception $e)
			{
				Mage::getSingleton("adminhtml/session")->addError($e->getMessage());

				$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
			}
		}

		$this->_redirect("*/*/");
	}

	public function exportAction()
	{
		$carrier = $this->getRequest()->getParam ('carrier');
		$website = $this->getRequest()->getParam ('website');
		$store = $this->getRequest()->getParam ('store');

		$utilsConfig = Mage::getModel ('utils/config');
		$website = $utilsConfig->getWebsite ('code', $website);
		$website_id = !empty ($website) ? $website->getWebsiteId () : -1;
		
		$store = $utilsConfig->getStore ('code', $store);
		$store_id = !empty ($store) ? $store->getStoreId () : -1;

		$item = Mage::getModel ('utils/config')->getCarrier ($carrier);
		$carrier_name = $item->getData ('name');
		$carrier_id = $item->getData ('id');

		$collection = Mage::getModel ('freight/freights')->getCollection();
		$collection->getSelect()->where ("carrier_id = {$carrier_id} AND website_id = {$website_id} AND store_id = {$store_id}");
		$collection->load ();
		
		header('Pragma: public');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-type: application/octet-stream');
		header("Content-disposition: attachment; filename={$carrier_name}.csv");

		echo implode (',', array ('BeginZip', 'EndZip', 'BeginWeight', 'EndWeight', 'DeliveryTime', 'DeliveryPrice')) . "\n";
		foreach ($collection as $item)
		{
			echo implode (',', $item->toArray (array ('begin_zip', 'end_zip', 'begin_weight', 'end_weight', 'delivery_time', 'delivery_price'))) . "\n";
		}

		die;
	}
}
