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

$installer = $this;
$installer->startSetup();
$sqlBlock = <<<SQLBLOCK
CREATE TABLE IF NOT EXISTS gamuza_freights
(
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    website_id int(11) NULL,
    store_id int(11) NULL,
    carrier_id int(11) unsigned NOT NULL,
    begin_zip char(8) NOT NULL,
    end_zip char(8) NOT NULL,
    begin_weight int(11) unsigned NOT NULL,
    end_weight int(11) unsigned NOT NULL,
    delivery_time int(11) unsigned NOT NULL,
    delivery_price int(11) unsigned NOT NULL,
    PRIMARY KEY (id),
    KEY carrier_id (carrier_id),
    KEY website_id (website_id),
    KEY store_id (store_id),
    KEY begin_zip (begin_zip),
    KEY end_zip (end_zip),
    KEY begin_weight (begin_weight),
    KEY end_weight (end_weight),
    KEY delivery_time (delivery_time),
    KEY delivery_price (delivery_price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
ALTER TABLE gamuza_freights
    ADD CONSTRAINT gamuza_freights_ibfk_1 FOREIGN KEY (carrier_id) REFERENCES gamuza_carriers (id);
SQLBLOCK;
$installer->run($sqlBlock);
//demo
//Mage::getModel('core/url_rewrite')->setId(null);
//demo
$installer->endSetup();
