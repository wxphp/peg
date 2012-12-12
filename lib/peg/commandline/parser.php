<?php
/*
 * @author Jefferson González
 * 
 * @license 
 * This file is part of PEG check the license file for information.
 * 
*/

namespace Peg\CommandLine;

/**
 * Class in charge of parsing the command line options
 */
class Parser
{
	/**
	 * Stores the number of arguments passed on command line.
	 * 
	 * @var integer
	 */
	private $argument_count;
	
	/**
	 * Stores the values passed on the command line.
	 * 
	 * @var string[] 
	 */
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
	 * Begins the process of reading command line options and calling command
	 * actions as needed.
	 * 
	 * @param integer $argc
	 * @param array $argv
	 */
	public function Start($argc, $argv)
	{
		$this->argument_count = $argc;
		$this->argument_values = $argv;
		
		if(
			$this->argument_count <= 1 ||
			in_array("-h", $this->argument_values) ||
			in_array("--help", $this->argument_values)	
		)
		{
			$this->PrintHelp();
		}
		
		if(in_array("--version", $this->argument_values))
		{
			$this->PrintVersion();
		}
		
		if($this->IsCommand($this->argument_values[1]))
		{
			$command = $this->commands[$this->argument_values[1]];
			$this->ParseOptions($command->options, $command);
			$command->Execute();

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
		// Store the len of the longest command name
		$max_command_len = 0;
		
		//Store the len of longest command name
		$max_option_len = 0;
		
		print $this->application_name . " v" . $this->application_version . "\n";
		print $this->application_description . "\n\n";
		
		print "Usage:\n";
		print "   " . $this->application_name . " [options]\n";
		
		if(count($this->commands) > 0)
		{
			foreach($this->commands as $command)
			{
				if(strlen($command->name) > $max_command_len)
					$max_command_len = strlen($command->name);
				
				if(count($command->options) > 0)
				{
					foreach($command->options as $option)
					{
						if(strlen($option->long_name) > $max_option_len)
							$max_option_len = strlen($option->long_name);
					}
				}
			}
			
			print "   peg <command> [options]\n";
		}
		
		if(count($this->commands) > 0)
		{
			print "\nCommands:\n";
			
			foreach($this->commands as $command)
			{
				if(count($command->options) > 0)
				{
					$line = "  " . str_pad($command->name, $max_command_len+2) . $command->description;
					$line = wordwrap($line, 80);
					$line_array = explode("\n", $line);
					
					print $line_array[0] . "\n";
					unset($line_array[0]);
					
					if(count($line_array) > 0)
					{
						foreach($line_array as $line)
						{
							print str_pad($line, strlen($line)+($max_command_len+4), " ", STR_PAD_LEFT) . "\n";
						}
					}
					
					if(count($command->options) > 0)
					{
						print "\n";
						print "    " . "Options:" . "\n";
						foreach($command->options as $option)
						{
							$line = 
								"      " . 
								str_pad(
									"-" . $option->short_name . "  --" . $option->long_name,
									$max_option_len+8
								) . 
								$option->description
							;
							
							$line = wordwrap($line, 80);
							$line_array = explode("\n", $line);
							
							print $line_array[0] . "\n";
							unset($line_array[0]);
							
							if(count($line_array) > 0)
							{
								foreach($line_array as $line)
								{
									print str_pad($line, strlen($line)+($max_option_len+14), " ", STR_PAD_LEFT) . "\n";
								}
							}
						}
					}
				}
				
				print "\n";
			}
		}
			
		
		exit(0);
	}
	
	/**
	 * Generates and prints the help based on the registered commands and options.
	 */
	public function PrintVersion()
	{
		print "v" . $this->application_version . "\n";
		
		exit(0);
	}
	
	/**
	 * Checks if a given name is registered as a command.
	 * 
	 * @param string $name
	 * 
	 * @return boolean
	 */
	private function IsCommand($name)
	{
		return isset($this->commands[$name]);
	}
	
	/**
	 * Checks if a given option exists on a given options array
	 * @param type $name
	 * @param \Peg\CommandLine\Option[] $options
	 */
	private function OptionExists($name, $options)
	{
		foreach($options as $option)
		{
			if($option->long_name == $name || $option->short_name == $name)
				return true;
		}
		
		return false;
	}
	
	/**
	 * Parses the command line options depending on a set of given options.
	 * The given options are updated with the values assigned on the
	 * command line.
	 * 
	 * @param \Peg\CommandLine\Option[] $options
	 * @param \Peg\CommandLine\Command $command
	 */
	private function ParseOptions(&$options, \Peg\CommandLine\Command $command=null)
	{	
		foreach($options as $index=>$option)
		{
			$argi = 1;
			
			// If command passed start parsing after it.
			if($command)
				$argi = 2;
			
			for($argi; $argi<$this->argument_count; $argi++)
			{
				$argument_original = $this->argument_values[$argi];
				$argument = $this->argument_values[$argi];
				$argument_next = "";
				
				if($command != null)
				{
					if(
						strstr($argument, "-") === false &&
						strstr($argument, "--") === false
					)
					{
						$command->value = trim($command->value . " " . $argument);
						continue;
					}
					
				}
				
				if($argi+1 < $this->argument_count)
				{
					$argument_next = $this->argument_values[$argi+1];
				}
				
				if(
					strstr($argument, "-") !== false ||
					strstr($argument, "--") !== false
				)
				{
					$argument = str_replace("-", "", $argument);
					
					if($this->OptionExists($argument, $options))
					{
						if(
							$argument == $option->long_name ||
							$argument == $option->short_name
						)
						{
							switch($option->type)
							{
								case OptionType::FLAG:
									$option->active = true;
									break;

								default:
									if($option->SetValue($argument_next))
										$argi++; //Forward to next argument
							}

							if($option->IsValid())
							{
								$options[$index] = $option;
							}
							else
							{
								Error::Show("Invalid value supplied for '$argument_original'");
							}
						}
					}
					elseif(!$this->IsCommand($argument))
					{
						Error::Show("Invalid parameter '$argument_original'");
					}
				}
			}
		}
	}
}

