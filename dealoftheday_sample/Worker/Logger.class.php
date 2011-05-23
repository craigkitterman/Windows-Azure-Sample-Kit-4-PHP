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

require_once 'Microsoft/WindowsAzure/Storage/Table.php';

class Logger {
    
        /**
         *
         * @var Array
         */
        private $mLog;
		
	/**
	 * Default Constructor - Sets up object
	 */
	public function __construct() {
            // Initialize log
            $this->mLog = array();
	}
	
        /**
         *
         * @param String $Name
         * @param String $Value
         * @param String $Level - Uses PHPs built in log constants
         */
	public function Log($Name, $Value, $Level) {
            $now = time();
            $this->mLog[] = array('Name' => $Name, 'Value'=> $Value, 'Level'=>$Level, 'EntryTime'=>$now, 'HumanTime'=> date('H:m:s', $now));
        }
        
        /**
         * Add the log data to the Log table
         * @global WindowsAzureTable $table 
         */
        public function Submit() {
            global $table;
            
            $this->Log('Shutdown', 'Page build finished', LOG_INFO);
            foreach($this->mLog as $l) {
                $entry = new Log(str_replace('/', '.',RUNID), rand(0,time()));
                $entry->Name = $l['Name'];
                $entry->Value = $l['Value'];
                $entry->Level  = $l['Level'];
                $entry->HumanTime = $l['HumanTime'];
                $entry->EntryTime = $l['EntryTime'];
                $table->insertEntity('Log', $entry);
            }
        }
        
        public function ShowCurrent() {
            echo '<pre>'; print_r($this->mLog); echo '</pre>';
        }
}