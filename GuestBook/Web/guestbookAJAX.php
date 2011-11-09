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

    //This PHP script outputs the Guest Book Listing
    //It is designed to be called from an AJAX Call
     
    //Reference the PHP SDK for Azure scripts
    require 'Microsoft\WindowsAzure\Storage\Table.php';
    require 'GuestBookEntry.php';
    require 'guestbooktable.php';
    require 'constants.php';
    
    //prevent jQuery from caching the script
    header ("Cache-Control: no-cache, must-revalidate"); 
    
    //output the table
    DisplayGuestBookTable();
    
?>