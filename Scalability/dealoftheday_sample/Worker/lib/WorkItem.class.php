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
class WorkItem {
    
    private $mAction; 
    
    /**
     * Constructor
     * If parameters are present a new work item will be created on the worklist queue
     * 
     * @param String $action - Use constants with WL_ prefix
     * @param String $details 
     */
    function __construct($action = '', $details = '') {
        $this->mAction = $action;
        
        if($action != '' && $details != '') $this->Create($details);
    }
    
    function Create($details) {
        global $queue; die(print_r($details));
        $queue->putMessage('worklist', serialize(array('Action'=>$this->mAction, 'Details'=>  serialize($details))));
    }
    
    function Exists() {
        global $queue;
        return $queue->hasMessages('worklist');
    }
    
    function Fetch($num = 1) {
        global $queue;
        $i1 = $queue->getMessages('worklist', 1);
        $i = $i1[0];
        $queue->deleteMessage('worklist', $i1[0]);
        return $i;
    } 
}