<?php
/*
 * @author Jefferson González
 * 
 * @license 
 * This file is part of PEG check the license file for information.
 * 
 * @description
 * Main start point for the PHP Extensions Generator
 * 
*/

// Register class auto-loader
function peg_autoloader($class_name)
{
	$file = str_replace("\\", "/", $class_name) . ".php";

	include("lib/".strtolower($file));
}

spl_autoload_register("peg_autoloader");

// Initialize command line parser
$parser = new Peg\CommandLine\Parser();

// Set Application details
$parser->application_name = "peg";
$parser->application_version = "1.0";
$parser->application_description = "PHP Extension Generator (http://github.com/wxphp/peg)";

// Some testing for the command line
$dummy = new Peg\CommandLine\Option();
$dummy->long_name = "some-flag";
$dummy->short_name = "s";
$dummy->type = Peg\CommandLine\OptionType::FLAG;
$dummy->description = "Displays the version of the binary that you are currently using to generate the extensions.";

$dir = new Peg\CommandLine\Option();
$dir->long_name = "directory";
$dir->short_name = "d";
$dir->type = Peg\CommandLine\OptionType::FLAG;
$dir->description = "Indicate the directory where the output of the skeleton files will be saved at the end.";

$command = new Peg\CommandLine\Command("init");
$command->description = "Populates a directory with skeleton files in preparation for generating an extension source code";
$command->AddOption($dummy);
$command->AddOption($dir);

$parser->RegisterCommand($command);

$command2 = new Peg\CommandLine\Command("generate");
$command2->description = "Populates a directory with skeleton files in preparation for generating an extension source code";
$command2->AddOption($dummy);

$parser->RegisterCommand($command2);

$parser->Start($argc, $argv);

print_r($command->options);
?>
