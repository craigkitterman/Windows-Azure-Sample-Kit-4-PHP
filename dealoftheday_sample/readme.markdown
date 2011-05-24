# About #
Deal of the Day (DotD) is a sample application that has been written to demonstrate the ability of PHP to scale on the Windows Azure platform. The source code to the project has been made freely available on github as a learning tool and will be updated over time with updates to the Windows Azure SDK for PHP and other Windows Azure PHP tools.

You can see DotD in action (and maybe win a cool prize!) at http://dealoftheday.cloudapp.net.


# Setup and Installation #

- Download the DotD files
- Create the required certificates and place them in the "Worker" folder 
	+ Certificates are required to use the Windows Azure Service Management API
	+ For information on creating certificates see the "Create the certificate for the Windows Azure Service Management API" section in part II of the Windows Azure PHP Scaling series: http://azurephp.interoperabilitybridges.com/articles/scaling-php-applications-on-windows-azure-part-ii-role-management
- Enter your Windows Azure hosted service and storage credentials into the config.php file
	+ NOTE: There is a config.php file in both "Web" and "Worker" folders
- Build the package
	+ If you have never built a Windows Azure PHP package before see this article: http://azurephp.interoperabilitybridges.com/articles/deploying-your-first-php-application-with-the-windows-azure-command-line-tools-for-php
- Edit the ServiceConfiguration.cscfg file the commandline tool created (Will be located in the output folder from the tool) with your WindowsAzureStorageConnectionString value
	+ If you have not done this before see the Windows Azure PHP Scaling series for more information: http://azurephp.interoperabilitybridges.com/articles
- Deploy the .cspkg and .cscfg files through the Windows Azure Portal

# Additional Information #
Additional information and documentation can be found at

- http://azurephp.interoperabilitybridges.com
- http://dealoftheday.cloudapp.net
