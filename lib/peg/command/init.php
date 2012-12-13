<?php
/*
 * @author Jefferson González
 * 
 * @license 
 * This file is part of PEG check the license file for information.
 * 
*/

namespace Peg\Command;

use Peg\CommandLine\Option;
use Peg\CommandLine\OptionType;

/**
 * In charge of initializing a directory to produce an extension.
 */
class Init extends \Peg\CommandLine\Command
{
	public function __construct()
	{
		parent::__construct("init");
		
		$this->description = "Populates a directory with skeleton files in preparation for generating an extension source code.";
		
		$this->RegisterAction(new Action\Init());
		
		$author = new Option(array(
			"long_name"=>"author",
			"short_name"=>"a",
			"type"=>OptionType::STRING,
			"required"=>true,
			"description"=>"Main author/developer that is going to be working on the extension.",
			"default_value"=>""
		));
		
		$this->AddOption($author);
	}
}

?>
