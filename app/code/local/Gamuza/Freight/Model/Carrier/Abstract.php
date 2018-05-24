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

class Gamuza_Freight_Model_Carrier_Abstract
extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		if (!$this->getConfigFlag ('active')) return false;
	
		$result = Mage::getModel('shipping/rate_result');
	
		$rawPostcode = $request->getDestPostcode ();
		$postCode = Mage::helper ('freight')->validatePostcode ($rawPostcode);
		if (empty ($postCode)) return $result;
	
		$packageWeight = $request->getPackageWeight ();
	
		$shipping = Mage::getModel ('freight/config')->getShippingPrice ($this->_code, $postCode, $packageWeight, $request);
		if ($shipping != null)
		{
            $shippingDeliveryType = $shipping->getData ('delivery_type');
			$shippingDeliveryPrice = $shipping->getData ('delivery_price') / 100;
			$shippingDeliveryTime = $shipping->getData ('delivery_time');
			
			$method = Mage::getModel('shipping/rate_result_method');
			$method->setCarrier($this->_code);
			$method->setCarrierTitle($this->getConfigData('title'));
			$method->setMethod($this->_code);
			$method->setMethodTitle($this->getConfigData('title') . ' - ' . Mage::helper ('freight')->formatShippingTime ($shippingDeliveryType, $shippingDeliveryTime));
			$method->setPrice($shippingDeliveryPrice);
			$method->setCost($shippingDeliveryPrice);
			$result->append($method);
		}
	
		return $result;
	}

	public function getAllowedMethods()
	{
		return array($this->_code=>$this->getConfigData('name'));
	}

	public function isTrackingAvailable ()
	{
		return true;
	}

	public function getTrackingInfo($tracking)
	{
	    $result = $this->getTracking($tracking);
	    if ($result instanceof Mage_Shipping_Model_Tracking_Result)
		{
	        if ($trackings = $result->getAllTrackings()) return $trackings[0];
	    }
		elseif (is_string($result) && !empty($result))
		{
	        return $result;
	    }

	    return false;
	}

	public function getTracking($trackings)
	{
	    $this->_result = Mage::getModel('shipping/tracking_result');
	    foreach ((array) $trackings as $code) $this->_getTracking($code);

	    return $this->_result;
	}

	protected function _getTracking ($code)
	{
		$tracking = Mage::getModel('shipping/tracking_result_status');
		$tracking->setTracking($code);
		$tracking->setCarrier($this->getConfigData('name'));
		$tracking->setCarrierTitle($this->getConfigData('title'));
	
		$url = Mage::getStoreConfig ('carriers/settings/tracking') . $code;
		try
		{
		    $client = new Zend_Http_Client();
		    $client->setUri($url);
		    $response = $client->request('GET');
		    $content = utf8_encode ($response->getBody ());
		    $correios_logo = Mage::getStoreConfig ('carriers/settings/correios_logo');
		    $correios_logo = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "gamuza/carriers/{$correios_logo}";
		    $styleBlock = "<style type='text/css'>table{border:1px solid #aaa;} hr{display:none;}</style>";
		    $content = $styleBlock . str_replace ("../correios/Img/correios.gif", $correios_logo, $content);
		}
		catch (Exception $e)
		{
		    $content = Mage::helper ('freight')->__('No tracking information available.');
		}

		$track ['status'] = $content;
		$tracking->addData($track);

		$this->_result->append($tracking);
		
		return true;
	}
}

