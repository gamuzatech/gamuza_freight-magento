<?php
/**
 * @package     Gamuza_Freight
 * @description This module offers freights functionality
 * @copyright   Copyright (c) 2018 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Library General Public
 * License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Library General Public License for more details.
 *
 * You should have received a copy of the GNU Library General Public
 * License along with this library; if not, write to the
 * Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
 * Boston, MA 02110-1301, USA.
 */

/**
 * See the AUTHORS file for a list of people on the Gamuza Team.
 * See the ChangeLog files for a list of changes.
 * These files are distributed with gamuza_blockchain-magento at http://github.com/gamuzatech/.
 */

/**
 * Used in creating options for DeliveryType config value selection
 *
 */
class Gamuza_Freight_Model_System_Config_Source_Delivery_Type
{
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray ()
    {
        $result = array(
            Gamuza_Freight_Helper_Data::DELIVERY_TYPE_MONTH  => Mage::helper ('freight')->__('Month'),
            Gamuza_Freight_Helper_Data::DELIVERY_TYPE_WEEK   => Mage::helper ('freight')->__('Week'),
            Gamuza_Freight_Helper_Data::DELIVERY_TYPE_DAY    => Mage::helper ('freight')->__('Day'),
            Gamuza_Freight_Helper_Data::DELIVERY_TYPE_HOUR   => Mage::helper ('freight')->__('Hour'),
            Gamuza_Freight_Helper_Data::DELIVERY_TYPE_MINUTE => Mage::helper ('freight')->__('Minute'),
        );

        return $result;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray ()
    {
        $result = array ();

        foreach ($this->toArray () as $value => $label)
        {
            $result [] = array ('value' => $value, 'label' => $label);
        }

        return $result;
    }
}

