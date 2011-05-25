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



/*
 * This file sets up all the common functionality for
 * the website, EG: storage settings, table, blob, etc
 */
 define('PRODUCTION_SITE', true);
 define('AZURE_STORAGE_KEY', ''); // Storage Primary Key
 define('AZURE_SERVICE', ''); // Storage Endpoint
 define('AZURE_ROLE_END', ''); // Web Endpoint
 define('SUB_ID', ''); // Service subscription id
 define('CERT_KEY', ''); // Certificate key (thumbprint)
 define('CERT', ''); // Full location of certificate file
 
define('MIN_WEBROLES', 5); // Minimum web role instances to run at all times
define('MAX_WEBROLES', 20); // Max web roles to run at all time. You MUST limit the max to prevent economic denial attacks
 
 
define('PROD_START_HOUR', 8); // Hour to unpause game
define('PROD_END_HOUR', 13); // Hour to pause game

define('PERF_IN_SEC', 30); // How many seconds to check performance

define('LOOP_PAUSE', 10); // How long should the worker pause between loops