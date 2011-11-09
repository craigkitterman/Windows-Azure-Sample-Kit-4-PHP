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

    //Outputs the Guest Book from Azure Table Storage...
    function DisplayGuestBookTable()
    {
        //Initialize Table Storage
        if (USE_DEV_EMULATOR){
            //Connect to the Storage Emulator
            $tableClient = new Microsoft_WindowsAzure_Storage_Table(); 
        }
        else {
            //Connect to Azure storage
            $tableClient = new Microsoft_WindowsAzure_Storage_Table
                            (AZURE_TABLES_URL,
                            AZURE_STORAGE_ACCOUNT,
                            AZURE_STORAGE_KEY);
        }
        
        //Create a table to store GuestBook Entries if it doesn't exist          
        if(!$tableClient->tableExists(GB_TABLE_NAME))
            $result = $tableClient->createTable(GB_TABLE_NAME);
        
        //Construct a query that will look for guest book entries with the current date.    
        $nowDT = new DateTime('now', new DateTimeZone('UTC'));
        $partitionKey = $nowDT->format("mdY");
        $query = "PartitionKey eq '".$partitionKey."'"; 

        echo '<table id="gbEntryTable" cellspacing="0" border="0" style="border-collapse:collapse;">';
        
        $entries = $tableClient->retrieveEntities("GuestBookEntry", $query, "GuestBookEntry"); 
        foreach($entries as $entry) 
        {     
                echo '<tr><td>';
                echo '<div class="signature">';
                echo '    <div class="signatureImage">';
                echo '        <a href="'.$entry->PhotoUrl.'" target="_blank">';
                echo '            <img src="'.$entry->ThumbnailUrl.'"'; 
                echo '                alt="'.$entry->GuestName.'" />';
                echo '        </a>';
                echo '    </div>';
                echo '    <div class="signatureDescription">';
                echo '        <div class="signatureName">';
                echo $entry->GuestName;
                echo '        </div>';
                echo '        <div class="signatureSays">';
                echo 'says';
                echo '        </div>';
                echo '        <div class="signatureDate">';
                echo $entry->getTimestamp()->format('Y-m-d H:i:s');
                
                
                echo '        </div>';
                echo '        <div class="signatureMessage">';
                echo '"'.$entry->Message.'"';
                echo '        </div>';
                echo '    </div>';
                echo '</div>';
                echo '</td></tr>';
        }
        
        echo '</table>';
     }//end DisplayGuestBookEntries

?>