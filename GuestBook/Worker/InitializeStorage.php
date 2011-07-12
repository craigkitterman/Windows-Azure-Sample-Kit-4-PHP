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

require_once 'constants.php';
require_once 'Microsoft\AutoLoader.php';

//Initialize the Azure Storage
function InitializeStorage()
{               
    if (USE_DEV_EMULATOR){
        //Connect to the Storage Emulator
        $tableClient = new Microsoft_WindowsAzure_Storage_Table(); 
        $blobClient = new Microsoft_WindowsAzure_Storage_Blob();
        $queueClient = new Microsoft_WindowsAzure_Storage_Queue();
    }
    else {
        //Connect to Azure storage
        $tableClient = new Microsoft_WindowsAzure_Storage_Table
                        (AZURE_TABLES_URL,
                        AZURE_STORAGE_ACCOUNT,
                        AZURE_STORAGE_KEY);
        $blobClient = new Microsoft_WindowsAzure_Storage_Blob
                        (AZURE_BLOBS_URL, 
                        AZURE_STORAGE_ACCOUNT,
                        AZURE_STORAGE_KEY);
        $queueClient = new Microsoft_WindowsAzure_Storage_Table
                        (AZURE_QUEUES_URL,
                        AZURE_STORAGE_ACCOUNT,
                        AZURE_STORAGE_KEY);
        }  
    
    //Create a table to store GuestBook Entries if it doesn't exist          
    if(!$tableClient->tableExists(GB_TABLE_NAME))
        $result = $tableClient->createTable(GB_TABLE_NAME);
    
    //Validate the blob container name
    if($blobClient->isValidContainerName(GB_BLOB_CONTAINER)) 
    { 
        if(!$blobClient->containerExists(GB_BLOB_CONTAINER)) 
        { 
            //create it if it doesn't exist
            $result = $blobClient->createContainer(GB_BLOB_CONTAINER); 
            //Make it public
            $blobClient->setContainerAcl(GB_BLOB_CONTAINER, 
                                         Microsoft_WindowsAzure_Storage_Blob::ACL_PUBLIC); 
            $blobClient->registerStreamWrapper();
        } 
    } 
                    
    //Create a queue to send thumbnail jobs to a worker process if it doesn't exist          
    $queue = $queueClient->createQueueIfNotExists(GB_QUEUE_NAME);
        
    return array ($tableClient, $blobClient, $queueClient);
    
}//End InitializeStorage


?>