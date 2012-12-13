<?php
/*
 * @author Jefferson González
 * 
 * @license 
 * This file is part of PEG check the license file for information.
 * 
*/

namespace Peg;

/**
 * Holds global options and objects.
 */
class Application
{
	/**
	 * Reference to the global parser.
	 * @var \Peg\CommandLine\Parser
	 */
	private static $parser;
	
	/**
	 * Disable constructor
	 */
	private function __construct(){}
	
	/**
	 * Gets the global parser.
	 * @return \Peg\CommandLine\Parser
	 */
	public static function GetParser()
	{
		return self::$parser;
	}
	
	/**
	 * Sets the global parser.
	 * @param \Peg\CommandLine\Parser $parser
	 */
	public static function SetParser(\Peg\CommandLine\Parser $parser)
	{
		self::$parser = $parser;
	}
}

?>
