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

// Store the parser on the global settings
Peg\Application::SetParser($parser);

// Set Application details
$parser->application_name = "peg";
$parser->application_version = "1.0";
$parser->application_description = "PHP Extension Generator (http://github.com/wxphp/peg)";

$parser->RegisterCommand(new Peg\Command\Help());
$parser->RegisterCommand(new Peg\Command\Init());

$parser->Start($argc, $argv);

?>
