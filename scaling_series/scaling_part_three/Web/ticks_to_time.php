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

/*
echo "\n\nConverting DateTime.Ticks(634358163600000000) to PHP time format to date\n";
$ticks = ticks_to_time(634358163600000000);
echo $ticks;

echo "\nAnd back again\n";
echo time_to_ticks($ticks);

echo "\n\nNow in ticks\n";
echo time_to_ticks(time());

echo "\n\n10 minutes ago in ticks is\n";
echo str_to_ticks("-10 minutes");


echo "\n\n05-15-1986 as time\n";
echo strtotime("05/15/1986");

echo "\n\n05-15-1986 as ticks\n";
echo str_to_ticks("05/15/1986");
*/


/**
 * Converts from C# DateTime.Ticks to a Unix Timestamp
 * 
 * @param Integer $ticks
 * @return Integer 
 */
function ticks_to_time($ticks) {
        return (($ticks - 621355968000000000) / 10000000);
}

/**
 * Converts from a Unix Timestamp to C# DateTime.Ticks
 * @param Integer $time
 * @return Integer
 */
 function time_to_ticks($time) {
        return number_format(($time * 10000000) + 621355968000000000 , 0, '.', '');
}

/**
 * Converts a given string into C# DateTime.Ticks. Uses strings valid in PHP
 * 
 * @link http://php.net/manual/en/function.strtotime.php
 * @param String $str
 * @return Integer
 */
 function str_to_ticks($str) {
        return time_to_ticks(strtotime($str));
}
