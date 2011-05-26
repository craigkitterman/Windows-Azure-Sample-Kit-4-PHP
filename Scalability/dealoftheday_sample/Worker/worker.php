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



//set error handler
//set_error_handler("customError");

require_once('include.php');

$work = new WorkItem('');

$timer->mark('start_perf_subscriptions');
// Ensure that all the roles currently running are submitting metrics information
// this may not work the first time the worker runs if the web roles are not yet running
// run it again till the web roles accept it
do {
    $again = false;
    try {
       add_perf_counters(find_role_instances_by_name('WebRole')); 
    } catch (Exception $e) { 
        echo "\nUnable to add performance counters on worker statup. Web roles may still be starting.";
        echo "\nTrying again in 10 seconds...";
        print_r($e);
        sleep(10);
        $again = true; 
    }
} while($again);
$timer->mark('stop_perf_subscriptions');

$lastRequestId = 0;
while(true) {
    $timer->mark('start_worker_loop');
    

    
    /*
     * START LOOKING FOR WORK TO Do
     */
    
    echo "\n\nLOOKING FOR WORK ITEMS";
    try {
       // Check the status of the site to determine if it should be handing out codes
       site_status_check();


        // See if there is anything on our worklist
        $timer->mark('start_work_check');
        if($work->Exists()) {
            // Do what the users wants
            $i = ($work->Fetch(1));

            $i = unserialize($i->messagetext);
           // print_r($i);
            // Figure out what the work items is and Do It!
            switch($i['Action']) {
                case WL_NEW_PROD:
                    new_product($i['Details']);
                    break;
                case WL_DEL_PROD:
                    delete_product($i['Details']);
                    break;
                case WL_NEW_COMMENT:
                    new_comment($i['Details']);
                    break;
                case WL_DEL_COMMENT:
                    delete_comment($i['Details']);
                    break;
                case WL_CODE_GIVEN:
                    // FILL IN
                    break;
                case WL_CODE_VENDOR_REDEEMED:
                    // FILL IN 
                    break;
            }
        }
	$timer->mark('end_work_check');
    } catch (Exception $e) { echo "\n**** Error somewhere in worklist queue check (worker.php:74) ***"; }
     
     
    /*
     * STOP LOOKING FOR WORK TO DO
     */

    
    
    
    /*
     * Start checking for load balancing
     */
    echo "\n\nBALANCING LOAD";
    $timer->mark('start_load_check');
    // Check load on Web roles. 
    // Increase or decrease instance count accordingly
    try {
        $timer->mark('start_get_load');
        $avg = averages(get_deployment_id()); // SLOW! calls and calculates from table
        $timer->mark('stop_get_load');
       // print_r($avg);
        $timer->mark('start_get_num_roles');
        $num_current_roles = get_num_roles('WebRole'); // VERY SLOW!! reads and parses XML - required to happen if using multiple workers
        $timer->mark('stop_get_num_roles');
        
        echo "\n - Average CPU usage: " . $avg['cpu'];
        echo "\n - Average Num connections: " . $avg['avg_connections_per_role'];
        echo "\n - Total connections across roles: " . $avg['total_connections'];
        echo "\n - Min connections to scale: 120";
        
       /* 
        $req_free_cpu = 70; // lowest % of free cpu allowed
        $free_cpu = round(100 - $avg['cpu']);//round((1 - ($avg / ($num_current_roles * 100)) * 100);
        $lower_threshold = 90; // % cpu free before removing an instance
        echo "\n - Free CPU: $free_cpu";
        echo "\n - Required Free CPU: $req_free_cpu";
        echo "\n - Role Removal when % CPU free: $lower_threshold";
        echo "\n - Number of running 'WebRole': $num_current_roles";
       */ 

	// Check to see if any process is already running. If so then we cannot change role counts		
        $timer->mark('start_get_op_status');
        $status = $client->getOperationStatus($lastRequestId);
        $timer->mark('stop_get_op_status');
		
        $timer->mark('start_update_perf_sub');
        // Update instance configuration to add performance metric subscriptions
        add_perf_counters(find_role_instances_by_name('WebRole'));
        $timer->mark('stop_update_perf_sub');
                
	if($status->Status != 'InProgress') {
            // Nothing found. Try updating the instance counts
            if($avg['avg_connections_per_role'] > 120 && $num_current_roles < MAX_WEBROLES) {
                // Too many connections to handle. Scale out
                $timer->mark('start_add_instance');
                echo "\n - LOAD HIGH! Creating new role";
                $client->setInstanceCountBySlot(AZURE_ROLE_END, 'production', 'WebRole', $num_current_roles + 5);
                $lastRequestId = $client->getLastRequestId();
                $timer->mark('stop_add_instance');

                
            } else if($avg['avg_connections_per_role'] < 20 && $num_current_roles > MIN_WEBROLES) {
                echo "\n - Low load. Removing role";
			$timer->mark('start_remove_instance');
			$client->setInstanceCountBySlot(AZURE_ROLE_END, 'production', 'WebRole', $num_current_roles - 1);
			$lastRequestId = $client->getLastRequestId();
			$timer->mark('stop_remove_instance');
            }
	   /* if($free_cpu < $req_free_cpu)  {
                        // HIGH LOAD, WE NEED NEW ROLES STAT!!!
			$timer->mark('start_add_instance');
			echo "\n - LOAD HIGH! Creating new role";
			$client->setInstanceCountBySlot(AZURE_ROLE_END, 'production', 'WebRole', $num_current_roles + 1);
			$lastRequestId = $client->getLastRequestId();
			$timer->mark('stop_add_instance');
                        
			$timer->mark('start_update_perf_sub');
			// Update instance configuration to add performance metric subscriptions
			add_perf_counters(find_role_instances_by_name('WebRole'));
			$timer->mark('stop_update_perf_sub');
                        
		} else if ($free_cpu > $lower_threshold && $num_current_roles > MIN_WEBROLES) {
			echo "\n - Low load. Removing role";
			$timer->mark('start_remove_instance');
			$client->setInstanceCountBySlot(AZURE_ROLE_END, 'production', 'WebRole', $num_current_roles - 1);
			$lastRequestId = $client->getLastRequestId();
			$timer->mark('stop_remove_instance');
		}*/
            
            } else {
                // Operation already running. Wait till next time
                echo "\n - Waiting for operation to complete before changing role count";
            }

    } catch (Exception $e) { echo "\n*** An error occurred somewhere while attempting to balance the load";  }
    $timer->mark('end_load_check');
    /*
    * Stop checking for load balancing
    */
    
    
   $timer->mark('stop_worker_loop'); 
   echo "\n** Loop executed in ".$timer->elapsed('start_worker_loop', 'stop_worker_loop')." seconds"; 
   sleep(LOOP_PAUSE);
}



