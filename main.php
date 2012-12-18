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

// Set the path to peg files by using environment variables if available,
// if not use current path
if(isset($_SERVER["PEG_SKELETON_PATH"]))
	define("PEG_SKELETON_PATH", $_SERVER["PEG_SKELETON_PATH"]);
else
	define("PEG_SKELETON_PATH", "./skeleton");

if(isset($_SERVER["PEG_LIBRARY_PATH"]))
	define("PEG_LIBRARY_PATH", $_SERVER["PEG_LIBRARY_PATH"]);
else 
	define("PEG_LIBRARY_PATH", "./");

if(isset($_SERVER["PEG_LOCALE_PATH"]))
	define("PEG_LOCALE_PATH", $_SERVER["PEG_LOCALE_PATH"]);
else
	define("PEG_LOCALE_PATH", "./locale");


if(!file_exists(PEG_LIBRARY_PATH . "lib"))
	throw new Exception("Peg lib path not found.");

if(!file_exists(PEG_LIBRARY_PATH . "lib"))
	throw new Exception("Peg skeleton files path not found.");

// Register class auto-loader
function peg_autoloader($class_name)
{	
	$file = str_replace("\\", "/", $class_name) . ".php";

	include(PEG_LIBRARY_PATH . "lib/".strtolower($file));
}

spl_autoload_register("peg_autoloader");

// Register global function for translating and to facilitate automatic
// generation of po files.
function t($text)
{
	static $language_object;
	
	if(!$language_object)
	{
		$language_object = new Localization\Language(PEG_LOCALE_PATH);
	}
	
	return $language_object->Translate($text);
}

// Initialize the application
Peg\Application::Intialize();

// Retrieve a reference of main command line parser
$parser = Peg\Application::GetParser();

// Set Application details
$parser->application_name = "peg";
$parser->application_version = "1.0";
$parser->application_description = t("PHP Extension Generator (http://github.com/wxphp/peg)");

// Register command operations
$parser->RegisterCommand(Peg\Application::GetHelpCommand());
$parser->RegisterCommand(Peg\Application::GetInitCommand());
$parser->RegisterCommand(Peg\Application::GetParseCommand());

$parser->Start($argc, $argv);

?>
