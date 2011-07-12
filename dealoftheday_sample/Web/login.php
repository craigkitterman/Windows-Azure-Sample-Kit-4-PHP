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


if(!isset($_COOKIE['PHPSESSID'])) {
    // Setup and start the session
    $session_handler = new Microsoft_WindowsAzure_SessionHandler($table , 'Session');
    $session_handler->register();
    session_start();    
   // echo "session started for login";
}

// If the user has submitted the form with all the required elements let's insert it into the table
if(isset($_POST['Password'])) {
	$auth = $table->retrieveEntityById('Data', 'Data', 'Auth');
	if($auth->Value == $_POST['Password']) {
		$_SESSION['ValidUser'] = true;
		header("Location: adm_product_list.php");
	}	
}

require('templates/BuzzBee/login.php');