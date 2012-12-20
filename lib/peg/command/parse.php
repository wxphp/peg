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
 * Command to parse files and generate json representations of them.
 */
class Parse extends \Peg\CommandLine\Command
{
	public function __construct()
	{
		parent::__construct("parse");
		
		$this->description = t("Extracts definitions that are stored in json files.");
		
		$this->RegisterAction(new \Peg\Parse\Extractor\Doxygen());
		
		$input_format = new Option(array(
			"long_name"=>"input-format",
			"short_name"=>"f",
			"type"=>OptionType::STRING,
			"required"=>false,
			"description"=>t("The kind of input to parse. Default: doxygen") . "\n" .
			t("Allowed values:") . " doxygen",
			"default_value"=>"doxygen"
		));
		
		$this->AddOption($input_format);
		
		$source = new Option(array(
			"long_name"=>"source",
			"short_name"=>"s",
			"type"=>OptionType::STRING,
			"required"=>true,
			"description"=>t("The path were resides the input to parse."),
			"default_value"=>""
		));
		
		$this->AddOption($source);
		
		$headers = new Option(array(
			"long_name"=>"headers",
			"short_name"=>"h",
			"type"=>OptionType::STRING,
			"required"=>true,
			"description"=>t("The path were resides the header files of the library in order to correctly solve headers include path."),
			"default_value"=>""
		));
		
		$this->AddOption($headers);
		
		$verbose = new Option(array(
			"long_name"=>"verbose",
			"short_name"=>"v",
			"type"=>OptionType::FLAG,
			"required"=>false,
			"description"=>t("Turns verbosity on."),
			"default_value"=>"",
		));
		
		$this->AddOption($verbose);
	}
}

?>
