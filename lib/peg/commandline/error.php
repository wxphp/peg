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
 * Functions to throw error messages.
 */
class Error
{
	/**
	 * Displays a message and exits the application with error status code.
	 * 
	 * @param string $message The message to display before exiting the application.
	 */
	public static function Show($message)
	{
		print "Error: " . $message . "\n";
		exit(1);
	}
}

?>
