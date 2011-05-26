# About #
"Guestbook” a simple application that allows guests to submit a guestbook entry and displays all the entries in a simple table with clickable thumbnail images of each guest.  The input form includes Name, Comments, and a user photo upload.  The application also consists of a background “worker” role that processes the images and generates an image thumbnail for each entry.  Some simple ajax on the web form/display page then updates the guestbook table with the newly created thumbnail image when it has been processed.
The application is taking advantage of the Windows Azure storage objects to keep track of the guestbook entries, store the image files, and keep track of asynchronous work (creating thumbnails) that is processed by the “worker” role implementation.    This is a good example of one of the most common patterns in cloud computing (asynchronous dispatch) and should provide a good simple example of how to take advantage of the primary (non-relational) storage objects in the Windows Azure platform.

# Setup and Installation #
- Install the Windows Azure SDK v1.4
	+ http://www.microsoft.com/windowsazure/getstarted/ 
- Install the Azure SDK for PHP v3.0
	+ http://phpazure.codeplex.com/ 
	Make a note of the folder location where the sdk files are installed
- Install the Windows Azure Command Line Tools for PHP from: http://azurephptools.codeplex.com/ 
	+ Make a note of the folder location where the sdk files are installed
- Modify the build.bat file in the root folder of the Guestbook application:
	+ Set the “WACMDDIR” constant to the proper path for your Windows Azure Command Line Tools folder
	+ Set the “PHPRUNTIMEDIR” to the folder that holds your php.exe (i.e. c:\program files\php\v5.3)
	+ Run build.bat
		* This will build, package, and deploy the app to the local Azure Compute Emulator.  The app is configured by default to use dev (local) storage.  If you want to use a live Azure storage account, you can change the details in the constants.php file in both the “Web” and “Worker” folders (note: they must be the same)

# Additional Information #

Questions?  Problems? Suggestions or Improvements?
Please don’t hesitate to contact us:
Craig Kitterman, ckitter@microsoft.com, @craigkitterman
Peter Laudati, peterlau@microsoft.com, @jrzyshr
