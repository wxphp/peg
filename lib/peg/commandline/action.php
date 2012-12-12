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
 * Class that represents an action executed when a specific command is called.
 */
abstract class Action
{
	/**
	 * Method called by the command if it was executed.
	 */
	abstract public function OnCall(Command $command);
}

?>
