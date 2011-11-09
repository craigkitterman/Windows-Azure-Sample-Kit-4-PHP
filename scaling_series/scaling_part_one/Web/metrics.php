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

// Bring in global include file 
require_once('setup.php');

// Performance counters to subscribe to 
$counters = array( 					
    '\Processor(_Total)\% Processor Time', 					
    '\TCPv4\Connections Established', 				
); 


// Retrieve the current configuration information for the running role 
$configuration = $manager->getConfigurationForRoleInstance(ROLE_ID);

// Add each subscription counter to the configuration 
foreach($counters as $c) { 	
    $configuration->DataSources->PerformanceCounters->addSubscription($c, PERF_IN_SEC); 
}

// These settings are required by the diagnostics manager to know when to transfer the metrics to the storage table 
$configuration->DataSources->OverallQuotaInMB = 10; 
$configuration->DataSources->PerformanceCounters->BufferQuotaInMB = 10; 
$configuration->DataSources->PerformanceCounters->ScheduledTransferPeriodInMinutes = 1; 

// Update the configuration for the current running role 
$manager->setConfigurationForRoleInstance(ROLE_ID,$configuration);