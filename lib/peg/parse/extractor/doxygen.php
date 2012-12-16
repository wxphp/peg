<?php
/*
 * @author Jefferson González
 * 
 * @license 
 * This file is part of PEG check the license file for information.
 * 
*/

namespace Peg\Parse\Extractor;

use Peg\Parse\DefinitionsType;
use Peg\Application;
use \DOMDocument;
use \DOMXPath;

/**
 * Implements a doxygen xml extractor of definitions
 */
class Doxygen extends \Peg\Parse\Extractor 
{
	private $document;
	private $path;
	
	public function __construct()
	{
		parent::__construct("doxygen");
	}
	
	public function Start($path)
	{
		$path = rtrim($path, "/\\");
		
		$this->path = $path;
		
		$this->document = new DOMDocument();
		$this->document->load($path . "/index.xml");
		
		$this->ExtractConstants();
		$this->ExtractEnumerations();
		$this->ExtractVariables();
		$this->ExtractTypeDefinitions();
		$this->ExtractFunctions();
		
		print "--------------------------------------------------------------\n";
		
		$this->SaveDefinitions(Application::GetCwd() . "/json", DefinitionsType::CONSTANTS);
		$this->SaveDefinitions(Application::GetCwd() . "/json", DefinitionsType::ENUMERATIONS);
		$this->SaveDefinitions(Application::GetCwd() . "/json", DefinitionsType::VARIABLES);
		$this->SaveDefinitions(Application::GetCwd() . "/json", DefinitionsType::TYPE_DEFINITIONS);
		$this->SaveDefinitions(Application::GetCwd() . "/json", DefinitionsType::FUNCTIONS);
		
		print "--------------------------------------------------------------\n";
	}
	
	private function ExtractConstants()
	{
		$xpath = new DOMXPath($this->document);
		
		$entries = $xpath->evaluate("//compound[@kind='file']", $this->document);
		
		for ($i = 0; $i < $entries->length; $i++) 
		{
			$refid = $entries->item($i)->getAttribute("refid");
			$name = $entries->item($i)->childNodes->item(0)->nodeValue;
			
			$file_doc = new DOMDocument();
			$file_doc->load($this->path . "/$refid.xml");

			$file_xpath = new DOMXPath($file_doc);

			$file_members = $file_xpath->evaluate("//memberdef[@kind='define'] | //memberdef[@kind='enum']", $file_doc);
			
			$this->constants[$name] = array();
			
			for($member=0; $member<$file_members->length; $member++)
			{
				$kind = $file_members->item($member)->getAttribute("kind");
				
				$namespace = "";
				
				if($kind == "define")
				{
					$define_name = $file_xpath->evaluate("name", $file_members->item($member))->item(0)->nodeValue;
					
					$define_initializer = true;
					
					if(is_object($file_xpath->evaluate("initializer", $file_members->item($member))->item(0)))
						$define_initializer = $file_xpath->evaluate("initializer", $file_members->item($member))->item(0)->nodeValue;

					//Skip macro function defines
					if($file_xpath->evaluate("param", $file_members->item($member))->length > 0)
					{
						continue;
					}

					//Skip defines used for compiler
					if($define_name{0} == "_" && $define_name{1} == "_")
					{
						continue;
					}

					$this->constants[$name][$namespace][$define_name] = "$define_initializer";
				}
				
				// Also add anonymous enumerations as constants
				elseif($kind == "enum")
				{
					$enum_name = $file_xpath->evaluate("name", $file_members->item($member))->item(0)->nodeValue;
					
					if($enum_name{0} != "@")
						continue;
					
					$enum_values = $file_xpath->evaluate("enumvalue", $file_members->item($member));

					for($enum_value=0; $enum_value<$enum_values->length; $enum_value++)
					{
						$this->constants[$name][$namespace][$file_xpath->evaluate("name", $enum_values->item($enum_value))->item(0)->nodeValue] = true;
					}
				}
			}
			
			// Remove files without enumerations
			if(count($this->constants[$name]) <= 0)
				unset($this->constants[$name]);
		}
	}
	
