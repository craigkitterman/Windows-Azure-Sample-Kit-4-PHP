<?php
/*
Copyright 2011 Microsoft Corporation

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/


class GuestBookEntry extends Microsoft_WindowsAzure_Storage_TableEntity
{
    //PHP docblock comments are used to map PHP types to Azure storage types
    
    /**
    * @azure Message
    */
	public $Message;
    /**
    * @azure GuestName
    */	
    public $GuestName;
    /**
    * @azure PhotoUrl
    */    
    public $PhotoUrl;
    /**
    * @azure ThumbnailUrl
    */    
    public $ThumbnailUrl;
    
    function __construct() {

        //Set the partition key to today's date in "mmddyyyy" format
        $nowDT = new DateTime('now', new DateTimeZone('UTC'));
        $partitionKey = $nowDT->format("mdY");
           
        //Build a row key that is the current date in seconds from the Epoch (negated) 
        //with a GUID appended to it.
        
        $nowSecondsFromEpoch = $nowDT->format("U");         
        $guid = trim(com_create_guid(), '{}');
 
        
        $rowKey = $nowSecondsFromEpoch . '_' . $guid;
        
        $this->_partitionKey = $partitionKey;
        $this->_rowKey       = $rowKey;
        
    }
    
}

?>