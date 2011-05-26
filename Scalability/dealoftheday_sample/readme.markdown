# About #
Deal of the Day (DotD) is a sample application that has been written to demonstrate the ability of PHP to scale on the Windows Azure platform. The source code to the project has been made freely available on github as a learning tool and will be updated over time with updates to the Windows Azure SDK for PHP and other Windows Azure PHP tools.

You can see DotD in action (and maybe win a cool prize!) at http://dealoftheday.cloudapp.net.


# Setup and Installation #

- Get the Windows Azure PHP Pre-Requisites
	+ http://azurephp.interoperabilitybridges.com/get-started
- Setup a Windows Azure account if you do not already have one
	+ http://www.microsoft.com/windowsazure/getstarted/
- Create a new Windows Azure Storage Service
	+ Using the Legacy Portal
		* Select the project
		* Click "New Service"
		* Choose "Storage Account"
	+ Using the New Portal
		* Click the "New Storage Account" button on the top toolbar
- Download the DotD files (If you downloaded an archive unpack it) 
- Create the required certificates
	+ Two certificates will be created, a .cer and a .pem. 
		* The .cer will be uploaded through the Windows Azure Portal
		* The .pem is used by the DotD to prove to Windows Azure it has the right to make changes. Store the .pem in the "Worker" folder 
	+ Certificates are required to use the Windows Azure Service Management API
	+ For information on creating certificates see the "Create the certificate for the Windows Azure Service Management API" section in part II of the Windows Azure PHP Scaling series: http://azurephp.interoperabilitybridges.com/articles/scaling-php-applications-on-windows-azure-part-ii-role-management
	+ Be sure to take note of your subscription id, certificate key (thumbprint), and the name of your .pem file
		* Details on locating the above can be found in the "Setup a connection to the Service Management API" section in the same document previously linked
- Enter your Windows Azure hosted service and storage credentials into the config.php file
	+ NOTE: There is a config.php file in both "Web" and "Worker" folders
	+ Find your storage credentials in the Legacy Portal
		* Select the project
		* Select the storage account
		* You will need to copy the Primary Access Key and the custom endpoint you created (E.G: If one of your endpoints is http://fabrikam.blob.core.windows.net your endpoint is fabrikam)
	+ Find your storage credentials in the New Portal
		* Click "Hosted Services, Storage Accounts & CDN" in the left pane
		* Click "Storage Accounts" in the left pane
		* ************************************* Portal not loading. fill in these steps later ************************************
	+ AZURE_STORAGE_KEY corresponds to your storage Primary Access Key
	+ AZURE_SERVICE corresponds to your custom storage endpoint (fabrikam from the example above)
	+ AZURE_ROLE_END corresponds to the custom URL of your hosted service. You may not have a hosted service yet. It is ok to leave this field blank
	+ SUB_ID corresponds to the subscription id you found while creating your certificate files
	+ CERT_KEY corresponds to the certificate key (thumbprint) you found while creating your certificate files
	+ CERT corresponds to the filename of the .pem certificate you created. Be sure you use the full path to your certificate file. If your .pem is fabrikam.pem you can use \__DIR\__ . 'fabrikam.pem'
- Build the package
	+ If you have never built a Windows Azure PHP package before see this article: http://azurephp.interoperabilitybridges.com/articles/deploying-your-first-php-application-with-the-windows-azure-command-line-tools-for-php
	+ You build the package with the Windows Azure Command Line tools for PHP. Be sure you change the following variables to reflect your project
		* %PROJ% - Location of your project. E.G. C:\Projects\fabrikam
		* %PROJNAME% - The name of your project. E.G. FabrikamCustomerPortal
	+ Open a Windows Azure SDK Command Prompt and change to the directory of your Windows Azure Command Line tools for PHP
	+ The following command was used to build this package during development
		* php.exe package.php  --source="%PROJ%\Web" --project="%PROJNAME%" -f --target="%PROJ%\deploy" --worker-role-startup-script="worker.php" --worker-role="%PROJ%\Worker"
- Edit the ServiceConfiguration.cscfg file the commandline tool created (Will be located in the output folder from the tool) with your WindowsAzureStorageConnectionString value
	+ If you have not done this before see the Windows Azure PHP Scaling series for more information: http://azurephp.interoperabilitybridges.com/articles
- Create a new Hosted Service and deploy the .cspkg and .cscfg files through the Windows Azure Portal
	+ If you have not done this before see the article http://azurephp.interoperabilitybridges.com/articles/deploying-your-first-php-application-with-the-windows-azure-command-line-tools-for-php
- Run http://yourHostedServiceEndpoint.cloudapp.net/setup.php to initialize the Windows Azure storage account

# Captcha #
DotD uses the reCaptcha service. Here are the steps you need to implement your own Captcha with reCaptcha

- Create a reCaptcha account and generate the keys
- Edit templates/BuzzBee/BuzzBee.php and replace all instances of YOURCODE with the public key
- Edit getcode.php and replace all instances of YOURCODE with the private key

http://recaptcha.net 

# Managing Products #
DotD provides a management interface for adding products. To access it go to http://yourHostedServiceEndpoint.cloudapp.net/admin

Default password is 'Abc.123'. There is not currently a way to change the password through the UI. Please update it with your favorite Windows Azure storage tool

# Additional Information #
Additional information and documentation can be found at

- http://azurephp.interoperabilitybridges.com
- http://dealoftheday.cloudapp.net
