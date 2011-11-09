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


