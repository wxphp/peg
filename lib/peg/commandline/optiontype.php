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
 * Enumeration used to declare a \Peg\CommandLine\Option type
 */
class OptionType
{	
	/**
	 * Accepts any type of string
	 */
	const STRING=1;
	
	/**
	 * Only accept numbers
	 */
	const INTEGER=2;
}
?>
