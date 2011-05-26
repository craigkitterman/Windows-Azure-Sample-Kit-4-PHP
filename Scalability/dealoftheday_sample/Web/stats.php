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
 * Current Item
 *  - How many this hour?
 *  - How many total?
 * 
 * System
 *  - How many roles are running?
 *  - CPU usage
 *  - Num connections
 */

require_once('include.php');


$current_prod = 0;
// Figure out what the current product is
 try {
    // Find the current product number
    $game = $queue->peekMessages('code', 1);
    if(!isset($game[0])) throw new Exception();
    $game = unserialize($game[0]->messagetext);
    $current_prod = $game['RowKey'];
} catch (Exception $e) { /* Right now I do not really care */ }



$p = 0; // temp product id to help dilineate
$num_diff_products = 0;
$total_products = 0;
$today_products = 0;
$total_curr_product = 0;
$total_hour = 0;
$today = strtotime(date('Y-m-d 2am'));
$last_hour = strtotime('-1 hour');

/**
 * Get the Redemption table infos
 * @todo Handle the continuation token
 */
try {
    // Gather stats from the table storage
    $filter = '';
    $data = $table->retrieveEntities('Redemption', $filter);
    
    foreach($data AS $d) {
        $total_products++; // Increment total number of redemption codes
        
        // Check to see if code was given today
        if($d->timestamp->getTimestamp() > $today) {
            $today_products++;
        }
        
        // Check to see if the code was give in the last hour
        if($d->timestamp->getTimestamp() > $last_hour) {
            $total_hour++;
        }
        
        if($d->getRowKey() != $p){
            $num_diff_products++;
        }
        
    }
    
} catch (Exception $e) {
    $q_err_msg = "Hmm, seems I could not calculate stats for the current item. That either means there is not currently a product up for grabs, or the boogey man got in here and is messing with my bits!";

}

echo "Prize codes given:<br/>";
echo "Last Hour: $total_hour<br/>";
echo "Today: $today_products<br/>";
echo "Total codes: $total_products";




 try {
        $avg = averages(get_deployment_id()); // SLOW! calls and calculates from table
        
        echo "<br/> - Average CPU usage: " . $avg['cpu'];
        echo "<br/> - Average Num connections: " . $avg['avg_connections_per_role'];
        echo "<br/> - Total connections across roles: " . $avg['total_connections'];
       
    

    } catch (Exception $e) { echo "\n*** An error occurred somewhere while attempting to gather the load metrics";  }