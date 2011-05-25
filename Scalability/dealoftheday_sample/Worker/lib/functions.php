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
/**
 * File containing commonly used functions
 *
 */

/**
 * Convert C# DateTime.Ticks to Unix timestamp
 *
 * @param Integer $ticks
 * @return Integer
 **/
function ticks_to_time($ticks) {
	return (($ticks - 621355968000000000) / 10000000);
}


/**
 * Convert Unix timestamp to C# DateTime.Ticks
 *
 * @param Integer $time
 * @return Integer
 **/
function time_to_ticks($time) {
	return number_format(($time * 10000000) + 621355968000000000 , 0, '.', '');
}

/**
 * Convert string to C# DateTime.Ticks
 *
 * @param String $str - Accepts any valid PHP date string
 * @return Integer
 **/
function str_to_ticks($str) {
	return time_to_ticks(strtotime($str));
}

/**
 * Returns the average CPU over the past Nx
 * N = number of units
 * x = unit (minutes, hours, days, years, etc)
 *
 * @example average_cpu("-5 minutes")
 * @param String $ago
 * @return Float
 **/
function averages($deployment_id) { 
	global $table;
	 $ago = str_to_ticks("-5 minutes");
         $one_min = str_to_ticks("-1 minute");
	$info = '';
	try {
		$filter = "DeploymentId eq '$deployment_id' and  Role eq 'WebRole' and PartitionKey gt '0$ago'";
		//$filter = "Role eq 'WebRole'";
                //$filter = '';
		//echo "\nFilter: $filter";
		$info = $table->retrieveEntities('WADPerformanceCountersTable', $filter); 
                
	} catch(Exception $e) { echo "\nAn error ocurred while retrieving the entities"; }
	
	$sum = 0;
	$i = 0; $x = 0;
        $total_cons = 0;
        $role_cons = array();
        $rx = array();
	//var_dump($info);
	foreach($info as $c) { //echo "\nChecking " . $c->CounterName . " with " . $c->CounterValue;
		if($c->CounterName == '\Processor(_Total)\% Processor Time') {
			//echo "\nChecking " . $c->CounterName . " with " . $c->CounterValue;
			$sum += $c->CounterValue;
			$i++;
		}
                if($c->CounterName == '\TCPv4\Connections Established') {
                    //echo "\nChecking " . $c->CounterName . " with " . $c->CounterValue;
                    $total_cons += $c->CounterValue;
                    if (!isset($role_cons[$c->RoleInstance])) $role_cons[$c->RoleInstance] = $c->CounterValue;
                    else $role_cons[$c->RoleInstance] += $c->CounterValue;
                    if(!isset($rx[$c->RoleInstance])) { $rx[$c->RoleInstance] = 1; }
                    else { $rx[$c->RoleInstance] += 1; }
                    $x++;
                }
                
	}
        
        $overall_role_avg = 0;
        $rc = 0;
        $ravg = 0;
        foreach($role_cons AS $k => $v) {
            $role_cons[$k] = $v / $rx[$k];
            $ravg += $role_cons[$k];
            $rc++;
        }
        
        $total_cons = $ravg;
        $ravg = $ravg / $rc; 
        
        $arr = array('cpu' => $sum / $i, 'total_connections' => $total_cons, 'avg_connections_per_role' => $ravg, 'roles' => $role_cons);
        //print_r($arr);
       
	return $arr;
}


/**
 * Returns the current number of running roles by role name
 * 
 * @global WAZ Management Client $client
 * @param String $roleName
 * @return Integer 
 */
function get_num_roles($roleName) {
    global $client;
    $ret = 0;
    $is_role = false;
	
    $s = $client->getDeploymentBySlot(AZURE_ROLE_END, 'production');
    //print_r($s->configuration);
	
	$xml = new SimpleXMLElement(mb_convert_encoding($s->configuration, "UTF-16"));
	foreach($xml->Role as $r) {
		//$s = ($r['Instances']);
		
		foreach($r->attributes() as $a=>$b) {
			//echo "\na: $a b: $b";
			if($a == 'name' && $b == $roleName) $is_role = true;
		}
		
		foreach($r->Instances->attributes() as $a=>$b) {
			//echo "\na: $a b: $b";
			if($is_role) return $b;
		}
	}
}

/**
 *  Add the performance counter subscriptions to the specified role. A list of
 * roles may be passed in using an array
 * 
 * @global type $manager
 * @global type $perf_counters
 * @param Mixed $role - (String|Array)
 */
function add_perf_counters($role) {
	global $manager, $perf_counters;
	//var_dump($role);
	if(is_array($role)) {
		// recursive
		foreach($role as $r) {
			add_perf_counters($r);
		}
	}
	
	$configuration = $manager->getConfigurationForRoleInstance($role);
 
	foreach($perf_counters as $c) {
		$configuration->DataSources->PerformanceCounters->addSubscription($c, PERF_IN_SEC);
	}
	$configuration->DataSources->OverallQuotaInMB=10;
	$configuration->DataSources->PerformanceCounters->BufferQuotaInMB=10;
	$configuration->DataSources->PerformanceCounters->ScheduledTransferPeriodInMinutes=1;


	$manager->setConfigurationForRoleInstance($role,$configuration);
}


/**
 * Finds all the instances by the overal webrole name.
 * 
 * Instances are usually named by <RoleName>_IN_#
 * 
 * @global type $client
 * @param String $roleName
 * @return Array  
 */
function find_role_instances_by_name($roleName) {
	global $client;

	$s = $client->getDeploymentBySlot(AZURE_ROLE_END, 'production');
	$arr = array();
	$list = $s->roleinstancelist;
	foreach($list as $i) {
		//var_dump ($i); break;
		if($i['rolename'] == $roleName) {
			$dep_id = $s->privateid;
			$arr[] = $dep_id . "/$roleName/" . $i['instancename'];
		}
	}
	
	return $arr;
}

function get_deployment_id() {
	global $client;
	$s = $client->getDeploymentBySlot(AZURE_ROLE_END, 'production');
	return $s->PrivateId;
}

function pause_site() {
    global $table;
    $e = $table->retrieveEntityById('Data', 'Data', 'SiteStatus');
    
    $d = new Data('Data', 'SiteStatus');
    $d->Value = 'Paused';
    $table->updateEntity('Data', $d);
}

function resume_site() {
    global $table;
    $e = $table->retrieveEntityById('Data', 'Data', 'SiteStatus');
    
    $d = new Data('Data', 'SiteStatus');
    $d->Value = 'Running';
    $table->updateEntity('Data', $d);
}


/**
 * Check the status of the site. If it is the end of the work week the site willpause,
 * if it is the beginning of the work week the site will unpause(resume)
 * 
 * @return Boolean - True if site is not paused
 */
function site_status_check() {
    $now = time();
    $ret = true;
    
    if(date('D', $now) == 'Fri' && date('H', $now) >= PROD_END_HOUR) {
        // End of the work week. Pause the site
        pause_site();
        $ret = false;;
    } else if(date('D', $now) == 'Mon' && date('H', $now) >= 8) {
        // Beginning of the work week. Resume the site
        resume_site();
    }
    return 1;
}