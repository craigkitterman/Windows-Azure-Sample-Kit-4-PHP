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

    // DISCLAIMER - This code is written by a total PHP N00b! 
    // I guarantee that it is NOT following best PHP coding patterns and practices!!
    // There is NO user input validation.  There is NO error handling.  There are NO security measures taken!
    // Please don't laugh me off the stage.  
    // However, any and all simple & obvious crowdsourced refactoring IS entirely welcomed!
    
    //Reference the PHP SDK for Azure scripts
    require_once 'Microsoft\WindowsAzure\Storage\Table.php';
    require_once 'Microsoft\WindowsAzure\Storage\Blob.php';
    require_once 'Microsoft\WindowsAzure\Storage\Queue.php';
    
    //Reference the script defining a Guest Book Entry entity
    require 'constants.php';
    require 'GuestBookEntry.php';
    require 'InitializeStorage.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Guest Book Manager</title>
        <link href="main.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery/jquery-1.5.1.min.js" type="text/javascript"></script>
	<script type="text/javascript">
        
    function DeleteButton_Click(pKey, rKey) {
        $("#partitionKey").val(pKey);
        $("#rowKey").val(rKey);
    	document.forms['DeleteEntity'].submit();
    }
        
    </script>
    <div class="general">
        <div class="title">
            <h1>
                Windows Azure PHP GuestBook Manager
            </h1>
        </div>
 
        <div id="DeleteForm">
            <form id="DeleteEntity" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
                <input id="partitionKey" name="partitionKey" type="hidden" value=""/> 
                <input id="rowKey" name="rowKey" type="hidden" value="" />
            </form>
        </div>
            
        <?php
        
            list($tableClient, $blobClient, $queueClient) = InitializeStorage();  
            
            //If this is a Postback...
        	if (!empty($_POST))
            { 	
                //delete whatever entity was selected
                echo 'attempting to get the entity you want to delete...</br>';
                echo 'partitionKey: '.$_POST['partitionKey'].'</br>';
                echo 'rowKey: '.$_POST['rowKey'].'</br></br>';
                $guestBookEntry = $tableClient->retrieveEntityById('GuestBookEntry', $_POST['partitionKey'], $_POST['rowKey'], 'GuestBookEntry'); 
                echo 'got the entity:</br>';
                echo 'name: '.$guestBookEntry->GuestName.'</br>';
                echo 'message: '.$guestBookEntry->Message.'</br>';
                echo 'PhotoUrl: '.$guestBookEntry->PhotoUrl.'</br>';
                echo 'thumbnailURL: '.$guestBookEntry->ThumbnailUrl.'</br></br>';
                
                //get file name from the URLs of the two image blobs
                $photoBlobName = substr($guestBookEntry->PhotoUrl,strrpos($guestBookEntry->PhotoUrl,"/")+1);
                $thumbnailBlobName = substr($guestBookEntry->ThumbnailUrl,strrpos($guestBookEntry->ThumbnailUrl,"/")+1);

                
                //Delete the images from Blob storage first!
                echo 'Deleting photo & thumbnail blobs from storage...</br>';
                $blobClient->deleteBlob(GB_BLOB_CONTAINER,$photoBlobName);
                $blobClient->deleteBlob(GB_BLOB_CONTAINER,$thumbnailBlobName);
                echo 'Blobs deleted!</br></br>';
                
                echo 'now trying to delete the entity...</br>';
                $tableClient->deleteEntity('GuestBookEntry', $guestBookEntry);
                echo 'hopefully it was deleted!</br>';
            }
            echo '<div id="theResults">';
            DisplayGuestBookEntries($tableClient);
            echo '</div>';
        ?>
        
     <?php
                  
            //Outputs the Guest Book from Azure Table Storage...
            function DisplayGuestBookEntries($tableClient)
            {
                $nowDT = new DateTime('now', new DateTimeZone('UTC'));
                $partitionKey = $nowDT->format("mdY");
                $query = "";//"PartitionKey eq '".$partitionKey."'"; 
                
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
                        echo '        <div class="editControls">';
                        echo '<input type="button" value="Delete Entry" onclick="DeleteButton_Click(\''.$entry->getPartitionKey().'\',\''.$entry->getRowKey().'\')" />';
                        echo '        </div>';
                        echo '    </div>';
                        echo '</div>';
                        echo '</td></tr>';
                }
             }//end DisplayGuestBookEntries
        ?>
    </body>
</html>
