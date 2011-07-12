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

require_once('setup.php');
require_once('ticks_to_time.php');

$metrics = (get_metrics(get_deployment_id()));


// check to see if we need to scale and scale accordingly
switch (scale_check($metrics)) {
    case 1:
        // Add an instance
        echo "Scaling Out";
        $client->setInstanceCountBySlot(AZURE_WEBROLE_END, 'production', 'WebRole', get_num_roles('WebRole') + 1);
        break;
    case 0:
        // Do no add/remove an instances
        echo "Perfomance within acceptable range";
        break;
    case -1:
        // Remove an instance
        echo "Scaling in";
        $client->setInstanceCountBySlot(AZURE_WEBROLE_END, 'production', 'WebRole', get_num_roles('WebRole') - 1);
        break;
}

// Just so you can see what metrics are available :)
h($metrics);


function h($v) {
    echo '<pre>';
    var_dump($v);
    echo '</pre>';
}

function c($v) {
    print_r($v);
}


/**
 *
 * @param Array $metrics
 * @return Integer
 */
function scale_check($metrics) {
    $ret = 0;
    if(120 > $metrics['totals']['\TCPv4\Connections Established']['average']) {
        $ret = 1;
    } else if(20 > $metrics['totals']['\TCPv4\Connections Established']['average']) {
        $ret = -1;
    }
    return $ret;
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
	
    $s = $client->getDeploymentBySlot(AZURE_WEBROLE_END, 'production');
    //print_r($s->configuration);
	
	$xml = new SimpleXMLElement(mb_convert_encoding($s->configuration, "UTF-16"));
	foreach($xml->Role as $r) {
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
 * Retrieves all of the performance metric data over the $ago period
 * 
 * @param String $ago - Default: -5 minutes- Any valid Date/Time string
 * @return Object Array
 */
function get_metrics($deployment_id, $ago = "-15 minutes") {
    global $table;
    // get DateTime.Ticks in past
    $ago = str_to_ticks($ago);
    // build query
    $filter = "DeploymentId eq '$deployment_id' and  Role eq 'WebRole' and PartitionKey gt '0$ago'";
    $filter='';
    // run query
    $metrics = $table->retrieveEntities('WADPerformanceCountersTable', $filter);
    
    $arr = array();
    foreach ($metrics AS $m) {
        // Global totals
        $arr['totals'][$m->countername]['count'] = (!isset($arr['totals'][$m->countername]['count'])) ? 1 : $arr['totals'][$m->countername]['count'] + 1;
        $arr['totals'][$m->countername]['total'] = (!isset($arr['totals'][$m->countername]['total'])) ? $m->countervalue : $arr['totals'][$m->countername]['total'] + $m->countervalue;
        $arr['totals'][$m->countername]['average'] = (!isset($arr['totals'][$m->countername]['average'])) ? $m->countervalue : $arr['totals'][$m->countername]['total'] / $arr['totals'][$m->countername]['count'];
        
        
        // Totals by instance
        $arr[$m->roleinstance][$m->countername]['count'] = (!isset($arr[$m->roleinstance][$m->countername]['count'])) ? 1 : $arr[$m->roleinstance][$m->countername]['count'] + 1;
        $arr[$m->roleinstance][$m->countername]['total'] = (!isset($arr[$m->roleinstance][$m->countername]['total'])) ? $m->countervalue : $arr[$m->roleinstance][$m->countername]['total'] + $m->countervalue;
        $arr[$m->roleinstance][$m->countername]['average'] = (!isset($arr[$m->roleinstance][$m->countername]['average'])) ? $m->countervalue : ($arr[$m->roleinstance][$m->countername]['total'] / $arr[$m->roleinstance][$m->countername]['count']);
    }
    return $arr;
}


/**
 * Finds the id for the currently running deployment
 * @global type $client
 * @return String
 */
function get_deployment_id() {
	global $client;
	$s = $client->getDeploymentBySlot(AZURE_WEBROLE_END, 'production');
	return $s->PrivateId;
}