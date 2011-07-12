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
 * @package   PHPScalingSamples
 * @author    Ben Lobaugh <a-beloba@microsoft.com>
 * @copyright 2011 Copyright Microsoft Corporation. All Rights Reserved
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 **/

// Setup settings
define('PRODUCTION_SITE', false);
define('AZURE_STORAGE_KEY', ''); // Storage Primary Key
define('AZURE_SERVICE', ''); // Storage Endpoint
define('PERF_IN_SEC', 30); // How many seconds to check performance
 define('ROLE_ID', $_SERVER['RoleDeploymentID'] . '/' . $_SERVER['RoleName'] . '/' . $_SERVER['RoleInstanceID']);
 
  define('SUB_ID', ''); // Service subscription id
 define('CERT_KEY', ''); // Certificate key (thumbprint)
 define('CERT', ''); // Full location of certificate file
 
define('MIN_WEBROLES', 2); // Minimum web role instances to run at all times
define('MAX_WEBROLES', 20); // Max web roles to run at all time. You MUST limit the max to prevent economic denial attacks
 define('LOOP_PAUSE', 10); // How long should the worker pause between loops
 
   /** Microsoft_WindowsAzure_Storage_Blob */
require_once 'Microsoft/WindowsAzure/Storage/Blob.php';
/** Microsoft_WindowsAzure_Diagnostics_Manager **/
require_once 'Microsoft/WindowsAzure/Diagnostics/Manager.php';

/** Microsoft_WindowsAzure_Storage_Table */
require_once 'Microsoft/WindowsAzure/Storage/Table.php';
if(PRODUCTION_SITE) {
	$blob = new Microsoft_WindowsAzure_Storage_Blob(
		'blob.core.windows.net',
		AZURE_SERVICE,
		AZURE_STORAGE_KEY
	);

        $table = new Microsoft_WindowsAzure_Storage_Table(
              'table.core.windows.net',
              AZURE_SERVICE,
              AZURE_STORAGE_KEY
	);
} else {
	// Connect to local Storage Emulator
	$blob = new Microsoft_WindowsAzure_Storage_Blob();
        $table = new Microsoft_WindowsAzure_Storage_Table();
}
$manager = new Microsoft_WindowsAzure_Diagnostics_Manager($blob);

$client = new Microsoft_WindowsAzure_Management_Client(                     
        SUB_ID,                      
        CERT,                      
        CERT_KEY             
);