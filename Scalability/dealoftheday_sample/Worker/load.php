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

add_perf_counters(find_role_instances_by_name('WebRole'));

while(true) {

 try {
        $avg = averages(get_deployment_id()); // SLOW! calls and calculates from table
        
        echo "\n - Average CPU usage: " . $avg['cpu'];
        echo "\n - Average Num connections: " . $avg['avg_connections_per_role'];
        echo "\n - Total connections across roles: " . $avg['total_connections'];
        echo "\n***********************************************\n";
       
    

    } catch (Exception $e) { echo "\n*** An error occurred somewhere while attempting to gather the load metrics";  }
	sleep(10);

}