/**
 * Adds a new comment to the comment table
 * 
 * @global WindowsAzureQueue $queue
 * @global WindowsAzureTable $table
 * @global Logger $logger
 * @param String $details 
 */
function new_comment($details) { 
    global $queue, $table;
    $details = unserialize($details);
   
    echo "\n- Inserting new comment";
    
    $c = new Comment($details->getRowKey());
    $c->setPartitionKey($details->getPartitionKey());
    $c->Name = $details->Name;
    $c->Text = $details->Text;
    $table->insertEntity('Comment', $c);
}

/**
 * Removes a comment from the comment table
 * 
 * @global WindowsAzureTable $table
 * @global Logger $logger
 * @global WindowsAzureQueue $queue
 * @param String $details 
 */
function delete_comment($details) {
     global $table,$queue;
    $details = unserialize($details);

    echo "\n- Removing a comment";
    $c = $table->retrieveEntityById('Comment', $details['PartitionKey'], $details['RowKey']);

    $table->deleteEntity('Comment', $c);
}
/**
 * Removes a product from the product table and code queue
 * 
 * @global WindowsAzureTable $table
 * @global Logger $logger
 * @global WindowsAzureQueue $queue
 * @param String $details
 */
function delete_product($details) {
    global $table,$queue;
    $details = unserialize($details);
 
    echo "\n- Deleting product: " . $details->Title;
    $table->deleteEntity('Product', $details);
    
    // Delete product from queue
    $run = true;
    while($run) {
        
        $i = $queue->peekMessages('code', 32); // max amount avaible to pull
        foreach($i as $e) {
            
            // Loops through queue items and delete any that are this product type
            $e = unserialize($e->messagetext);
           print_r($e);
            // If the product is not the same break out of the loop
            if($e['RowKey'] !=
                    $details->RowKey) { $run = false; break; }
            
            $queue->deleteMessage('code', $e);
        }
    }
}

