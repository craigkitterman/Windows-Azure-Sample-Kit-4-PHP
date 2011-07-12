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
require_once 'InitializeStorage.php';
require_once 'Microsoft\AutoLoader.php';
require_once 'GuestBookEntry.php';

// initialize storage
list($tableClient, $blobClient, $queueClient) = InitializeStorage();    

// set the max length of the longest side of the thumbnail image
$max_thumb_side_length = 128;

//seconds to sleep between queue review cycles
$sleep_seconds = 20;


//loop forever
while (true)
{
	ProcessThumbnails($tableClient,$blobClient,$queueClient,$max_thumb_side_length);
	
    // Pause the loop
	echo("\nSleep for ".$sleep_seconds." seconds\n");
	sleep($sleep_seconds);

} // end while


function ProcessThumbnails($tableClient,$blobClient,$queueClient,$max_thumb_side_length)
{
    // Fetch messages
	$messagesToProcess = $queueClient->getMessages(GB_QUEUE_NAME);
    echo "\n********\nGot Message List\n";
    
	// Process messages
	foreach ($messagesToProcess as $message) 
    {
            // Unserialize body text
            $messageContents = explode(',',$message->MessageText);
	            
            //parse blob name, partition key, row key
            $blobName = $messageContents[0];
            $partitionKey = $messageContents[1];
            $rowKey = $messageContents[2];
            $thumbFileName = 'thumbnail_' . $blobName;
            $thumbLocation= __DIR__ . "\\$thumbFileName";

            // Get information from GuestBookEntry table to find image 
            $guestBookEntry = $tableClient->retrieveEntityById(GB_TABLE_NAME, $partitionKey, $rowKey, 'GuestBookEntry'); 
            
            // Pull original image from blob  
            $src = imagecreatefromjpeg($guestBookEntry->PhotoUrl); // this function allow you to pull an image from a url

            // Get size and mime type info for image
            $image_info = getimagesize($guestBookEntry->PhotoUrl); // this can pull from a url as well
            $image_width = $image_info[0];
            $image_height = $image_info[1];
            
            // the next couple lines are from the regular samples
            list($thumb_width,$thumb_height) = GetThumbnailDimensions($image_width,  $image_height, $max_thumb_side_length);
            echo "Thumbnail will be ".$thumb_height." high by ".$thumb_width." wide.";
            $thumbImage = imagecreatetruecolor($thumb_width, $thumb_height);
            imagecopyresampled($thumbImage, $src, 0, 0, 0, 0, $thumb_width, $thumb_height, $image_width,  $image_height);

            // Create a temporary thumbnail image file to upload
            if($image_info['mime'] == 'image/jpeg') 
            {
                echo "\nCreated temporary jpeg image";
                imagejpeg($thumbImage, $thumbLocation, 100);
            } 
            else if($image_info['mime'] == 'image/png') 
            {
                echo "\nCreated temporary png image";
                imagepng($thumbImage, $thumbLocation, 100);
            } 
            else 
            { 
                echo "\nERROR: Unable to create temporary image file!!! (startup.php:59)"; 
            }
             
            if(file_exists($thumbLocation)) 
            {
                echo "\nUploading thumbnail to blob storage";
                
                // Add the thumbnail image to the blob store
                $blob = $blobClient->putBlob(GB_BLOB_CONTAINER, 
                                                 $thumbFileName, 
                                                 $thumbLocation,
                                                 null, null, 
                                                 array('x-ms-blob-content-type' => $image_info['mime']));

                // Update table entry with thumbnail Url
                $guestBookEntry->ThumbnailUrl = $blob->Url;
                $result = $tableClient->updateEntity(GB_TABLE_NAME,$guestBookEntry);

                // Remove temporary image file from drive??
                unlink($thumbLocation);
            }

            // Delete message from processing queue
            $queueClient->deleteMessage(GB_QUEUE_NAME, $message);            
            
	} // end for
}

function GetThumbnailDimensions($OriginalImageWidth, $OriginalImageHeight, $max_thumb_side_length)
{
    if($OriginalImageHeight > $OriginalImageWidth)
    {
        $height = $max_thumb_side_length * $OriginalImageHeight / $OriginalImageWidth;
        $width = $max_thumb_side_length;
    }
    else
    {
        $height = $max_thumb_side_length;
        $width = $max_thumb_side_length * $OriginalImageWidth / $OriginalImageHeight;
    }

    return array($width,$height);
}

