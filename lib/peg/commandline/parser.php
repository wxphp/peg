<?php
/*
 * @author Jefferson González
 * 
 * @license 
 * This file is part of PEG check the license file for information.
 * 
 * 
*/

namespace Peg\CommandLine;

/**
 * Class in charge of parsing the command line options
 */
class Parser
{
	private $argument_count;
	
	private $argument_values;
	/**
	 * List of command line options registered on the parser.
	 * 
	 * @var \Peg\CommandLine\Option[]
	 */
	private $options;
	
	/**
	 * List of sub-commands registered on the parser.
	 * 
	 * @var \Peg\CommandLine\Command[] 
	 */
	private $commands;
	
	/**
	 * Name of the main application using the command line parser, displayed
	 * when printing the help message.
	 * 
	 * @var string 
	 */
	public $application_name;
	
	/**
	 * Version number of the main application using the command line parser,
	 * displayed when printing the help message.
	 * 
	 * @var string 
	 */
	public $application_version;
	
	/**
	 * Description of the main application using the command line parser,
	 * displayed when printing the help message.
	 * 
	 * @var string 
	 */
	public $application_description;
	
	/**
	 * Initialize the parser.
	 */
	public function __construct()
	{
		$this->options = array();
		$this->commands = array();
		
		$this->application_name = "Untitled";
		$this->application_version = "0.1";
		$this->application_description = "Untitled application description.";
	}
	
	/**
	 * Get array of options.

	 * @return \Peg\CommandLine\Option[]
	 */
	public function GetOptions()
	{
		return $this->options;
	}
	
	/**
	 * Get array of commands
	 * 
	 * @return \Peg\CommandLine\Command[]
	 */
	public function GetCommands()
	{
		return $this->commands;
	}
	
	/**
	 * Adds a sub command to the parser.
	 * @param \Peg\CommandLine\Command $command
	 * @throws Exception
	 */
	public function RegisterCommand(Command $command)
	{
		if(!isset($this->commands[$command->name]))
			$this->commands[$command->name] = $command;
		else
			throw new \Exception("Command '{$command->name}' is already registered.");
	}
	
	/**
	 * Adds an option to the parser.
	 * @param \Peg\CommandLine\Option $option
	 * @throws Exception
	 */
	public function RegisterOption(Option $option)
	{
		if(!isset($this->options[$option->long_name]))
			$this->options[$option->long_name] = $option;
		else
			throw new \Exception("Option '{$option->long_name}' is already registered.");
	}
	
	/**
	 * Begins the process of reading command line options and calling actions
	 * as needed.
	 * 
	 * @param integer $argc
	 * @param array $argv
	 */
	public function Start($argc, $argv)
	{
		$this->argument_count = $argc;
		$this->argument_values = $argv;
		
		if($this->argument_count <= 1)
		{
			$this->PrintHelp();
		}
		
		if($this->IsCommand($this->argument_values[1]))
		{
			$command = $this->commands[$this->argument_values[$i]];
			$this->ParseOptions($command->options);

			return;
		}
		else
		{
			$this->ParseOptions($this->options);
		}
	}
	
	/**
	 * Generates and prints the help based on the registered commands and options.
	 */
	public function PrintHelp()
	{
		print $this->application_name . " v" . $this->application_version . "\n";
		print $this->application_description . "\n\n";
	}
	
	private function IsCommand($name)
	{
		return isset($this->commands[$name]);
	}
	
	/**
	 * Parses the command line options depending on a set of given options.
	 * 
	 * @param \Peg\CommandLine\Option[] $options
	 */
	private function ParseOptions(&$options)
	{
		foreach($options as $index=>$option)
		{
			for($argi=1; $argi<$this->argument_count; $argi++)
			{
				$argument = $this->argument_values[$argi];
				$argument_next = $this->argument_values[$argi+1];
				
				if(
					strstr($argument, "-") !== false ||
					strstr($argument, "--") !== false
				)
				{
					$argument = str_replace("-", "", $argument);
					
					if(
						$argument == $option->long_name ||
						$argument == $option->short_name
					)
					{
						if($option->SetValue($argument_next))
							$argi++; //Forward to next argument
						
						if($option->IsValid())
						{
							$options[$index] = $option;
						}
						else
						{
							throw new \Exception("Invalid value supplied for '$argument'");
						}
					}
				}
				elseif(!$this->IsCommand($argument))
				{
					throw new \Exception("Invalid parameter '$argument'");
				}
			}
		}
	}
}

