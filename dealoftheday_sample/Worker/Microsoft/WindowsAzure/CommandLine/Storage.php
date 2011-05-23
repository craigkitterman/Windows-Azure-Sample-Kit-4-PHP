<?php
/**
 * Copyright (c) 2009 - 2011, RealDolmen
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of RealDolmen nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY RealDolmen ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL RealDolmen BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Microsoft
 * @package    Microsoft_Console
 * @subpackage Exception
 * @version    $Id: Exception.php 55733 2011-01-03 09:17:16Z unknown $
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */

/**
 * @see Microsoft_AutoLoader
 */
require_once dirname(__FILE__) . '/../../AutoLoader.php';

/**
 * Storage commands
 * 
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure_CommandLine
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 * 
 * @command-handler storage
 * @command-handler-description Windows Azure Storage commands
 * @command-handler-header Windows Azure SDK for PHP
 * @command-handler-header Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 */
class Microsoft_WindowsAzure_CommandLine_Storage
	extends Microsoft_Console_Command
{	
	/**
	 * List storage accounts for a specified subscription.
	 * 
	 * @command-name ListAccounts
	 * @command-description List storage accounts for a specified subscription.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-example List storage accounts for subscription:
	 * @command-example ListAccounts -sid:"<your_subscription_id>" -cert:"mycert.pem"
	 */
	public function listAccountsCommand($subscriptionId, $certificate, $certificatePassphrase)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->listStorageAccounts();

		foreach ($result as $object)
		{
			$this->_displayObjectInformation($object, array('ServiceName', 'Url'));
		}
	}
	
	/**
	 * Get storage account properties.
	 * 
	 * @command-name GetProperties
	 * @command-description Get storage account properties.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $accountName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --AccountName Required. The storage account name to operate on.
	 * @command-example Get storage account properties for account "phptest":
	 * @command-example GetProperties -sid:"<your_subscription_id>" -cert:"mycert.pem"
	 * @command-example --AccountName:"phptest"
	 */
	public function getPropertiesCommand($subscriptionId, $certificate, $certificatePassphrase, $accountName)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->getStorageAccountProperties($accountName);
		
		$this->_displayObjectInformation($result, array('ServiceName', 'Label', 'AffinityGroup', 'Location'));
	}
	
	/**
	 * Get storage account property.
	 * 
	 * @command-name GetProperty
	 * @command-description Get storage account property.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $accountName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --AccountName Required. The storage account name to operate on.
	 * @command-parameter-for $property Microsoft_Console_Command_ParameterSource_Argv --Property|-prop Required. The property to retrieve for the storage account.
	 * @command-example Get storage account property "Url" for account "phptest":
	 * @command-example GetProperty -sid:"<your_subscription_id>" -cert:"mycert.pem"
	 * @command-example --AccountName:"phptest" --Property:Url
	 */
	public function getPropertyCommand($subscriptionId, $certificate, $certificatePassphrase, $accountName, $property)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->getStorageAccountProperties($accountName);
		
		printf("%s\r\n", $result->$property);
	}
	
	/**
	 * Get storage account keys.
	 * 
	 * @command-name GetKeys
	 * @command-description Get storage account keys.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $accountName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --AccountName Required. The storage account name to operate on.
	 * @command-example Get storage account keys for account "phptest":
	 * @command-example GetKeys -sid:"<your_subscription_id>" -cert:"mycert.pem"
	 * @command-example --AccountName:"phptest"
	 */
	public function getKeysCommand($subscriptionId, $certificate, $certificatePassphrase, $accountName)
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->getStorageAccountKeys($accountName);
		
		$this->_displayObjectInformation((object)array('Key' => 'primary', 'Value' => $result[0]), array('Key', 'Value'));
		$this->_displayObjectInformation((object)array('Key' => 'secondary', 'Value' => $result[1]), array('Key', 'Value'));
	}
	
	/**
	 * Get storage account key.
	 * 
	 * @command-name GetKey
	 * @command-description Get storage account key.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $accountName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --AccountName Required. The storage account name to operate on.
	 * @command-parameter-for $key Microsoft_Console_Command_ParameterSource_Argv --Key|-k Optional. Specifies the key to regenerate (primary|secondary). If omitted, primary key is used as the default.
	 * @command-example Get primary storage account key for account "phptest":
	 * @command-example GetKey -sid:"<your_subscription_id>" -cert:"mycert.pem"
	 * @command-example --AccountName:"phptest" -Key:primary
	 */
	public function getKeyCommand($subscriptionId, $certificate, $certificatePassphrase, $accountName, $key = 'primary')
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$result = $client->getStorageAccountKeys($accountName);
		
		if (strtolower($key) == 'secondary') {
			printf("%s\r\n", $result[1]);
		}
		printf("%s\r\n", $result[0]);
	}
	
	/**
	 * Regenerate storage account keys.
	 * 
	 * @command-name RegenerateKeys
	 * @command-description Regenerate storage account keys.
	 * @command-parameter-for $subscriptionId Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --SubscriptionId|-sid Required. This is the Windows Azure Subscription Id to operate on.
	 * @command-parameter-for $certificate Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --Certificate|-cert Required. This is the .pem certificate that user has uploaded to Windows Azure subscription as Management Certificate.
	 * @command-parameter-for $certificatePassphrase Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Prompt --Passphrase|-p Required. The certificate passphrase. If not specified, a prompt will be displayed.
	 * @command-parameter-for $accountName Microsoft_Console_Command_ParameterSource_Argv|Microsoft_Console_Command_ParameterSource_Env --AccountName Required. The storage account name to operate on.
	 * @command-parameter-for $key Microsoft_Console_Command_ParameterSource_Argv --Key|-k Optional. Specifies the key to regenerate (primary|secondary). If omitted, primary key is used as the default.
	 * @command-example Regenerate secondary key for account "phptest":
	 * @command-example RegenerateKeys -sid:"<your_subscription_id>" -cert:"mycert.pem"
	 * @command-example --AccountName:"phptest" -Key:secondary
	 */
	public function regenerateKeysCommand($subscriptionId, $certificate, $certificatePassphrase, $accountName, $key = 'primary')
	{
		$client = new Microsoft_WindowsAzure_Management_Client($subscriptionId, $certificate, $certificatePassphrase);
		$client->regenerateStorageAccountKey($accountName, $key);
	}
}

Microsoft_Console_Command::bootstrap($_SERVER['argv']);