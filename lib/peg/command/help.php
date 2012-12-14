<?php
/*
 * @author Jefferson González
 * 
 * @license 
 * This file is part of PEG check the license file for information.
 * 
*/

namespace Peg\Command;

use Peg\Application;

/**
 * Display overall help or for a given command.
 */
class Help extends \Peg\CommandLine\Command
{
	public function __construct() {
		parent::__construct("help");
		
		$this->description = t("Display a help message for a specific command.");
		$this->description .= "\n" . t("Example:") . " " . 
		Application::GetParser()->application_name . " help <command>";
		
		$this->RegisterAction(new Action\Help());
	}
}

?>
