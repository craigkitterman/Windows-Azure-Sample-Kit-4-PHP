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

function require_login() {
    if(!isset($_SESSION['ValidUser'])) {
	header("Location: login.php");
	exit();
    }
}

function site_paused() {
    global $table;
    $r = $table->retrieveEntityById('Data', 'Data', 'SiteStatus');
    if($r->Value == 'Paused') return true;
    return false;
}

/**
 * Pulls and calculates running times from the Log table
 * 
 * @todo Make it work
 * @global WindowAzureTable $table
 * @param String $run_id 
 */
function show_site_times($run_id) {
    global $table;
    
    $what = array('Storage Integrity Check');
    
}


function pause_site() { 
    global $table;
    $e = $table->retrieveEntityById('Data', 'Data', 'SiteStatus');
    
    $d = new Data('Data', 'SiteStatus');
    $d->Value = 'Paused';
    $table->updateEntity('Data', $d);
}
?>