	private function ExtractEnumerations()
	{
		$xpath = new DOMXPath($this->document);
		
		$entries = $xpath->evaluate("//compound[@kind='file']", $this->document);
		
		for ($i = 0; $i < $entries->length; $i++) 
		{
			$refid = $entries->item($i)->getAttribute("refid");
			$name = $entries->item($i)->childNodes->item(0)->nodeValue;
			
			$file_doc = new DOMDocument();
			$file_doc->load($this->path . "/$refid.xml");

			$file_xpath = new DOMXPath($file_doc);

			$file_members = $file_xpath->evaluate("//memberdef[@kind='enum']", $file_doc);
			
			$this->enumerations[$name] = array();

			for($member=0; $member<$file_members->length; $member++)
			{
				$enum_name = $file_xpath->evaluate("name", $file_members->item($member))->item(0)->nodeValue;

				// Just extract named enumerations
				// Anonymous enumerations go on constants.json
				if($enum_name{0} != "@")
				{
					$namespace = "";
					
					$this->enumerations[$name][$namespace][$enum_name] = array();
					
					$enum_values = $file_xpath->evaluate("enumvalue", $file_members->item($member));
					
					for($enum_value=0; $enum_value<$enum_values->length; $enum_value++)
					{
						$this->enumerations[$name][$namespace][$enum_name][] = $file_xpath->evaluate("name", $enum_values->item($enum_value))->item(0)->nodeValue;
					}
				}
			}
			
			// Remove files without enumerations
			if(count($this->enumerations[$name]) <= 0)
				unset($this->enumerations[$name]);
		}
	}
	
	private function ExtractVariables()
	{
		$xpath = new DOMXPath($this->document);
		
		$entries = $xpath->evaluate("//compound[@kind='file']", $this->document);
		
		for ($i = 0; $i < $entries->length; $i++) 
		{
			$refid = $entries->item($i)->getAttribute("refid");
			$name = $entries->item($i)->childNodes->item(0)->nodeValue;
			
			$file_doc = new DOMDocument();
			$file_doc->load($this->path . "/$refid.xml");

			$file_xpath = new DOMXPath($file_doc);

			$file_members = $file_xpath->evaluate("//memberdef[@kind='variable']", $file_doc);
			
			$this->variables[$name] = array();
			
			for($member=0; $member<$file_members->length; $member++)
			{
				$namespace = "";
				$global_variable_name = $file_xpath->evaluate("name", $file_members->item($member))->item(0)->nodeValue;
				$global_variable_type = $file_xpath->evaluate("type", $file_members->item($member))->item(0)->nodeValue;
				
				$this->variables[$name][$namespace][$global_variable_name] = str_replace(array(" *", " &"), array("*", "&"), $global_variable_type);
			}
			
			// Remove files without variables
			if(count($this->variables[$name]) <= 0)
				unset($this->variables[$name]);
		}
	}
	
	private function ExtractTypeDefinitions()
	{
		$xpath = new DOMXPath($this->document);
		
		$entries = $xpath->evaluate("//compound[@kind='file']", $this->document);
		
		for ($i = 0; $i < $entries->length; $i++) 
		{
			$refid = $entries->item($i)->getAttribute("refid");
			$name = $entries->item($i)->childNodes->item(0)->nodeValue;
			
			$file_doc = new DOMDocument();
			$file_doc->load($this->path . "/$refid.xml");

			$file_xpath = new DOMXPath($file_doc);

			$file_members = $file_xpath->evaluate("//memberdef[@kind='typedef']", $file_doc);
			
			$this->type_definitions[$name] = array();
			
			for($member=0; $member<$file_members->length; $member++)
			{
				$namespace = "";
				
				$typedef_name = $file_xpath->evaluate("name", $file_members->item($member))->item(0)->nodeValue;
				$typedef_type = $file_xpath->evaluate("type", $file_members->item($member))->item(0)->nodeValue;
				
				$this->type_definitions[$name][$namespace][$typedef_name] = $typedef_type;
			}
			
			// Remove files without type definitions
			if(count($this->type_definitions[$name]) <= 0)
				unset($this->type_definitions[$name]);
		}
	}
	
