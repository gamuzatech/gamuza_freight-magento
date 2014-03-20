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
	
class Gamuza_Freight_Block_Adminhtml_freights_Edit
extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();

		$this->_objectId = "id";
		$this->_blockGroup = "freight";
		$this->_controller = "adminhtml_freights";
		$this->_updateButton("save", "label", Mage::helper("freight")->__("Save Item"));
		$this->_updateButton("delete", "label", Mage::helper("freight")->__("Delete Item"));

		$this->_addButton("saveandcontinue", array(
			"label"     => Mage::helper("freight")->__("Save And Continue Edit"),
			"onclick"   => "saveAndContinueEdit()",
			"class"     => "save",
		), -100);

		$this->_formScripts[] = "function saveAndContinueEdit(){editForm.submit($('edit_form').action+'back/edit/');}";
	}

	public function getHeaderText()
	{
		if( Mage::registry("freight_data") && Mage::registry("freight_data")->getId() )
		{
			return Mage::helper("freight")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("freight_data")->getId()));
		} 
		else
		{
			 return Mage::helper("freight")->__("Add Item");
		}
	}
}
