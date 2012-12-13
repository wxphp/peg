<?php
/*
 * @author Jefferson González
 * 
 * @license 
 * This file is part of PEG check the license file for information.
 * 
*/

namespace Peg\Command\Action;

/**
 * Action taken if the init command was executed.
 */
class Init extends \Peg\CommandLine\Action
{
	public function OnCall(\Peg\CommandLine\Command $command)
	{
		$author = $command->GetOption("author");
		
		print $command->value . "\n";
		
		print $author->value . "\n";
	}
}

?>