	private function ExtractFunctions()
	{
		$xpath = new DOMXPath($this->document);
		
		$entries = $xpath->evaluate("//compound[@kind='file']", $this->document);
		
		for ($i = 0; $i < $entries->length; $i++) 
		{
			$refid = $entries->item($i)->getAttribute("refid");
			$name = $entries->item($i)->childNodes->item(0)->nodeValue;
			
			$file_doc = new DOMDocument();
			$file_doc->load($this->path . "/$refid.xml");

			$file_xpath = new DOMXPath($file_doc);

			$file_members = $file_xpath->evaluate("//memberdef[@kind='function']", $file_doc);
			
			$this->functions[$name] = array();
			
			for($member=0; $member<$file_members->length; $member++)
			{
				$namespace = "";
				
				$function_name = $file_xpath->evaluate("name", $file_members->item($member))->item(0)->nodeValue;
				
				$function_type = $file_xpath->evaluate("type", $file_members->item($member))->item(0)->nodeValue;
				$function_brief_description = trim($file_xpath->evaluate("briefdescription", $file_members->item($member))->item(0)->nodeValue);
				$function_type = str_replace(array(" *", " &"), array("*", "&"), $function_type);

				//Check all function parameters
				$function_parameters = $file_xpath->evaluate("param", $file_members->item($member));

				$parameters = array();

				for($function_parameter=0; $function_parameter<$function_parameters->length; $function_parameter++)
				{
					$parameters[$function_parameter] = array();
					
					$parameters[$function_parameter]["name"] = "param" . $function_parameter;
					
					if(is_object($file_xpath->evaluate("declname", $function_parameters->item($function_parameter))->item(0)))
					{
						$parameters[$function_parameter]["name"] = $file_xpath->evaluate("declname", $function_parameters->item($function_parameter))->item(0)->nodeValue;
					}
					else
					{
						if($this->verbose)
							print t("Skipping:") . " " . t("function") . " '" . $function_name . "' " . t("seems to be a macro with undocumented parameter types.") . "\n";
						
						continue;
					}
					
					$parameters[$function_parameter]["type"] = str_replace(array(" *", " &"), array("*", "&"), $file_xpath->evaluate("type", $function_parameters->item($function_parameter))->item(0)->nodeValue);

					//Check if parameter is array
					if($file_xpath->evaluate("array", $function_parameters->item($function_parameter))->length > 0)
					{
						$array_value = $file_xpath->evaluate("array", $function_parameters->item($function_parameter))->item(0)->nodeValue;

						if($array_value == "[]")
						{
							$parameters[$function_parameter]["is_array"] = true;
						}
						else
						{
							$parameters[$function_parameter]["extra"] = $array_value;
						}	
					}

					if($file_xpath->evaluate("defval", $function_parameters->item($function_parameter))->length > 0)
					{
						$parameters[$function_parameter]["value"] = $file_xpath->evaluate("defval", $function_parameters->item($function_parameter))->item(0)->nodeValue;
					}
				}

				if(count($parameters) > 0)
				{
					$this->functions[$name][$namespace][$function_name][] = array(
						"return_type"=>$function_type, 
						"brief_description"=>$function_brief_description,
						"parameters"=>$parameters
					);
				}
				else
				{
					$this->functions[$name][$namespace][$function_name][] = array(
						"return_type"=>$function_type, 
						"brief_description"=>$function_brief_description
					);
				}
			}
			
			// Remove files without functions
			if(count($this->functions[$name]) <= 0)
				unset($this->functions[$name]);
		}
	}
}

?>
