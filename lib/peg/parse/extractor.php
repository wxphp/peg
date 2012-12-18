<?php
/*
 * @author Jefferson González
 * 
 * @license 
 * This file is part of PEG check the license file for information.
 * 
*/

namespace Peg\Parse;

use Peg\Application;
use Peg\Utilities\Json;
use Peg\CommandLine\Error;
use Peg\Utilities\FileSystem;

/**
 * Declares the base for a parse action that extract and generates definition
 * files.
 */
abstract class Extractor extends \Peg\CommandLine\Action 
{
	protected $command;
	
	protected $input_format;
	
	protected $verbose;
	
	protected $constants;
	
	protected $enumerations;
	
	protected $variables;
	
	protected $type_definitions;
	
	protected $resources;
	
	protected $functions;
	
	protected $classes;
	
	protected $class_enumerations;
	
	protected $class_variables;
	
	protected $class_groups;
	
	protected $includes;
	
	public function __construct($input_format)
	{
		$this->input_format = $input_format;
		
		$this->constants = array();
		$this->enumerations = array();
		$this->variables = array();
		$this->type_definitions = array();
		$this->functions = array();
		$this->classes = array();
		$this->class_enumerations = array();
		$this->class_variables = array();
		$this->class_groups = array();
		$this->includes = array();
	}
	
	public function OnCall(\Peg\CommandLine\Command $command) 
	{
		if(!Application::ValidExtension())
			Error::Show ("The current directory is not a valid peg managed extension.");
		
		if(!file_exists(Application::GetCwd() . "/json"))
			FileSystem::MakeDir (Application::GetCwd () . "/json");
		
		if($command->GetOption("input-format")->GetValue() == $this->input_format)
		{
			$this->command = $command;
			
			$this->verbose = $command->GetOption("verbose")->active;
			
			$this->Start($command->GetOption("source")->GetValue());
		}
	}
	
	/**
	 * Generates definition files in a specified path.
	 * Can generate definitions of a specific type if the $type is specified
	 * using one of the values from \Peg\Parse\DefinitionsType
	 * @param string $path
	 * @param integer $type If not set generates definitions for all types.
	 */
	public function SaveDefinitions($path, $type=null)
	{
		$path = rtrim($path, "/\\");
		
		switch($type)
		{
			case DefinitionsType::CONSTANTS:
				file_put_contents($path . "/constants.json", Json::Encode($this->constants));
				print "Constants found: " . $this->CountDefinitions($this->constants) . "\n";
				break;
			
			case DefinitionsType::ENUMERATIONS:
				file_put_contents($path . "/enumerations.json", Json::Encode($this->enumerations));
				print "Enumerations found: " . $this->CountDefinitions($this->enumerations) . "\n";
				break;
			
			case DefinitionsType::VARIABLES:
				file_put_contents($path . "/variables.json", Json::Encode($this->variables));
				print "Variables found: " . $this->CountDefinitions($this->variables) . "\n";
				break;
			
			case DefinitionsType::TYPE_DEFINITIONS:
				file_put_contents($path . "/type_definitions.json", Json::Encode($this->type_definitions));
				print "Type definitions found: " . $this->CountDefinitions($this->type_definitions) . "\n";
				break;
			
			case DefinitionsType::RESOURCES:
				file_put_contents($path . "/resources.json", Json::Encode($this->resources));
				break;
			
			case DefinitionsType::FUNCTIONS:
				file_put_contents($path . "/functions.json", Json::Encode($this->functions));
				print "Functions found: " . $this->CountDefinitions($this->functions) . "\n";
				break;
			
			case DefinitionsType::CLASSES:
				file_put_contents($path . "/classes.json", Json::Encode($this->classes));
				print "Classes found: " . $this->CountDefinitions($this->classes) . "\n";
				break;
			
			case DefinitionsType::CLASS_ENUMERATIONS:
				file_put_contents($path . "/class_enumerations.json", Json::Encode($this->class_enumerations));
				print "Class enumerations found: " . $this->CountDefinitions($this->class_enumerations) . "\n";
				break;
			
			case DefinitionsType::CLASS_VARIABLES:
				file_put_contents($path . "/class_variables.json", Json::Encode($this->class_variables));
				print "Class variables found: " . $this->CountDefinitions($this->class_variables) . "\n";
				break;
			
			case DefinitionsType::CLASS_GROUPS:
				file_put_contents($path . "/class_groups.json", Json::Encode($this->class_groups));
				break;
			
			case DefinitionsType::INCLUDES:
				file_put_contents($path . "/includes.json", Json::Encode($this->includes));
				break;
			
			default: // Save all definitions
				file_put_contents($path . "/constants.json", Json::Encode($this->constants));
				file_put_contents($path . "/enumerations.json", Json::Encode($this->constants));
				file_put_contents($path . "/variables.json", Json::Encode($this->constants));
				file_put_contents($path . "/type_definitions.json", Json::Encode($this->constants));
				file_put_contents($path . "/resources.json", Json::Encode($this->constants));
				file_put_contents($path . "/functions.json", Json::Encode($this->constants));
				file_put_contents($path . "/classes.json", Json::Encode($this->constants));
				file_put_contents($path . "/variables.json", Json::Encode($this->constants));
				file_put_contents($path . "/groups.json", Json::Encode($this->constants));
				file_put_contents($path . "/includes.json", Json::Encode($this->constants));
		}
	}
	
	private function CountDefinitions($definitions)
	{
		$definitions_found = 0;
		
		foreach($definitions as $file=>$namespaces)
		{
			foreach($namespaces as $definitions_list)
				$definitions_found += count($definitions_list);
		}
		
		return $definitions_found;
	}
	
	abstract public function Start($path);
}

?>
