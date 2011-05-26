<?php
/**
 *    Copyright 2011 Microsoft Corporation
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * @category  Microsoft
 * @package   DealOfTheDay
 * @author    Ben Lobaugh <a-beloba@microsoft.com>
 * @copyright 2011 Copyright Microsoft Corporation. All Rights Reserved
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 **/
require_once('include.php');
require_login();

if(isset($_GET['r'])) $p = $table->retrieveEntityById('Product', 'Product', $_GET['r']);

if(isset($_GET['action']) && $_GET['action'] == 'Delete') {
    
    new WorkItem(WL_DEL_PROD, $p);
    header("Location: adm_product_list.php");
}

if(isset($_POST['Cancel']) && $_POST['Cancel'] == 'Cancel') header("Location: adm_product_list.php");

if(isset($_POST['Title']) && $_POST['Title'] != '') { 
    // User wants to insert a new product. Do it
    $p = new Product();
    
    $image = $blob->putBlob('product', $_POST['Title'], $_FILES['Image']['tmp_name']);
    
    $p->Title = $_POST['Title'];
    $p->NumProducts = $_POST['NumProducts'];
    $p->Description = $_POST['Description'];
    $p->StartDate = $_POST['StartDate'];
    $p->EndDate = $_POST['EndDate'];
    $p->ValidDays = $_POST['ValidDays'];
    $p->Image = $image->Url;
    
    $w = new WorkItem(WL_NEW_PROD, $p);
   
    
    
}

require_once('templates/BuzzBee/adm_product.php');