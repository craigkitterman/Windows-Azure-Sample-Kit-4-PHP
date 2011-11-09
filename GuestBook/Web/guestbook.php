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
// I GUARANTEE that it is NOT following best PHP coding patterns and practices!!
// There is NO user input validation.  There is NO error handling.  There are NO security measures taken!
// Please don't laugh me off the stage.  
// However, any and all simple & obvious crowdsourced refactoring IS entirely welcomed!


//Reference the PHP SDK for Azure scripts
require 'Microsoft\WindowsAzure\Storage\Table.php';
require 'Microsoft\WindowsAzure\Storage\Blob.php';
require 'Microsoft\WindowsAzure\Storage\Queue.php';

//Reference the script defining a Guest Book Entry entity
require 'GuestBookEntry.php';
require 'guestbooktable.php';
require 'constants.php';
require 'InitializeStorage.php';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
    <title>Windows Azure Guestbook</title>
    <link href="main.css" rel="stylesheet" type="text/css" />
   
</head>
<body>
     <script src="http://ajax.aspnetcdn.com/ajax/jquery/jquery-1.5.1.min.js" type="text/javascript"></script>
	 <script type="text/javascript">
    
    function SignButton_Click() {
    	document.forms['gbForm'].submit();
    }
    
    window.setInterval(refreshGuestBookEntries, 10000);
    
    function refreshGuestBookEntries() 
    { 
        $('#UpdatePanel').load("guestbookAJAX.php");
    }
    
    </script>
    <div class="general">
        <div class="title">
            <h1>
                Windows Azure GuestBook
            </h1>
        </div>
        <div class="inputSection">
        <form id="gbForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <dl>
                <dt>
                    <span id="NameLabel">Name:</span></dt>
                <dd>
                    <input type="text" 
                       id="NameTextBox"
                       name="NameTextBox" 
                       class="field"/>
                </dd>
                <dt>
                    <span id="MessageLabel">Message:</span>
                </dt>
                <dd>
                    <textarea name="MessageTextBox" rows="2" cols="20" id="MessageTextBox" class="field" ></textarea>         
                </dd>
                <dt>
                    <span id="FileUpload1">Photo:</span></dt>
                <dd>
                	<input type="file" name="blobfile" id="blobfile" size="16" value="" />
                </dd>
            </dl>
            <div class="inputSignSection">
                <img src="sign.png" align="bottom" alt="Sign Guestbook" onclick="SignButton_Click()" />
            </div>
            </form>
        </div>
        
        <?php
            
            //If this is a Postback...
        	if (!empty($_POST))
            { 	   	
                //Initialize the Azure Storage Clients
                list($tableClient, $blobClient, $queueClient) = InitializeStorage();  
                        
                //Check to see if a file was POSTed
                if (is_uploaded_file($_FILES['blobfile']['tmp_name']))
                {
                    //Was the file a .JPG or .PNG?
                    if (($_FILES['blobfile']['type'] != "image/png") && ($_FILES['blobfile']['type'] != "image/jpeg"))
                    {
                        //NO? Display an error message and do not display the guest book!
                        echo 'SORRY! The guestbook is only accepting JPG or PNG images at this time. ';
                        echo 'Check back later to see if we are accepting malicious payloads.';
                             
                        //Game over!
                    }
                    else
                    {
                        //Create a unique name for the image to be stored in Azure Blob Storage
                        $guid = trim(com_create_guid(), '{}');
                        if ($_FILES['blobfile']['type'] === "image/jpeg")
                            $extension = '.jpg';
                        if ($_FILES['blobfile']['type'] === "image/png")
                            $extension = '.png';
                        
                        $blobName = 'image_'.$guid.$extension;
                        
                        //Now actually upload the file to Azure Blob Storage
                        $blob = $blobClient->putBlob(GB_BLOB_CONTAINER, 
                                             $blobName, 
                                             $_FILES['blobfile']['tmp_name'], 
                                             null, null, 
                                             array('x-ms-blob-content-type' => $_FILES['blobfile']['type']));
                       
                        //Get the form fields
                        $name = htmlentities($_POST['NameTextBox']);
                        $message = htmlentities($_POST['MessageTextBox']);
                        
                        //Create a new entity object
                        $entry = new GuestBookEntry(); 
                        $entry->GuestName = $name; 
                        $entry->Message = $message;
                        $entry->PhotoUrl = $blob->Url;
                        $entry->ThumbnailUrl = $blob->Url;
                        
                        //Insert it into the table
                        $result = $tableClient->insertEntity('GuestBookEntry', $entry);
                        
                        //Put a message on queue for the worker role to process a thumbnail...
                        $queueMessage = $blobName.','.$entry->getPartitionKey().','.$entry->getRowKey();
                        $queueClient->putMessage(GB_QUEUE_NAME, $queueMessage);
                        
                    }
                }
                else
                {
                    //nothing was uploaded?
                    echo 'no image was uploaded. Your entry was not added to the guestbook. We NEED your picture!</br>';
                }
              
            } //end if POSTBACK        
        ?>
        
        <div id="theResults">
        
            <div ID="UpdatePanel" >
            <?php
                DisplayGuestBookTable(); 
            ?>
            </div><!-- update panel -->
            
        </div>
            
        </div> 
    </div>
    </body>
</html>