/**
 *
 * @global WindowsAzureTable $table
 * @param String $details - Serialized object
 */
function new_product($details) {
    global $table;
    $details = unserialize($details);
    
    // Add product to product table
    $p = new Product();    
    $p->Title = $details->Title;
    $p->NumProducts = $details->NumProducts;
    $p->Description = $details->Description;
    $p->StartDate = $details->StartDate;
    $p->EndDate = $details->EndDate;
    $p->Image = $details->Image; 
    $p->ValidDays = $details->ValidDays;
    $table->insertEntity('Product', $p);  
    echo "\n- Added product: " . $p->Title;
    // Add product to code queue
    fill_new_product_queue($p);
}

/**
 *Fills the code queue based on the provided Product entity and adds code
 * export data to the Data table
 * 
 * @global WindowsAzureQueue $queue
 * @global type $logger
 * @global WindowsAzureTable $table
 * @global type $timer
 * @param Product $p 
 */
function fill_new_product_queue($p) { 
    global $queue,$table, $timer;

    $i = $p->NumProducts;
    $times = array(); // holds the times codes have been created for
    $export = array(); // holds codes to be exported via csv
    $timer->mark('start_build_prod_codes');
    while($i > 0) { //echo "\nLoop: $i/" . $p->NumProducts;
        $t = rand(strtotime($p->StartDate), strtotime($p->EndDate));
        
        /*
         * Need to check these failing conditions.
         * If they exists create a new code
         * 
         * $t = new time
         * $times = list of used times
         * 
         * $t already in $times
         * not in $valid_days
         * 
         */
       // echo "\nI am in the array: "; var_dump(in_array(date('D', $t));
        if(in_array($t, $times)  || !in_array(date('D', $t), $p->ValidDays)|| date('G', $t) < 8 || date('G', $t) > 15) { //echo "\ncontinueing";
            continue;
        }
        
        /*
         * Otherwise no conflict has been found. Free to use this time
         * Add it to the $times array
         */
        $times[] = $t;
        
        /*
         * Now that we have a time we can add the new redemption code to the db.
         * NOTE that sometimes this fails to authenticate. No idea why so I am
         * doing some very hacky "trapping" of this dumbness
         */
        $code = generate_code($p->StartDate, $p->EndDate);
        $a = array('PartitionKey' => $p->getPartitionKey(),
                       'RowKey' => $p->getRowKey(),
                       'Code' => $code,
                       'Valid' => $t,
                       'StartDate' => $p->StartDate,
                       'EndDate' => $p->EndDate,
                       'ValidDays' => $p->ValidDays
                      );
        $try = 1; // Watch how many tries it has taken to insert this code to the queue
        do {
            try {
                $again = false;
                $queue->putMessage('code', serialize($a));
            } catch (Exception $e) {
                echo "\nFailed to insert product code to queue on try $try";
                $try++;
                $again = true;
            }
       } while($again);
       
       /*
        * Setup export information so codes can easily be given to vendor
        */
       $export[] = array('ProductId'=>$p->getRowKey(), 'Valid' => $t, 'Code' => $code, 'StartDate' => $p->StartDate, 'EndDate' => $p->EndDate, 'ValidDays' => $p->ValidDays);
    
        /*
         * Reduce the counter or DIE!!MUAUAHAHA
         */
        $i--; 
    }
    $timer->mark('stop_build_prod_codes');

    
    $timer->mark('start_insert_export');
    /*
     * Store the export data in the Data table in the ProductExport partition
     */
       $try = 1; // Watch how many tries it has taken to insert this code to the queue
       do {
           $again = false;
           try {
               
               $d = new Data('ProductExport', $p->getRowKey());
               $d->Value = serialize($export);
               $table->insertEntity('Data', $d);
               echo "\n --- Inserted code export data into table";
           } catch(Exception $e) {
               echo "\nFailed to insert code export to table on try $try";
                $try++;
                $again = true;
           }
       } while($again);
       $timer->mark('stop_insert_export');
}


/**
 * Creates a new redemption code
 *
 * In it's own function in case we want to get fancy
 *
 * @return String
 **/
function generate_code($start, $end) {
	        $md5a = md5($start . rand(0, time()));
                $md5e = md5($end);
                
                return md5($md5a . $md5e);
}