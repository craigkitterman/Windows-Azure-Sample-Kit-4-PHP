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
require_once dirname(__FILE__) . '/../AutoLoader.php';

/**
 * @category   Microsoft
 * @package    Microsoft_Console
 * @copyright  Copyright (c) 2009 - 2011, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class Microsoft_Console_Command
{
	/**
	 * The handler.
	 *
	 * @var array
	 */
	protected $_handler;
	
	/**
	 * Gets the handler.
	 * 
	 * @return array
	 */
	public function getHandler()
	{
		return $this->_handler;
	}
	
	/**
	 * Sets the handler.
	 * 
	 * @param array $handler
	 * @return Microsoft_Console_Command
	 */
	public function setHandler($handler)
	{
		$this->_handler = $handler;
		return $this;
	}
	
	/**
	 * Replaces PHP's error handler
	 * 
	 * @param mixed $errno
	 * @param mixed $errstr
	 * @param mixed $errfile
	 * @param mixed $errline
	 */
	public static function phpstderr($errno, $errstr, $errfile, $errline)
	{
		self::stderr($errno . ': Error in ' . $errfile . ':' . $errline . ' - ' . $errstr);
	}
	
	/**
	 * Replaces PHP's exception handler
	 * 
	 * @param Exception $exception
	 */
	public static function phpstdex($exception)
	{
		self::stderr('Error: ' . $exception->getMessage());
	}
	
	/**
	 * Writes output to STDERR, followed by a newline (optional)
	 * 
	 * @param string $errorMessage
	 * @param string $newLine
	 */
	public static function stderr($errorMessage, $newLine = true)
	{
		if (error_reporting() === 0) {
			return;
		}
		file_put_contents('php://stderr', $errorMessage . ($newLine ? "\r\n" : ''));
	}
	
	/**
	 * Bootstrap the shell command.
	 * 
	 * @param array $argv PHP argument values.
	 */
	public static function bootstrap($argv)
	{
		// Replace error handler
		set_error_handler(array('Microsoft_Console_Command', 'phpstderr'));
		set_exception_handler(array('Microsoft_Console_Command', 'phpstdex'));
		
		// Build the application model
		$model = self::_buildModel();
		
		// Find a class that corresponds to the $argv[0] script name
		$requiredHandlerName = str_replace('.bat', '', str_replace('.sh', '', str_replace('.php', '', strtolower(basename($argv[0])))));
		$handler = null;
		foreach ($model as $possibleHandler) {
			if ($possibleHandler->handler == $requiredHandlerName) {
				$handler = $possibleHandler;
				break;
			}
		}
		if (is_null($handler)) {
			self::stderr("No class found that implements handler " . $requiredHandlerName);
			die();
		}
		
		// Find a method that matches the command name
		$command = null;
		foreach ($handler->commands as $possibleCommand) {
			if (in_array((isset($argv[1]) ? $argv[1] : '<default>'), $possibleCommand->aliases)) {
				$command = $possibleCommand;
				break;
			}
		}
		if (is_null($command)) {
			self::stderr("No method found that implements command " . (isset($argv[1]) ? $argv[1] : '<default>'));
			die();
		}
		
		// Parse parameter values
		$parameterValues = array();
		$parameterInputs = array_splice($argv, 2);
		foreach ($command->parameters as $parameter) {
			// Default value: null
			$value = null;
			
			// Consult value providers for value. First one wins.
			foreach ($parameter->valueproviders as $valueProviderName) {
				$valueProvider = new $valueProviderName();
				
				$value = $valueProvider->getValueForParameter($parameter, $parameterInputs);
				if (!is_null($value)) {
					break;
				}
			}
			
			// Set value
			$parameterValues[] = $value;
		}		
	
		// Run the command
		$className = $handler->class;
		$classInstance = new $className();
		$classInstance->setHandler($handler);
		call_user_func_array(array($classInstance, $command->method), $parameterValues);
		
		// Restore error handler
		restore_exception_handler();
		restore_error_handler();
	}
	
	/**
	 * Builds the handler model.
	 * 
	 * @return array
	 */
	protected static function _buildModel()
	{
		$model = array();
		
		$classes = get_declared_classes();
		foreach ($classes as $class) {
			$type = new ReflectionClass($class);
			
			$handlers = self::_findValueForDocComment('@command-handler', $type->getDocComment());
			$handlerDescriptions = self::_findValueForDocComment('@command-handler-description', $type->getDocComment());
			$handlerHeaders = self::_findValueForDocComment('@command-handler-header', $type->getDocComment());
			
			for ($hi = 0; $hi < count($handlers); $hi++) {
				$handler = $handlers[$hi];
				$handlerDescription = isset($handlerDescriptions[$hi]) ? $handlerDescriptions[$hi] : isset($handlerDescriptions[0]) ? $handlerDescriptions[0] : '';
				$handlerDescription = str_replace('\r\n', "\r\n", $handlerDescription);
				$handlerDescription = str_replace('\n', "\n", $handlerDescription);
				
				$handlerModel = (object)array(
					'handler'     => $handler,
					'description' => $handlerDescription,
					'headers'     => $handlerHeaders,
					'class'       => $class,
					'commands'    => array()
				);
					
				$methods = $type->getMethods();
			    foreach ($methods as $method) {
			       	$commands = self::_findValueForDocComment('@command-name', $method->getDocComment());
			       	$commandDescriptions = self::_findValueForDocComment('@command-description', $method->getDocComment());
			       	$commandExamples = self::_findValueForDocComment('@command-example', $method->getDocComment());
			       	
			       	if (count($commands) > 0) {
						$command = $commands[0];
						$commandDescription = isset($commandDescriptions[0]) ? $commandDescriptions[0] : '';
								
						$commandModel = (object)array(
							'command'     => $command,
							'aliases'     => $commands,
							'description' => $commandDescription,
							'examples'    => $commandExamples,
							'class'       => $class,
							'method'      => $method->getName(),
							'parameters'  => array()
						);
						
						$parameters = $method->getParameters();
						$parametersFor = self::_findValueForDocComment('@command-parameter-for', $method->getDocComment());
						for ($pi = 0; $pi < count($parameters); $pi++) {
							// Find the $parametersFor with the smae name defined
							$parameter = $parameters[$pi];
							$parameterFor = null;
							foreach ($parametersFor as $possibleParameterFor) {
								$possibleParameterFor = explode(' ', $possibleParameterFor, 4);
								if ($possibleParameterFor[0] == '$' . $parameter->getName()) {
									$parameterFor = $possibleParameterFor;
									break;
								}
							}
							if (is_null($parameterFor)) {
								die('@command-parameter-for missing for parameter $' . $parameter->getName());	
							}
							
							$parameterModel = (object)array(
								'name'           => '$' . $parameter->getName(),
								'valueproviders' => explode('|', $parameterFor[1]),
								'aliases'        => explode('|', $parameterFor[2]),
								'description'    => (isset($parameterFor[2]) ? $parameterFor[3] : '')
							);
							
							// Add to model
							$commandModel->parameters[] = $parameterModel;
						}

						// Add to model
						$handlerModel->commands[] = $commandModel;
			       	}
				}
					
				// Add to model
				$model[] = $handlerModel;
			}
		}
	
		return $model;
	}
	
	/**
	 * Finds the value for a specific docComment.
	 * 
	 * @param string $docCommentName Comment name
	 * @param unknown_type $docComment Comment object
	 * @return array
	 */
	protected static function _findValueForDocComment($docCommentName, $docComment)
	{
		$returnValue = array();
		
		$commentLines = explode("\n", $docComment);
	    foreach ($commentLines as $commentLine) {
	        if (strpos($commentLine, $docCommentName . ' ') !== false) {
	            $returnValue[] = trim(substr($commentLine, strpos($commentLine, $docCommentName) + strlen($docCommentName) + 1));
	        }
	    }
	    
	    return $returnValue;
	}
	
	/**
	 * Display information on an object
	 * 
	 * @param object $object Object
	 * @param array $propertiesToDump Property names to display
	 */
	protected function _displayObjectInformation($object, $propertiesToDump = array())
	{
		foreach ($propertiesToDump as $property) {
			printf('%-16s: %s' . "\r\n", $property, $object->$property);
		}
		printf("\r\n");
	}
	
	/**
	 * Displays the help information.
	 * 
	 * @command-name <default>
	 * @command-name -h
	 * @command-name -help
	 * @command-description Displays the current help information.
	 */
	public function helpCommand() {
		$handler = $this->getHandler();
		$newline = "\r\n";
		
		if (count($handler->headers) > 0) {
			foreach ($handler->headers as $header) {
				printf('%s%s', $header, $newline);		
			}
			printf($newline);
		}
		printf('%s%s', $handler->description, $newline);
		printf($newline);
		printf('Available commands:%s', $newline);
		foreach ($handler->commands as $command) {
			$description = str_split($command->description, 52);
			printf('  %-25s %s%s', implode(', ', $command->aliases), $description[0], $newline);
			for ($di = 1; $di < count($description); $di++) {
				printf('    %-25s %s%s', '', $description[$di], $newline);
			}
			printf($newline);			
			
			if (count($command->parameters) > 0) {
				foreach ($command->parameters as $parameter) {
					$description = str_split($parameter->description, 50);
					printf('    %-23s %s%s', implode(', ', $parameter->aliases), $description[0], $newline);
					for ($di = 1; $di < count($description); $di++) {
						printf('    %-23s %s%s', '', $description[$di], $newline);
					}
					printf($newline);
				}
			}
			printf($newline);
			
			if (count($command->examples) > 0) {
				printf('    Example usage:%s', $newline);
				foreach ($command->examples as $example) {
					printf('      %s%s', $example, $newline);
				}
				printf($newline);
			}
		}
	}
}
